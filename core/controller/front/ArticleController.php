<?php

class ArticleController extends FrontController
{
    
    public function index($id)
    {

           $article = new Article($id, $this->id_lang);
            if (!$article->id)
            {
                throw new NotFoundException();
            }
            $this->viewManager->initVariable(

                    array('title'=>$article->getTitle(),
                        'content'=>$article->getContent(),
                        'dateAdd'=>$article->getDateAdd(),
                        'name'=>$article->getName(),
                        'authorFirstName'=>$article->getAuthorFirstName(),
                        'authorLastName'=>$article->getAuthorLastName(),
            ));

            echo $this->viewManager->render("pages/article.html");



    }

}
