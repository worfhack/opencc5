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

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }


    public function __construct($id)
    {
        parent::__construct($id);
        $this->author = new Author($this->id_author);
    }

    /**
     * @return mixed
     */
    public function getIdArticle()
    {
        return $this->id_article;
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