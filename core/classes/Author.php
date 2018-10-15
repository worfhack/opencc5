<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 07/08/17
 * Time: 18:17
 */
class Author extends ObjectModel
{
    public $table = 'author';
    public $identifier = 'id_author';



    public $mail;

    public $password;

    public $firstname;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    public function getName()
    {
        return $this->getFirstname() . ' ' . $this->getFirstname();
    }
    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getIdZendesk()
    {
        return $this->id_zendesk;
    }

    /**
     * @param mixed $id_zendesk
     */
    public function setIdZendesk($id_zendesk)
    {
        $this->id_zendesk = $id_zendesk;
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
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    public $lastname;

    public $phone;

public $id_zendesk;

    public $id_author;
    public $active = 1;


}
