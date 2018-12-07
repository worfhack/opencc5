<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class ArticleCollection extends Collection
{
    protected $modelName = 'Article';

    public function getAllPublication($id_lang = false)
    {

    }

    public function getLastPublication($id_lang = false, $limit=1, $page=1, $id_category=false)
    {


        if ($id_category) {
            $this->model->fields_join[] = [
                'table'=>'category_article',
                'key'=>'category_article',
                'onleft'=>'id_article',
                'onright'=>'id_article',
                'andwhere'=>'category_article.id_category = ' . $id_category,

                ];

        }
        $this->model->order_by = 'date_add';
        $this->model->order_way = 'DESC';
        $this->model->get_list_limit_force  = 1;
        $this->model->get_list_limit_deb = ($page - 1 ) * $limit;
        $this->model->get_list_limit_end = $limit;
        $this->model->active_filters = false;
    }
}