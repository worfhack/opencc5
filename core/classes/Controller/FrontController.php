<?php


class FrontController extends Controller
{
    protected $tags;
    protected $lastPosts;
    protected $user;
    protected $with_sidebar = true;

    private function  loadlastPosts(){
        $limit_home_page = $this->context->getConfig('_LIMIT_SIDEBAR_POST_');
        $this->lastPosts = new ArticleCollection($this->id_lang);
        $this->lastPosts ->getLastPublication(_ID_LANG_, $limit_home_page, 1);
        $this->lastPosts ->load();
    }

    function __construct()
    {
        $this->user =  Users::getUsers();

        if ($this->user === false && $this->need_to_be_log === true) {
            Tools::redirect(_BASE_URL_. '/login');
        }

        $menuCat = Category::getTree();
        $this->viewManager = new RenderFront(['page_name' => $this->pageName]);
        parent::__construct();
        $this->loadlastPosts();
        $this->viewManager->initVariable(

            array('menuCat' => $menuCat,
                    'user'=>$this->user,
                    'with_sidebar'=>$this->with_sidebar,
                    "sidebarData"=>array('lastPosts'=>$this->lastPosts),
            ));

    }
}
