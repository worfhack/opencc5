<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class CategoryCollection extends Collection
{
    protected $modelName = 'Category';



    public function getRoot($id_root)
    {



        $this->model->order_by = 'date_add';
        $this->model->where = ['id_parent='.$id_root];
        $this->model->active_filters = false;
    }

}
