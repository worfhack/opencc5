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
        $this->count_all = $this->model->get_list_count($this->id_lang, false);
        $results = $this->model->get_list($this->id_lang, false);
        foreach ($results as $r)
        {
            $this->collection[] = call_user_func(array($this->modelName, 'toObject'), $r);
        }

    }
public function toArray()
{
    $array = [];
    foreach ($this->collection as $k)
    {
        $array[] = $k;
    }
    return $array;
}
    public function toArrayJSON()
    {
        $array = [];
        foreach ($this->collection as &$k)
        {
            if (is_object($k))
            {
                $k = get_object_vars($k);

            }

            if (is_array($k))
            {
                $k = array_intersect_key($k, array_fill_keys($this->model->json_fields, 0));

            }

            $array[] = $k;
        }
        return $array;
    }

    public function getIterator()
    {
            return new ArrayIterator($this->collection);

    }

}