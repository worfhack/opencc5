<?php


class FrontController extends Controller
{
    protected $tags;
    protected $lastPosts;

    private function  loadlastPosts(){
        $limit_home_page = $this->context->getConfig('_LIMIT_SIDEBAR_POST_');
        $this->lastPosts = new ArticleCollection($this->id_lang);
        $this->lastPosts ->getLastPublication(_ID_LANG_, $limit_home_page, 1);
        $this->lastPosts ->load();
    }
    private function  loadTags(){

    }
    function __construct()
    {
        $menuCat = Category::getTree();
        $this->viewManager = new RenderFront(['page_name' => $this->pageName]);
        parent::__construct();
        $this->loadlastPosts();
        $this->viewManager->initVariable(

            array('menuCat' => $menuCat,
                    "sidebarData"=>array('lastPosts'=>$this->lastPosts),
            ));

    }
}