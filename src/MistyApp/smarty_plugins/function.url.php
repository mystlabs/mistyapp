<?php

/**
 * Take a path and transform it into a url
 * @required string $path
 */
function smarty_function_url($params, $smarty)
{
    $path = $params['path'];
    unset($params['path']);

    global $provider;
    return $provider
        ->lookup('urilifier')
        ->encode($path, $params);
}
