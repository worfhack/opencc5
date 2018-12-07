<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminCategoryController extends AdminController
{
    public function list()
    {

        $collectionManager = new CategoryCollection($this->id_lang);
        $collectionManager->load();

        $this->viewManager->initVariable(

            array('categories' => $collectionManager)
        );
        echo $this->viewManager->render('pages/category/list.html', [
        ]);
    }

    public function form($id_article = false)
    {

        if ($id_article != false)
        {
            $category = new Category($id_article, _ID_LANG_ );
        }else
        {
            $category =  NULL;
        }
        $collectionManager = new AdministratorCollection($this->id_lang);
        $collectionManager->load();
        $this->viewManager->initVariable(
            [
                'category'=>$category,
            ]);


        echo $this->viewManager->render('pages/category/form.html', [
        ]);

    }

    public function add()
    {
        $category = new Category();
        $category->copy_from_post();
        $category->add();

        Tools::redirectAdmin('/category');

    }

    public function remove($id)
    {
        $category = new Category($id);
        $category->delete();
        Tools::redirectAdmin('/category');
    }
    public function edit($id)
    {


        $category = new Category($id, _ID_LANG_);
        $category->copy_from_post();
        $category->update();

        Tools::redirectAdmin('/category');

    }
}