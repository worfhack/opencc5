<?php

class CategoryController extends FrontController
{

    public function index($rewrite)
    {
        $rewrite = trim($rewrite, '/');
        $rewrite = explode("/", $rewrite);

        $rewrite = end($rewrite);
        $context = Context::getContext();

        $page = Tools::getValue("page", 1);
        $limit_home_page = $context->getConfig('_LIMIT_CATEGORY_POST_');
        $id =  Category::getIdByRewrite($rewrite);
        $category = new Category($id, $this->id_lang);
        $category->loadArticles($limit_home_page, $page);

        if (!$category->id) {
            throw new NotFoundException();
        }
        $this->viewManager->initVariable(

            array('title' => $category->getTitle(),
                'articles' => $category->getArticles(),
                'dateAdd' => $category->getDateAdd(),

            ));
        echo $this->viewManager->render("pages/category.html");


    }

}
