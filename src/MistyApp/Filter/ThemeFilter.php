<?php

namespace MistyApp\Filter;

use MistyApp\Filter\ResponseFilter;
use MistyDepMan\Container;
use Symfony\Component\HttpFoundation\Response;

class ThemeFilter implements ResponseFilter
{
    use Container;

    /**
     * Wraps the response content in the requested layout
     *
     * @see ResponseFilter
     * @return $this
     */
    public function apply(Response $response)
    {
        if ($response->headers->get('Content-type', 'text/html') !== 'text/html') {
            return; // we only apply the layout to html content
        }

        $theme = $this->provider->lookup('theme');
        $themedContent = $theme->apply($response->getContent());
        $response->setContent($themedContent);

        return $this;
    }
}
