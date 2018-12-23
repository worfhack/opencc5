<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 04/08/17
 * Time: 17:03
 */
class CommentCollection extends Collection
{
    protected $modelName = 'Comment';


    public function getFromArticle($id_article)
    {

        $this->model->order_by = 'date_add';
        $this->model->where = ['publish'=>1,'id_article'=>$id_article];
        $this->model->active_filters = false;
    }
    public function getNotPublish($with_article=false)
    {


        if ($with_article)
        {
            $this->model->fields_join[] = [
                'table'=>'article_lang',
                'key'=>'article',
                'onleft'=>'id_article',
                'onright'=>'id_article',
                'lang'=>true,
                'fields'=>['title','id_article'],

            ];
        }


        $this->model->order_by = 'date_add';
        $this->model->where = ['publish=0'];
        $this->model->active_filters = false;
    }
}