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

        if ($id_article !== false)
        {
            $article = new Article($id_article, _ID_LANG_ );
        }else
        {
            $article =  NULL;
        }

        $categories = new CategoryCollection($this->id_lang);
        $categories->load();
        $authors = new AdministratorCollection($this->id_lang);
        $authors->load();
        $this->viewManager->initVariable(
            [
                'article'=>$article,
                'article_cat'=>($article ? $article->getCategories(): []),
                'categories'=>$categories,
                'authors'=>$authors,
            ]);


        echo $this->viewManager->render('pages/articles/form.html', [
        ]);

    }

    public function add()
    {
        $categories = Tools::getValue('categories');
          if (!is_array($categories))
        {
            $categories = [$categories];
        }
        $article = new Article();
        $article->copyFromPost();
        $article->add();
          $article->setCategories($categories);
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

        $categories = Tools::getValue('categories');
          if (!is_array($categories))
        {
            $categories = [$categories];
        }
        $article = new Article($id, _ID_LANG_);
        $article->copyFromPost();
        $article->setCategories($categories);
        $article->update();

        Tools::redirectAdmin('/article');

    }
}
