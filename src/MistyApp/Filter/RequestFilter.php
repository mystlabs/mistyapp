<?php

namespace MistyApp\Filter;

use Symfony\Component\HttpFoundation\Request;

interface RequestFilter
{
    /**
     * Apply a transformation over the request before it's processed by the FrontController
     *
     * @param Request $request
     */
    public function apply(Request $request);
}
