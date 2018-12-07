<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class Article extends ObjectModel
{
    public $identifier = 'id_article';
    public $table = 'article';


    public $id_article;
    public $title;
    public $date_add;
    public $content;
    public $name;
    public $id_author;
    public $resume;


    public $author_lastname;
    public $author_firstname;

    public $fields_join = [
        [
            'table'=>'administrator',
            'key'=>'author',
            'onleft'=>'id_author',
            'onright'=>'id_administrator',
            'fields'=>['firstname', 'lastname']]
    ];

    public $author_name;

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

    public $fields_lang = ['title', 'name', 'content', 'resume'];
    /**
     * @return mixed
     */
    public function getAuthorLastName()
    {
        return $this->author_lastname;
    }
    public function getAuthorFirstName()
    {
        return $this->author_firstname;
    }

    public function getPostLink()
    {
        return Link::getPageLink($this->getName() . '-' .$this->id_article);
    }
    /**
     * @return mixed
     */
    public function getIdArticle()
    {
        return $this->id_article;
    }
public function add()
{
    if (!is_array($this->name)) {
        $this->name = Tools::link_rewrite($this->title);
    }else
    {
        $this->name = [];
        foreach ($this->title as $title)
        {
            $this->name[] =  Tools::link_rewrite($title);
        }
    }
    return parent::add();
}

    public function update()
    {
        if (!is_array($this->name)) {
            $this->name = Tools::link_rewrite($this->title);
        }else
        {
            $this->name = [];
            foreach ($this->title as $title)
            {
                $this->name[] =  Tools::link_rewrite($title);
            }
        }
        return parent::update();
    }
    /**
     * @param mixed $id_article
     */
    public function setIdArticle($id_article)
    {
        $this->id_article = $id_article;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}