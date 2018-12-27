<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminCommentController extends AdminController
{


    public function remove($id)
    {
        $comment = new Comment($id);
        $comment->delete();
        Tools::redirectAdmin('/');
    }
    public function publish($id)
    {


        $comment = new Comment($id);
        $comment->publish = 1;
        $comment->update();
        Tools::redirectAdmin('/');

    }
}
