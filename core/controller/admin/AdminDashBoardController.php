<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminDashBoardController extends AdminController
{


    public function index()
    {
        $collectionManager = new CommentCollection($this->id_lang);
        $collectionManager->getNotPublish(true);
        $collectionManager->load();
        $this->viewManager->initVariable(

            array('comments' => $collectionManager)
        );
        echo $this->viewManager->render("pages/dash.html");
    }
    public function publish()
    {
        $comment = new Comment();
        $comment->setPublish(1);
        $comment->save();
        Tools::redirectAdmin('/');

    }

    public function remove($id)
    {
        $comment = new Comment($id);
        $comment->delete();
        Tools::redirectAdmin('/');
    }
}
