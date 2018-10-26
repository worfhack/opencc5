<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class ArticleCollection  extends Collection{
  protected $modelName = 'Article';

    public function getLastPublication($id_lang=false)
    {


        $this->model->order_by = 'date_add';
        $this->model->order_way = 'DESC';
        $this->model->get_list_limit_deb = 0;
        $this->model->get_list_limit_end = 5;
        $this->model->active_filters = false;

    }
}