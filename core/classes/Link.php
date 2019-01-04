<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 21/10/2018
 * Time: 21:07
 */
class Link
{
    static public function getPageLink($page)
    {
            $context = Context::getContext();
            $url_link = $context->getBaseurlLang();
            return $url_link . $page;
    }
}
