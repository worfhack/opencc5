<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class Category extends ObjectModel
{
    public $identifier = 'id_category';
    public $table = 'category';


    public $id_category;
    public $title;
    public $date_add;
    public $url_rewrite;
    public $resume;
    public $articles ;
    public $id_parent;
    public $fields_lang = ['title', 'url_rewrite', 'resume'];

    /**
     * @return mixed
     */
    public function getIdCategory()
    {
        return $this->id_category;
    }

    /**
     * @param mixed $id_category
     */
    public function setIdCategory($id_category)
    {
        $this->id_category = $id_category;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

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

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getUrlRewrite()
    {
        return $this->url_rewrite;
    }

    /**
     * @param mixed $url_rewrite
     */
    public function setUrlRewrite($url_rewrite)
    {
        $this->url_rewrite = $url_rewrite;
    }

    /**
     * @return array
     */
    public function getFieldsLang()
    {
        return $this->fields_lang;
    }

    /**
     * @param array $fields_lang
     */
    public function setFieldsLang($fields_lang)
    {
        $this->fields_lang = $fields_lang;
    }


    /**
     * @return mixed
     */
    public function getIdAuthor()
    {
        return $this->id_author;
    }

    /**
     * @param mixed $id_author
     */
    public function setIdAuthor($id_author)
    {
        $this->id_author = $id_author;
    }

    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
    }
    public function getArticles()
    {
        return $this->articles;
    }
    public function loadArticles($limit, $page)
    {
        $this->articles = new ArticleCollection();
        $this->articles->getLastPublication(_ID_LANG_, $limit, $page, $this->getIdCategory());
         $this->articles->load();

    }


    static public function getIdByRewrite($rewrite)
    {
        return ObjectModel::getSingleInfoLang('category_lang','url_rewrite', $rewrite,  'id_category', _ID_LANG_);
    }

    static public function buildTree(array &$elements, $parentId = 0) {

        $branch = array();

        foreach ($elements as &$element) {

            if ($element['id_parent'] == $parentId) {
                $children = self::buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($element);
            }
        }
        return $branch;
    }
    static public function getTree()
    {
        $query = 'SELECT c.*, cl.*, c.id_category as id, c.id_parent as parent from ' . _DB_PREFIX_ . 'category c JOIN ' . _DB_PREFIX_ . 'category_lang cl ON 
        
        c.id_category = cl.id_category and cl.id_lang =  ' . _ID_LANG_ .' ORDER BY c.id_parent';
        $res = DB::getInstance()->ExecuteS($query);
        return self::buildTree($res);


    }

    public function add()
    {
        if (!is_array($this->url_rewrite)) {
            $this->url_rewrite = Tools::link_rewrite($this->title);
        } else {
            $this->url_rewrite = [];
            foreach ($this->title as $title) {
                $this->url_rewrite[] = Tools::link_rewrite($title);
            }
        }
        return parent::add();
    }

    public function update()
    {
        if (!is_array($this->url_rewrite)) {
            $this->url_rewrite = Tools::link_rewrite($this->title);
        } else {
            $this->url_rewrite = [];
            foreach ($this->title as $title) {
                $this->url_rewrite[] = Tools::link_rewrite($title);
            }
        }
        return parent::update();
    }


}