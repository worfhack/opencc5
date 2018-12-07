<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminArticleController extends AdminController
{
    public function list()
    {

        $collectionManager = new ArticleCollection($this->id_lang);
        $collectionManager->getAllPublication();
        $collectionManager->load();

        $this->viewManager->initVariable(

            array('articles' => $collectionManager)
        );
        echo $this->viewManager->render('pages/articles/list.html', [
        ]);
    }

    public function form($id_article = false)
    {

        if ($id_article != false)
        {
            $article = new Article($id_article, _ID_LANG_ );
        }else
        {
            $article =  NULL;
        }
        $collectionManager = new AdministratorCollection($this->id_lang);
        $collectionManager->load();
        $this->viewManager->initVariable(
            [
                'article'=>$article,
                'authors'=>$collectionManager,
            ]);


        echo $this->viewManager->render('pages/articles/form.html', [
        ]);

    }

    public function add()
    {
        $article = new Article();
        $article->copy_from_post();
        $article->add();
        Tools::redirectAdmin('/article');

    }

    public function remove($id)
    {
        $article = new Article($id);
        $article->delete();
        Tools::redirectAdmin('/article');
    }
    public function edit($id)
    {


        $article = new Article($id, _ID_LANG_);
        $article->copy_from_post();
        $article->update();

        Tools::redirectAdmin('/article');

    }
}