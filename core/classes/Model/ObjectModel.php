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
                    if (isset($j['left']) && $j['left'] === true)
                    {
                        $sql .= ' LEFT ';
                    }
                    $sql .= '  JOIN ' . _DB_PREFIX_ . $j['table'] . ' ' . $key_name .
                        ' ON ' . 'a' . '.' . $j['onleft'] . ' = ' . $key_name . '.' . $j['onright'];
                    if (isset($j['lang']) && $j['lang'] === true) {
                        $sql .= ' AND ' . $j['onleft'] . '.id_lang=' . _ID_LANG_;

                    }
                    if (isset($j['andwhere']) && !empty($j['andwhere'])) {
                        $sql .= ' AND (' . $j['andwhere'] . ' ) ';
                    }
                }
            }

            $sql .= ' WHERE a.`' . Tools::pSQL($this->identifier) . '` = ' . Tools::pSQL(intval($id));
            $result = Db::getInstance()->getRow($sql);
            if (!$result) {
                return false;
            }
            $this->id = intval($id);
            foreach ($result AS $key => $value) {


                if (key_exists($key, $this)) {
                    $this->{$key} = stripslashes($value);
                }
            }

            /* Si l'id de la langue n'est pas renseigné, on charger les information dans des tableau avec toute les langues. */
            if (!$id_lang) {
                $sql = 'SELECT * FROM `' . Tools::pSQL(_DB_PREFIX_ . $this->table) . '_lang` WHERE `' . $this->identifier . '` = ' . intval($id);


                $result = Db::getInstance()->executeS($sql);
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
        return Db::getInstance()->execute($sql);
    }


    static public function getSingleInfo($table, $where_row, $where_value, $row)
    {
        $sql = 'SELECT `' . Tools::pSQL($row) . '` FROM `' . _DB_PREFIX_ . Tools::pSQL($table) . '` WHERE `' . Tools::pSQL($where_row ). '` = \'' . Tools::pSQL($where_value) . '\'';
        return Db::getInstance()->getValue($sql);
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

    static public function getSingleInfoLang($table, $where_row, $where_value, $row, $id_lang = false)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('_ID_LANG_DEFAULT_');
        }

        $sql = 'SELECT `' . Tools::pSQL($row ). '`
FROM `' . _DB_PREFIX_ . Tools::pSQL($table ). '`
WHERE `' . Tools::pSQL($where_row) . '` = \'' . Tools::pSQL($where_value ). '\'
AND `id_lang` = ' . Tools::pSQL(intval($id_lang));
        return Db::getInstance()->getValue($sql);
    }

//Mise à jour d'un objet

    static public function staticGetUrl($id_object)
    {

    }

    static public function deleteInfos($table, $where_row, $where_value)
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . $where_row . '` = \'' . $where_value . '\'');
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
        //Ajoute la valeur au params si l'index exist en tant que champs de la table et qu'il n'est pas dans la table des langues
        foreach ($this as $key => $value)
            if (in_array($key, $columns) && !in_array($key, $this->fields_lang) && !array_key_exists($key, $defaultValues)) {
                $params[$key] = $value;
            }

        //Enregistre les link_rewrite si il existe
        if (array_key_exists('link_rewrite', $this)) {
            if (empty($this->link_rewrite)) {
                $this->link_rewrite = Tools::linkRewrite($this->name);
            } else {
                $this->link_rewrite = Tools::linkRewrite($this->link_rewrite);
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
        if ($DB->AutoExecute(_DB_PREFIX_ . $this->table, $params, 'INSERT')) {
            $this->id = $DB->insertID();


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


                    $DB->AutoExecute(_DB_PREFIX_ . $this->table . '_lang', $params_lang_s, 'INSERT');

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

    public function copyFromPost()
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

            $isDeleted = Db::getInstance()->executeS($sql);
            if ($this->fields_lang) {
                $isDeleted = Db::getInstance()->executeS('DELETE FROM `' . _DB_PREFIX_ . $this->table . '_lang` WHERE `' . $this->identifier . '` = ' . $this->id);
            }
            return $isDeleted;
        }
    }


//Affiche les filtres

    public function getList($id_lang = false, $count = false)
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
        if ($count === false) {
//Selectionne tous par défaut dans la table de la classe
            $sql = 'SELECT ' . _DB_PREFIX_ . $this->table . '.* ';

            if ($fields_lang) {

                $sql .= ', ' . _DB_PREFIX_ . $this->table . '_lang.' . implode(', ' . _DB_PREFIX_ . $this->table . '_lang.',
                        $this->fields_lang);
            }

            foreach ($this->fields_join as $j) {
                $key_name = $j['key'];
                if (isset($j['fields']) && !empty($j['fields'])) {
                    foreach ($j['fields'] as $jfield) {
                        $sql .= ',' . $key_name . '.' . $jfield . ' as ' . $key_name . '_' . $jfield;

                    }
                }
            }
        } else {
            $sql = 'SELECT count(*) ';
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
                if (isset($j['left']) && $j['left'] === true)
                {
                    $sql .= ' LEFT ';
                }
                $sql .= '  JOIN ' . _DB_PREFIX_ . $j['table'] . ' ' . $key_name .
                    ' ON ' . _DB_PREFIX_ . $this->table . '.' . $j['onleft'] . ' = ' . $key_name . '.' . $j['onright'];
                if (isset($j['andwhere']) && !empty($j['andwhere'])) {
                    $sql .= ' AND (' . $j['andwhere'] . ' ) ';
                }
            }
        }
        $sql .= ' WHERE 1 = 1 ';

        if (array_key_exists('deleted', $this)) {
            $sql .= ' AND ' . _DB_PREFIX_ . $this->table . '.deleted = 0';
        }
        if ($this->where && is_array($this->where)) {
           foreach ($this->where as $row=>$value) {


              $sql .= ' AND ' . Tools::pSQL($row) . " = " . $value;
           }
        }
        if ($count === false) {
            if (array_key_exists('position', $this)) {
                $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.position ' . $this->order_way;
            } elseif ($this->order_by) {
                $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.' . $this->order_by . ' ' . $this->order_way;
            } elseif (!$this->order_by && $this->order_way) {
                $sql .= ' ORDER BY `' . _DB_PREFIX_ . $this->table . '`.' . $this->identifier . ' ' . $this->order_way;
            }
            if ($this->get_list_limit_force) {
                $sql .= ' LIMIT ' . Tools::pSQL(intval($this->get_list_limit_deb)) . ',' . Tools::pSQL(intval($this->get_list_limit_end));
            }
        }
        if ($count === false) {
            $results = Db::getInstance()->executeS($sql, true);

            if (!$results) {
                return [];
            }
            return $results;
        }else{

            return  Db::getInstance()->getValue($sql,  true);
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


}
