<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class Collection implements IteratorAggregate
{
    protected $model;
    protected $modelName;
    protected $id_lang;
    protected $collection;
    protected $context;

    public function __construct($id_lang = false)
    {
        $this->context = Context::getContext();
        $this->model = new $this->modelName();
        if (!$id_lang) {
            $id_lang = $this->context->getCurrentLanguage()->id_lang;
        }
        $this->id_lang = $id_lang;

    }

    public function load()
    {
        $this->collection = [];
        $results = $this->model->get_list($this->id_lang, false);
        foreach ($results as $r)
        {
            $this->collection[] = call_user_func(array($this->modelName, 'toObject'), $r);
        }

    }


    public function getIterator()
    {
            return new ArrayIterator($this->collection);

    }

}