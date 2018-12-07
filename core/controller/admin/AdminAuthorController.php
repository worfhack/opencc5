<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminAuthorController extends AdminController
{
    public function list()
    {

        $collectionManager = new AdministratorCollection($this->id_lang);
        $collectionManager->load();
        $this->viewManager->initVariable(

            array('authors' => $collectionManager)
        );
        echo $this->viewManager->render('pages/administrator/list.html', [
        ]);
    }

    public function form($id_author = false)
    {

        if ($id_author != false)
        {
            $author = new Administrator($id_author, _ID_LANG_ );
        }else
        {
            $author =  NULL;
        }
        $this->viewManager->initVariable(
            [
                'author'=>$author,
            ]);


        echo $this->viewManager->render('pages/administrator/form.html', [
        ]);

    }

    public function add()
    {
        $author = new Administrator();
        $author->copy_from_post();
        $author->add();
        Tools::redirectAdmin('/author');

    }

    public function remove($id)
    {
        $author = new Administrator($id);
        $author->delete();
        Tools::redirectAdmin('/author');
    }
    public function edit($id)
    {


        $author = new Administrator($id, _ID_LANG_);
        $author->copy_from_post();
        $author->update();

        Tools::redirectAdmin('/author');

    }
}