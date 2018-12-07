<?php

class HomeController extends FrontController
{

    public function index()
    {
        $context = Context::getContext();
        $limit_home_page = $context->getConfig('_LIMIT_HOMEPAGE_POST_');
        $page = Tools::getValue("page", 1);
        $collectionManager = new ArticleCollection($this->id_lang);
        $collectionManager->getLastPublication(_ID_LANG_, $limit_home_page, $page);
        $collectionManager->load();
        $nbr_page = ceil( $collectionManager->count_all / $limit_home_page);

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

        $this->viewManager->initVariable(

            array('listLastPub' => $collectionManager,
            'nbr_page'=>$nbr_page,
                'next'=>$next,
                'prev'=>$prev,
           'current_page'=>$page,
            )
        );

        echo $this->viewManager->render("pages/home.html");


    }

}
