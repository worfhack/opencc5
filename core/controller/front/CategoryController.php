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
        $nbr_page = ceil( $category->getArticles()->count_all / $limit_home_page);

        if ($page < $nbr_page)
        {
            $next = $page  +1;
        }else
        {
            $next =false;
        }
        if ($page != 1)
        {
            $prev = $page -1 ;
        }else
        {
            $prev = false;
        }
        if (!$category->id) {
            throw new NotFoundException();
        }

        $metadescription = $category->getMetaDescription();
        if (!$metadescription)
        {
            $metadescription =  Context::getContext()->getConfig('_DEFAULT_DESCRIPTION_');
        }
        $this->viewManager->initVariable(

            array('title' => $category->getTitle(),
                'articles' => $category->getArticles(),
                'dateAdd' => $category->getDateAdd(),
                'nbr_page'=>$nbr_page,
                'link_rewrite'=>$category->getUrlRewrite(),
                'canonical'=>Context::getContext()->getBaseurlLang()  . "category/"  .$category->getUrlRewrite(),
                "pageDescription"=>$metadescription,
                'next'=>$next,
                'prev'=>$prev,
                'current_page'=>$page,
            ));
        echo $this->viewManager->render("pages/category.html");


    }

}
