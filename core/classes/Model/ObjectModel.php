<?php

abstract class ObjectModel
{

    public $add = true;
    public $view = false;
    public $edit = true;
    public $live_edit = false;
    public $delete = true;
    public $exportable = true;
    public $active_filters = true;

    public $link_add = 'add';
    public $link_add_params = '';

    public $link_view = 'view';
    public $link_view_params = '';

    public $link_edit = 'edit';
    public $link_edit_params = '';

    public $fields_lang = []; //Tableau des index qui sont soumis à des traductions
    public $fields_required = []; //Tableau des index qui sont obligatoires
    public $fields_validate = []; //Tableau des indes qui doivent être véfifiés
    public $fields_join = [];
    public $admin_tab = [];

    public $json_fields = [];
    public $where = false;
    public $order_by = false;
    public $order_way = 'ASC';
    public $date_add;
    /**
     * @return mixed
     */
    public function getDateAdd()
    {
        return $this->date_add;
    }

    /**
     * @param mixed $date_add
     */
    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

//Limites du get_list (Pour les tableau trop long à charger en JS)
    public $get_list_count = 0;
    public $get_list_limit_deb = 0;
    public $get_list_limit_end = 300;
    public $get_list_max_result = 300;
    public $get_list_fields = [];
    public $get_list_limit_force = 0;
    public $current_id_lang = false;
    public $id;

    /**
     * Construit autommatiqurment l'objet
     *
     * @param integer $id : Si l'id est renseigné, on charge les informations
     * @param integer $id_lang Required if object is multilingual (optional)
     */
    public function __construct($id = NULL, $id_lang = NULL)
    {

        /* Connect to database and check SQL table/identifier */
        if (!Validate::isTableOrIdentifier($this->identifier) || !Validate::isTableOrIdentifier($this->table)) {
            throw new Exception(Tools::displayError());
        }
        $this->identifier = Tools::pSQL($this->identifier);

        if ($id_lang) {
            $this->current_id_lang = $id_lang;
        }

        /* Charge les informations depuis la base de données */
        if ($id) {

            $sql = 'SELECT a.* ' . ($id_lang && $this->fields_lang ? ',b.*' : '');
            foreach ($this->fields_join as $j) {
                $key_name = $j['key'];
                foreach ($j['fields'] as $jfield) {
                    $sql .= ',' . $key_name . '.' . $jfield . ' as ' . $key_name . '_' . $jfield;

                }
            }

            $sql .= ' FROM `' . _DB_PREFIX_ . $this->table . '` a ' . ($id_lang && $this->fields_lang ? (' JOIN `' . Tools::pSQL(_DB_PREFIX_ . $this->table) . '_lang` b ON (a.`' . $this->identifier . '` = b.`' . $this->identifier) . '` AND `id_lang` = ' . intval($id_lang) . ')' : '');


            if ($this->fields_join) {
                foreach ($this->fields_join as $j) {
                    $key_name = $j['key'];
                    $sql .= '  JOIN ' . _DB_PREFIX_ . $j['table'] . ' ' . $key_name .
                        ' ON ' . 'a' . '.' . $j['onleft'] . ' = ' . $key_name . '.' . $j['onright'];
                    if (isset($j['lang']) &&  $j['lang'] === true)
                    {
                        $sql .= ' AND ' . $j['onleft']. '.id_lang='. _ID_LANG_;

                    }
                    if (isset($j['andwhere']) && !empty($j['andwhere']))
                    {
                        $sql .=  ' AND (' . $j['andwhere'] . ' ) ';
                    }
                }
            }
            $sql .= ' WHERE a.`' . $this->identifier . '` = ' . intval($id);
            $result = Db::getInstance()->getRow($sql);
            if (!$result) {
                return false;
            }
            $this->id = intval($id);
            foreach ($result AS $key => $value) {


                if (key_exists($key, $this)) {
                    //   echo "kkk";
                    //     d($key);
                    $this->{$key} = stripslashes($value);
                }
            }
            /* Si l'id de la langue n'est pas renseigné, on charger les information dans des tableau avec toute les langues. */
            if (!$id_lang) {
                $sql = 'SELECT * FROM `' . Tools::pSQL(_DB_PREFIX_ . $this->table) . '_lang` WHERE `' . $this->identifier . '` = ' . intval($id);


                $result = Db::getInstance()->ExecuteS($sql);
                if ($result) {
                    foreach ($result as $row) foreach ($row AS $key => $value)
                        if (key_exists($key, $this) && $key != $this->identifier) {
                            $this->{$key}[$row['id_lang']] = stripslashes($value);
                        }
                }
            }
        }

    }

    static public function deleteRow($table, $identifier, $value)
    {
        if (!$table || !$identifier || !$value) {
            return;
        }
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . $identifier . '` = ' . $value;
        return Db::getInstance()->Execute($sql);
    }

    static public function getMultipleInfo($table, $where_row, $where_value, $row, $memcached = false)
    {


        $sql = 'SELECT ' . implode(',',
                $row) . ' FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . $where_row . '` = \'' . $where_value . '\'';

        return Db::getInstance()->getRow($sql, $memcached);
    }

    static public function getSingleInfo($table, $where_row, $where_value, $row, $memcached = false)
    {
        $sql = 'SELECT `' . $row . '` FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . $where_row . '` = \'' . $where_value . '\'';
        return Db::getInstance()->getValue($sql, $memcached);
    }

    static public function toObject($array)
    {
        $name = get_called_class();
        $objet = new $name();
        foreach ($array as $key => $value) {
            $objet->$key = $value;
        }

        return $objet;
    }

    /*
    $table = varchar qui définit la table dans laquelle insérer l'objet
    $fields = array avec en key le nom des champs de la table + value
    //*/

    static public function getSingleInfoLang($table, $where_row, $where_value, $row, $id_lang = false,
                                             $memcached = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('_ID_LANG_DEFAULT_');
        }



        $sql = 'SELECT `' . $row . '`
FROM `' . _DB_PREFIX_ . $table . '`
WHERE `' . $where_row . '` = \'' . $where_value . '\'
AND `id_lang` = ' . intval($id_lang);
        return Db::getInstance()->getValue($sql, $memcached);
    }

//Mise à jour d'un objet

    static public function staticGetUrl($id_object)
    {

    }

    static public function deleteInfos($table, $where_row, $where_value)
    {
        return Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . $where_row . '` = \'' . $where_value . '\'');
    }

    static public function selectLast($table)
    {
        $sql = 'SELECT * FROM `' . $table . '` ORDER BY `date_add` DESC';
        return Db::getInstance()->getRow($sql);
    }

    public function Exist()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . $this->table . ' ` WHERE `' . $this->identifier . '` = ' . $this->id;
        return Db::getInstance()->ExecuteS($sql);
    }

    /*
    Supprime un objet instancié. Dans cette fonction global, on supprimer uniquement la ligne dans la table associée a l'objet.
    Si on veut effectuer d'autres action lors de la suppression d'un objet,
    utiliser la fonction delete dans ka classe choisie.
    exemple :
    public function delete()
    {
    //Supprime les images par exemple
    //Supprimer ensuite d'autres champs dans la table
    // et enfin supprime l'objet en lui même
    parent::delete();
    }
    //*/

    public function save()
    {
//Si l'objet à déjà un identifiant, on met à jour au lieu d'ajouter
        if ($this->id) {
            return $this->update();
        } else {
            return $this->add();
        }
    }

//Supprime un champs dans une table.
    public function update()
    {
        if (!defined('_ID_LANG_')) {
            define('_ID_LANG_', Configuration::get('_ID_LANG_DEFAULT_'));
        }
        //Si l'objet n'a pas d'identifiant, on l'ajoute au lieu de le mettre à jour
        if (!$this->id) {
            return $this->add();
        }

        //Valeurs par défaut
        $defaultValues = ['date_upd' => date('Y-m-d H:i:s'), 'is_new' => 0];

        if (!$this->validateObject()) {
            return false;
        }

        $params = []; //Tableau pour enregistrer les parametres.

        $columns = Db::getInstance()->getMysqlColumns(_DB_PREFIX_ . $this->table); //Recupère le nom de champs de la table

        //Enregistre les defaultValue
        foreach ($defaultValues as $key => $default_value) {
            if (array_key_exists($key, $this)) {
                $params[$key] = $defaultValues[$key];
                $this->{$key} = $defaultValues[$key];
            }
        }
        // die( Tools::debug($params, true));
        //Ajoute la valeur au params si l'index exist en tant que champs de la table et qu'il n'est pas dans la table des langues
        foreach ($this as $key => $value)
            if (in_array($key, $columns) && !in_array($key, $this->fields_lang) && !array_key_exists($key, $defaultValues)) {
                $params[$key] = $value;
            }

        //Enregistre les link_rewrite si il existe
        if (array_key_exists('link_rewrite', $this)) {
            if (empty($this->link_rewrite)) {
                $this->link_rewrite = Tools::link_rewrite($this->name);
            } else {
                $this->link_rewrite = Tools::link_rewrite($this->link_rewrite);
            }
        }
        //Récupère les champs de la langue.
        foreach ($this->fields_lang as $field_lang) $params_lang[$field_lang] = trim($this->{$field_lang});

        $result_lang = true;
        if (sizeof($this->fields_lang) && sizeof($params_lang)) {
            $result_lang = Db::getInstance()->AutoExecute(_DB_PREFIX_ . $this->table . '_lang', $params_lang, 'UPDATE', '`' . $this->identifier . '`=' . $this->id . ' AND id_lang = ' . ($this->current_id_lang ? $this->current_id_lang : _ID_LANG_));
        }
        $result = Db::getInstance()->AutoExecute(_DB_PREFIX_ . $this->table, $params, 'UPDATE', '`' . $this->identifier . '`=' . $this->id);

        if ($result && $result_lang) {
            return true;
        } else {
            return false;
        }
    }


    public function setFromArray($tab)
    {

        foreach ($tab as $key => $value) {
            $this->$key = $value;
        }

    }

    public function add()
    {
//Si l'objet à déjà un identifiant, on met à jour au lieu d'ajouter
        if ($this->id) {
            return $this->update();
        }

        $DB = Db::getInstance();
//Valeurs par défaut
        $defaultValues = ['date_upd' => date('Y-m-d H:i:s'), 'date_add' => date('Y-m-d H:i:s'), 'is_new' => 1];


        if (!$this->validateObject()) {
            return false;
        }

        $params = []; //Tableau pour enregistrer les parametres.

        $columns = $DB->getMysqlColumns(_DB_PREFIX_ . $this->table); //Recupère le nom de champs de la table

        foreach ($columns as $key => $value) {
            if (array_key_exists($value, $defaultValues)) {
                $params[$value] = $defaultValues[$value];
            }
        }


//Enregistre les defaultValue
        foreach ($defaultValues as $key => $default_value) {
            if (array_key_exists($key, $this)) {
                $params[$key] = $defaultValues[$key];
                $this->{$key} = $defaultValues[$key];
            }
        }


//Ajoute la valeur au params si l'index exist en tant que champs de la table.
        foreach ($this as $key => $value)

            if (in_array($key, $columns) && !in_array($key, $this->fields_lang) && !array_key_exists($key,
                    $defaultValues)
            ) {
                $params[$key] = $value;
            }
        if ($result = $DB->AutoExecute(_DB_PREFIX_ . $this->table, $params, 'INSERT')) {
            $this->id = $DB->Insert_ID();


//Construit le tableau des valeurs à insérer dans la base de données.
            foreach ($this->fields_lang as $field_lang) {

                $params_lang[$field_lang] = ($this->{$field_lang});

            }

//Si le tableau fields_lang existe
            if (sizeof($this->fields_lang) && sizeof($params_lang)) {

//Récupère les language disponible. Par défaut enregistre donc les valeurs pour toute les langues existante. (Evite de devoir checker lors des updates)
                $languages = Language::getLanguages();


                foreach ($languages as $lang) {
                    $params_lang_s = [];
                    foreach ($this->fields_lang as $pl) {

                        if (is_array($this->$pl)) {
                            $params_lang_s[$pl] = $this->$pl[$lang['id_lang']];
                        } else {
                            $params_lang_s[$pl] = $this->$pl;
                        }
                    }
                    $params_lang_s['id_lang'] = $lang['id_lang'];
                    $params_lang_s[$this->identifier] = $this->id;


                    $result = $DB->AutoExecute(_DB_PREFIX_ . $this->table . '_lang', $params_lang_s, 'INSERT');

                }
            }
            return true;
        } else {

            return false;
        }
    }


//Fonction pour afficher un tableau d'objet.

    public function validateObject($die = false, $return_error = false, $display_error = true, $return_errors = false)
    {
//Check les champs obligatoires

        $errors = [];

        foreach ($this->fields_required as $field_required) {
            if (!array_key_exists($field_required, $this)) {
                if ($return_errors) {
                    array_push($errors, [
                        'error' => 'required field',
                        'field' => $field_required
                    ]);
                } else if ($return_error) {
                    return ['error' => (bool)true,
                        'error_detail' => 'Le champs "' . $field_required . '" n\'est pas renseigné'];
                } else {
//Tools::displayError ('Le champs "' . $field_required . '" n\'est pas renseigné' , $die);
                    return false;
                }
            } elseif (empty($this->{$field_required})) {
                if ($return_errors) {
                    array_push($errors, [
                        'error' => 'empty field',
                        'field' => $field_required
                    ]);
                } else if ($return_error) {
                    return ['error' => (bool)true,
                        'error_detail' => 'Le champs "' . $field_required . '" n\'est pas renseigné'];
                } else if ($display_error === true) {
//Tools::displayError ('Le champs "' . $field_required . '" n\'est pas renseigné' , $die);
                    return false;
                }
            }
        }
//Validation des champs
        foreach ($this->fields_validate as $row => $validate_function) {
            if (!$this->{$row}) {
                continue;
            }

            if (!method_exists('Validate', $validate_function)) {
                Tools::displayError('Fonction de validation "' . $validate_function . '" incconue', $die);
                return false;
            } elseif (!call_user_func(['Validate', $validate_function], $this->{$row})) {

                Tools::displayError('le champs "' . $row . '" ne répond pas à la fonction de validation "' . $validate_function . '"',
                    $die);
                return false;
            }
        }
        if ($return_error) {
            return ['error' => (bool)false, 'error_detail' => ''];
        }
        if ($return_errors && (count($errors) > 0)) {
            return $errors;
        }
        return true;
    }

    public function copy_from_post()
    {
        $_POST = array_merge($_POST, $_GET);
        foreach ($_POST as $params => $value) {
            if (is_array($value)) {
                continue;
            }

            if (array_key_exists($params, $this)) {
                $this->{$params} = trim($value);
            }
            if ($this->identifier == $params) {
                $this->id = intval($value);
            }
        }
    }

//Compte les champs récupérés

    public function copy_from_array($array, $excludes_keys = [])
    {
// $_POST = array_merge($_POST, $_GET);
        if (is_array($array)) {
            foreach ($array as $params => $value)
                if (array_key_exists($params, $this) && !in_array($params, $excludes_keys)) {
                    $this->{$params} = $value;
                }
        }
    }

//Affiche les filtres

    public function delete()
    {
        if (array_key_exists('deleted',
            $this)) // Si l'objet à la propriété delete, on fait juste un update. (Pour les clients par exemple ou les commandes)
        {
            $this->deleted = 1;
            return ($this->update());
        } else {
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . $this->table . '` WHERE `' . $this->identifier . '` = ' . $this->id;

            $isDeleted = Db::getInstance()->Execute($sql);
            if ($this->fields_lang) {
                $isDeleted = Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . $this->table . '_lang` WHERE `' . $this->identifier . '` = ' . $this->id);
            }
            return $isDeleted;
        }
    }



//Affiche les filtres

    public function get_list($id_lang = false, $forselect = false, $field_forselect_name = 'name',
                             $title_forselect_name = false, $separator = ' ', $limits = true, $memcached = false)
    {
//Si la lang n'est pas renseigné on affiche la lang par défaut chargé dans le init ( Configuration::loadConfig )
        if (!$id_lang) {
            $context = Context::getContext();

            $id_lang = $context->getCurrentLanguage()->id_lang;

        }
        $fields_lang = false;
        if (array_key_exists('fields_lang', $this)) {
            $fields_lang = count($this->fields_lang);
        }

//Selectionne tous par défaut dans la table de la classe
        $sql = 'SELECT ' . _DB_PREFIX_ . $this->table . '.* ';

//Si le tableau field_lang de la class n'est pas vide on ajoute tous les champs de la lang
        if ($fields_lang) {
            $sql .= ', ' . _DB_PREFIX_ . $this->table . '_lang.' . implode(', ' . _DB_PREFIX_ . $this->table . '_lang.',
                    $this->fields_lang);
        }
        foreach ($this->fields_join as $j) {
            $key_name = $j['key'];
            if(isset($j['fields']) && !empty($j['fields'])) {
                foreach ($j['fields'] as $jfield) {
                    $sql .= ',' . $key_name . '.' . $jfield . ' as ' . $key_name . '_' . $jfield;

                }
            }
        }
        $sql .= ' FROM `' . _DB_PREFIX_ . $this->table . '` ';

//Si le tableau field_lang de la class n'est pas vide on ajoute les left join
        if ($fields_lang) {
            $sql .= '  JOIN `' . _DB_PREFIX_ . $this->table . '_lang`
ON ' . _DB_PREFIX_ . $this->table . '.' . $this->identifier . ' = ' . _DB_PREFIX_ . $this->table . '_lang.' . $this->identifier . ' AND  
 ' . _DB_PREFIX_ . $this->table . '_lang.id_lang = ' . $id_lang;
        }


        if ($this->fields_join) {
            foreach ($this->fields_join as $j) {
                $key_name = $j['key'];
                $sql .= '  JOIN ' . _DB_PREFIX_ . $j['table'] . ' ' . $key_name .
                    ' ON ' . _DB_PREFIX_ . $this->table . '.' . $j['onleft'] . ' = ' . $key_name . '.' . $j['onright'];
                if (isset($j['andwhere']) && !empty($j['andwhere']))
                {
                    $sql .=  ' AND (' . $j['andwhere'] . ' ) ';
                }
            }
        }
        $sql .= ' WHERE 1 = 1 ';


//Si l'objet à la propriété deleted, récupère uniquement ceux qui sont a deleted 0
        if (array_key_exists('deleted', $this)) {
            $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.deleted = 0';
        }

        if ($this->where && is_array($this->where)) {
            foreach ($this->where as $where) $sql .= ' AND ' . $where;
        }

        $sql .= $this->get_list_filters();
        $sql .= $this->get_list_search_filters();

        if (array_key_exists('position', $this)) {
            $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.position ' . $this->order_way;
        } elseif ($this->order_by) {
            $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.' . $this->order_by . ' ' . $this->order_way;
        } elseif (!$this->order_by && $this->order_way) {
            $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.' . $this->identifier . ' ' . $this->order_way;
        }
        if ($limits) {
            if ($this->get_list_limit_force) {
                $sql .= ' LIMIT ' . intval($this->get_list_limit_deb) . ',' . intval($this->get_list_limit_end);
            } else {
                $sql .= ' LIMIT ' . intval($this->get_list_limit_deb) . ',' . intval($this->get_list_max_result);
            }
        }
   //  echo get_class($this).'->get_list() (Via ObjectModel) <br/>--------------------------<br/>'.$sql.'<br/>--------------------------<br/>';
        $results = Db::getInstance()->ExecuteS($sql, $array = true, $memcached);
        if (!$results) {
            return [];
        }

        if ($forselect) {
            return $this->forselect($results, $field_forselect_name, $title_forselect_name, $separator);
        }
        return $results;
    }

    public function get_list_count($id_lang = false, $forselect = false, $field_forselect_name = 'name',
                                   $title_forselect_name = false, $separator = ' ', $limits = true, $memcached = false)
    {
//Si la lang n'est pas renseigné on affiche la lang par défaut chargé dans le init ( Configuration::loadConfig )
        if (!$id_lang) {
            $context = Context::getContext();

            $id_lang = $context->getCurrentLanguage()->id_lang;

        }
        $fields_lang = false;
        if (array_key_exists('fields_lang', $this)) {
            $fields_lang = count($this->fields_lang);
        }

//Selectionne tous par défaut dans la table de la classe
        $sql = 'SELECT  count(*)  as nbr';

//Si le tableau field_lang de la class n'est pas vide on ajoute tous les champs de la lang

        $sql .= ' FROM `' . _DB_PREFIX_ . $this->table . '` ';

//Si le tableau field_lang de la class n'est pas vide on ajoute les left join
        if ($fields_lang) {
            $sql .= '  JOIN `' . _DB_PREFIX_ . $this->table . '_lang`
ON ' . _DB_PREFIX_ . $this->table . '.' . $this->identifier . ' = ' . _DB_PREFIX_ . $this->table . '_lang.' . $this->identifier . ' AND  
 ' . _DB_PREFIX_ . $this->table . '_lang.id_lang = ' . $id_lang;
        }


        if ($this->fields_join) {
            foreach ($this->fields_join as $j) {
                $key_name = $j['key'];
                $sql .= '  JOIN ' . _DB_PREFIX_ . $j['table'] . ' ' . $key_name .
                    ' ON ' . _DB_PREFIX_ . $this->table . '.' . $j['onleft'] . ' = ' . $key_name . '.' . $j['onright'];
               if (isset($j['lang']) && $j['lang'] === true)
               {
                  $sql .= ' AND ' . $j['onleft']. '.id_lang='. _ID_LANG_;

               }
                if (isset($j['andwhere']) &&  !empty($j['andwhere']))
                {
                    $sql .=  ' AND (' . $j['andwhere'] . ' ) ';
                }
            }
        }
        $sql .= ' WHERE 1 = 1 ';


//Si l'objet à la propriété deleted, récupère uniquement ceux qui sont a deleted 0
        if (array_key_exists('deleted', $this)) {
            $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.deleted = 0';
        }

        if ($this->where && is_array($this->where)) {
            foreach ($this->where as $where) $sql .= ' AND ' . $where;
        }

        $sql .= $this->get_list_filters();
        $sql .= $this->get_list_search_filters();

        if (array_key_exists('position', $this)) {
            $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.position ' . $this->order_way;
        } elseif ($this->order_by) {
            $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.' . $this->order_by . ' ' . $this->order_way;
        } elseif (!$this->order_by && $this->order_way) {
            $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.' . $this->identifier . ' ' . $this->order_way;
        }

       //  echo get_class($this).'->get_list() (Via ObjectModel) <br/>--------------------------<br/>'.$sql.'<br/>--------------------------<br/>';
        $results = Db::getInstance()->getValue($sql, $array = true, $memcached);

        if (!$results) {
            return [];
        }

        if ($forselect) {
            return $this->forselect($results, $field_forselect_name, $title_forselect_name, $separator);
        }

        return $results;
    }

// Ajoute des conditions SQL si le champs recherche est renseigné

    function get_list_filters()
    {
        $sql = '';
//Filtres
        if ($this->get_list_limit_force == 0) {
            $class_name = get_class($this);
            if (Tools::isSubmit('filtrer_' . $class_name) && $filters = Tools::getValue('filter')) {
// Tools::debug($filters, true);
                if (is_array($filters) && array_key_exists($class_name, $filters)) {
//Date de début et de fin
                    $date_from = (array_key_exists('date_from',
                        $filters[$class_name]) ? $filters[$class_name]['date_from'] : false);
                    $date_to = (array_key_exists('date_to',
                        $filters[$class_name]) ? $filters[$class_name]['date_to'] : false);
                    if ($date_from && $date_to) { //Entre x et y
                        $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.date_add BETWEEN \'' . $date_from . ' 00:00:01\' AND \'' . $date_to . ' 23:59:59\'';
                    } elseif ($date_from && !$date_to) { //Depuis x
                        $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.date_add BETWEEN \'' . $date_from . ' 00:00:01\' AND \'' . date('Y-m-d H:i:s') . '\'';
                    } elseif (!$date_from && $date_to) { //Avant x
                        $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.date_add < \'' . $date_to . ' 23:59:59\'';
                    }

//Limittes MYSQL
                    if (isset($filters[$class_name]['limits'])) {
                        $limits = explode('-', $filters[$class_name]['limits']);
                        $this->get_list_limit_deb = $limits[0];
                        $this->get_list_limit_end = $limits[1];
                    }
                }
            } else {
                $this->get_list_limit_deb = 0;
                $this->get_list_limit_end = $this->get_list_max_result;
            }
        }
        return $sql;
    }


//Récupère les infos parrallèles.

    function get_list_search_filters()
    {
        $class_name = get_class($this);
        $sql = '';
//Filtre par mots clés
        $search_filters = Tools::getValue('search_filter_' . $class_name);
        $search_filters_column = Tools::getValue('search_filters_column_' . $class_name);
        if (Tools::isSubmit('search_' . $class_name) && $search_filters && $search_filters_column) {
            $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.' . $search_filters_column . ' LIKE \'%' . $search_filters . '%\'';
        }
        return $sql;
    }


//Formate un tableau pour pouvoir directement le passer un paramtère dans la method Form::addSelectBox
// Le tableau de sorti est sous la forme : array( id_object => value, id_object => value )

    public function forselect($fields, $field_value_name = 'name', $title = false, $separator = ' ')
    {
// Tools::debug($fields, true);
        if (!is_array($fields)) {
            return false;
        }
        $return = [];

//Si le titre est renseigné, on l'ajoute avec l'index 0.
        if ($title) {
            $return[0] = $title;
        }

        foreach ($fields as $field) {
//Si field_value_name est un tableu, on concat les infos.
            if (is_array($field_value_name)) {
                $return[$field[$this->identifier]] = [];
                foreach ($field_value_name as $field_name)
                    $return[$field[$this->identifier]] [] = $field[$field_name];
                $return[$field[$this->identifier]] = implode($separator, $return[$field[$this->identifier]]);
            } else {
                $return[$field[$this->identifier]] = $field[$field_value_name];
            }
        }
        return $return;
    }



//Récupère la position à attribuer à un nouvel object

    public function count_fields()
    {
//Selectionne tous par défaut dans la table de la classe
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $this->table . '` WHERE 1 = 1 ';
//Si l'objet à la propriété deleted, récupère uniquement ceux qui sont a deleted 0
        if (array_key_exists('deleted', $this)) {
            $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.deleted = 0';
        }
        if ($this->where && is_array($this->where)) {
            foreach ($this->where as $where) $sql .= ' AND ' . $where;
        }
        $sql .= $this->get_list_filters();

        return Db::getInstance()->getValue($sql);
    }

//Récupère une valeur dans une table précise.

    public function display_filters()
    {
        $table = '';

        $columns = Db::getInstance()->getMysqlColumns(_DB_PREFIX_ . $this->table); //Recupère le nom de champs de la table
        $class_name = get_class($this);
        $date_from = '';
        $date_to = '';
        $limits = '0-' . $this->get_list_max_result;

        if ($this->active_filters) {
            if (Tools::isSubmit('filtrer_' . $class_name) && $filters = Tools::getValue('filter')) {
                if (is_array($filters) && array_key_exists($class_name, $filters)) {
                    $date_from = (array_key_exists('date_from',
                        $filters[$class_name]) ? $filters[$class_name]['date_from'] : '');
                    $date_to = (array_key_exists('date_to',
                        $filters[$class_name]) ? $filters[$class_name]['date_to'] : '');
                    $limits = (array_key_exists('limits',
                        $filters[$class_name]) ? $filters[$class_name]['limits'] : '');
                }
            }

            $table .= '
<div>
    <div class="right">
        <form method="post"  stlye="margin:10px 10px 0 10px">';

            //Si le nombre de résultats est top grand pour tout afficher en JS
            // echo $this->get_list_count;
            if ($this->get_list_count > $this->get_list_max_result) {
                $table .= 'Nombre de résultats <select name="filter[' . $class_name . '][limits]" style="width:150px">';
                for ($i = 0; $i < ($this->get_list_count / $this->get_list_max_result); $i++) {
                    $limit_deb = $i * $this->get_list_max_result;
                    $limi_end = $i * $this->get_list_max_result + $this->get_list_max_result;

                    $table .= '<option value="' . $limit_deb . '-' . $limi_end . '" ' . ($limits == $limit_deb . '-' . $limi_end ? 'selected="selected"' : '') . '>' . $limit_deb . ' à ' . $limi_end . '</option>';
                }

                $table .= '</select>&nbsp;&nbsp;&nbsp;';
            }
            if (in_array('date_add', $columns)) {
                $table .= '
            Date de Début 	<input type="date" name="filter[' . $class_name . '][date_from]" value="' . $date_from . '"/>
            &nbsp;&nbsp;&nbsp;
            Date de Fin  	<input type="date" name="filter[' . $class_name . '][date_to]" value="' . $date_to . '"/>';
            }

            $table .= '
            <button class="button button-gray ui-button-default" type="submit" name="filtrer_' . $class_name . '"><span class="accept"></span>Filtrer</button>
        </form>
    </div>
    <div class="clear" style="margin:10px 0"></div>
</div>';
            $table .= '<div class="clear" style="margin:10px 0"></div>';
        }

        return $table;
    }

//Récupère une valeur dans une table précise.

    public function display_search_filters()
    {
        $table = '';
        $class_name = get_class($this);
        $selected_colomn = Tools::getValue('search_filters_column_' . $class_name);
        $columns = Db::getInstance()->getMysqlColumns(_DB_PREFIX_ . $this->table); //Recupère le nom de champs de la table
        if ($this->fields_lang) {
            $columns = array_merge($columns, Db::getInstance()->getMysqlColumns(_DB_PREFIX_ . $this->table . '_lang'));
        }

        $table .= '
<div>
    <div class="right">
        <form method="post"  stlye="margin:10px 10px 0 10px">';

        $table .= ' Rechercher <input type="text" name="search_filter_' . $class_name . '" value="' . Tools::getValue('search_filter_' . $class_name) . '" />&nbsp;&nbsp;&nbsp;';

        $table .= 'Champs de recherche <select name="search_filters_column_' . $class_name . '">
                <option value="0">-- Selectionnez --</option>';
        foreach ($columns as $column) {
            $table .= '<option value="' . $column . '" ' . ($selected_colomn == $column ? 'selected="selected"' : '') . '>' . $column . '</option>';
        }
        $table .= '</select>&nbsp;&nbsp;&nbsp;';

        $table .= '
            <button class="button button-gray ui-button-default" type="submit" name="search_' . $class_name . '"><span class="accept"></span>Rechercher</button>
        </form>
    </div>
    <div class="clear" style="margin:10px 0"></div>
</div>';
        $table .= '<div class="clear" style="margin:10px 0"></div>';


        return $table;
    }

//Récupère une valeur dans une table précise.

    public function get_joins_infos($table = false, $identifier = false, $order_by = false, $order_type = false)
    {
        if ($table) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . ($identifier ? $identifier : $this->identifier) . '` = ' . intval($this->id);
            $sql .= ' ORDER BY `' . ($order_by ? $order_by : ($identifier ? $identifier : $this->identifier)) . '` ' . ($order_type ? $order_type : 'ASC');
            $this->{$table} = Db::getInstance()->ExecuteS($sql);
        } else {
            if (!array_key_exists('joins_tables', $this)) {
                return;
            }
            foreach ($this->joins_tables as $table) {
                $this->{$table} = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . ($identifier ? $identifier : $this->identifier) . '` = ' . intval($this->id));
            }
        }
    }

    public function setViewed()
    {
        if (!array_key_exists('is_new', $this)) {
            return;
        }
        return self::updateSingleInfo($table = $this->table, $row = 'is_new', $new_value = 0,
            $where_row = $this->identifier, $where_value = $this->id);
    }

    static public function updateSingleInfo($table, $row, $new_value, $where_row, $where_value)
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . $table . '` SET `' . $row . '` = \'' . $new_value . '\'
WHERE `' . $where_row . '` = \'' . $where_value . '\'';
        return Db::getInstance()->Execute($sql);
    }

//Delete en masse à partir d'UN champs

    public function getHighestPosition()
    {
        if (!array_key_exists('position', $this)) {
            return;
        }

        $highestPosition = Db::getInstance()->getValue('
SELECT `position` FROM `' . _DB_PREFIX_ . $this->table . '`
ORDER BY `position` DESC');
        if (!$highestPosition) {
            $this->position = 0;
        } else {
            $this->position = $highestPosition + 1;
        }
    }

    public function active()
    {

    }

    public function duplicate()
    {
        $clone = clone $this;
        $clone->id = false;
        return $clone;
    }

    public function getApiModel()
    {
        return get_object_vars($this);
    }

}

?>