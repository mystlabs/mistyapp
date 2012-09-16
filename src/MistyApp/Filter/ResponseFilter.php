<?php

namespace MistyApp\Filter;

use Symfony\Component\HttpFoundation\Response;

interface ResponseFilter
{
    /**
     * Apply a transformation over the response before it's sent
     *
     * @param Response $response
     */
    public function apply(Response $response);
}
