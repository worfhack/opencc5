<?php

class RouteCollection extends \SplObjectStorage
{
    /**
     * Attach a Route to the collection.
     *
     * @param Route $attachObject
     */
    public function attachRoute(Route $attachObject)
    {
        parent::attach($attachObject, null);
    }

    /**
     * Fetch all routes stored on this collection of routes and return it.
     *
     * @return Route[]
     */
    public function all()
    {
        $temp = array();
        foreach ($this as $route) {
            $temp[] = $route;
        }

        return $temp;
    }
}
