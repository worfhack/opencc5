<?php
/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 15/12/2018
 * Time: 10:23
 */

class Comment extends ObjectModel
{
    public $identifier = 'id_comment';
    public $table = 'comment';

    public $id_comment;
    public $date_add;
    public $id_article;
    public $id_user;
    public $message;
    public $publish;
    public $fields_join = [
        [
            'table'=>'users',
            'key'=>'user',
            'onleft'=>'id_user',
            'onright'=>'id_user',
            'fields'=>['firstname', 'lastname'],


        ]
    ];
    public $user_lastname;
    public $article_id_article;
    public $article_title;

    /**
     * @return mixed
     */
    public function getArticleTitle()
    {
        return $this->article_title;
    }

    /**
     * @param mixed $article_title
     */
    public function setArticleTitle($article_title)
    {
        $this->article_title = $article_title;
    }
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getArticleIdArticle()
    {
        return $this->article_id_article;
    }

    /**
     * @param mixed $article_id
     */
    public function setArticleIdArticle($article_id)
    {
        $this->article_id_article = $article_id;
    }

    /**
     * @return mixed
     */
    public function getArticleTile()
    {
        return $this->article_tile;
    }

    /**
     * @param mixed $article_tile
     */
    public function setArticleTile($article_tile)
    {
        $this->article_tile = $article_tile;
    }
    public $article_tile;

    /**
     * @return mixed
     */
    public function getUserLastname()
    {
        return $this->user_lastname;
    }

    /**
     * @param mixed $user_lastname
     */
    public function setUserLastname($user_lastname)
    {
        $this->user_lastname = $user_lastname;
    }

    /**
     * @return mixed
     */
    public function getUserFirstname()
    {
        return $this->user_firstname;
    }

    /**
     * @param mixed $user_firstname
     */
    public function setUserFirstname($user_firstname)
    {
        $this->user_firstname = $user_firstname;
    }
    public $user_firstname;



    /**
     * @return mixed
     */
    public function getIdComment()
    {
        return $this->id_comment;
    }

    /**
     * @param mixed $id_comment
     */
    public function setIdComment($id_comment)
    {
        $this->id_comment = $id_comment;
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
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * @param mixed $publish
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;
    }
}
