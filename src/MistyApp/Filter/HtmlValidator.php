<?php

namespace MistyApp\Filter;

use Symfony\Component\HttpFoundation\Response;

/**
 * Validate the HTML of the response
 */
class HtmlValidator implements ResponseFilter
{
    /**
     * @see ExtensionInterface
     */
    public function apply(Response $response)
    {
        $tidy = new \Tidy();
        $tidy->parseString($response->getContent());
        if ($tidy->errorBuffer) {
            throw new \Exception($tidy->errorBuffer);
        }
    }
}
