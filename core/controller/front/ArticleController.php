<?php

class ArticleController extends FrontController
{
    
    public function index($id_article)
    {
           $article = new Article($id_article, $this->id_lang);

            if (!$article->id)
            {

                throw new NotFoundException();
            }

            $this->viewManager->initVariable(

                    array('title'=>$article->getTitle(),
                        'id_article'=>$article->getIdArticle(),
                        'rewrite'=>$article->getPostLink(),
                        'comments'=>$article->getComments(),
                        'content'=>$article->getContent(),
                        'dateAdd'=>$article->getDateAdd(),
                        'name'=>$article->getName(),
                        'authorFirstName'=>$article->getAuthorFirstName(),
                        'authorLastName'=>$article->getAuthorLastName(),
            ));

            echo $this->viewManager->render("pages/article.html");



    }

}
