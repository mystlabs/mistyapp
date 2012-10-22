<?php

namespace MistyApp\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestFilter
{
    /**
     * Apply a transformation over the request before it's processed by the FrontController
     * It can return a Response object, and cut out the front controller and the other REQUEST filters
     *
     * @param Request $request
     * @return Response|null
     */
    public function apply(Request $request);
}
