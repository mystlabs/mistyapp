<?php

namespace MistyApp\Controller;

use MistyDepMan\Provider;

trait Redirecter
{
    /**
     * Redirect to the given route
     *
     * @param string $routeName The name of the route
     * @param array $params Params to build the path
     * @param array $options Params to build the url
     * @param int $code The HTTP redirect code
     * @throws RedirectTo
     */
    protected function redirectToRoute($routeName, $params = array(), $options = array(), $code = 302)
    {
        $urlifier = $this->getProvider()->lookup('urlifier');
        $this->redirect(
            $urlifier->url(
                $routeName,
                $params,
                array_merge($options, array('absolute' => true))
            ),
            $code
        );
    }

    /**
     * Redirect to the given url
     *
     * @param string $url The absolute url to redirect to
     * @param int $code The HTTP redirect code
     * @throws RedirectTo
     */
    protected function redirect($url, $code = 302)
    {
        throw new RedirectTo($url, $code);
    }

    /**
     * @return Provider
     */
    abstract function getProvider();
}
