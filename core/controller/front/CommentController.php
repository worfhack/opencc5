<?php

class CommentController extends FrontController
{

    public function addComment()
    {
        $id_article = Tools::getValue('id_article');
        $message = Tools::getValue('message');
        $article = new Article($id_article, _ID_LANG_);
        if (!$article->id_article or !$this->user)
        {
            throw new NotFoundException();
        }
            $comment = new Comment();
            $comment->setIdArticle($id_article);
            $comment->setIdUser($this->user->id_user);
            $comment->setMessage($message);
            $comment->save();

        Tools::redirect($article->getPostLink());

    }

}
