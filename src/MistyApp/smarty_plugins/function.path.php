<?php

/**
 * Take a path and transform it into a url
 * @required string $_r The name of the route
 */
function smarty_function_path($params, $smarty)
{
    $routeName = $params['_r'];
    unset($params['_r']);

    global $provider;
    return $provider
        ->lookup('router')
        ->encode($routeName, $params);
}
