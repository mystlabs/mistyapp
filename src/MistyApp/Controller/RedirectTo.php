<?php

namespace MistyApp\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectTo extends \Exception
{
    private $url;
    private $redirectCode;

    public function __construct($url, $redirectCode = 302)
    {
        $this->url = $url;
        $this->redirectCode = $redirectCode;
    }

    public function getResponse()
    {
        return new RedirectResponse(
            $this->url,
            $this->redirectCode
        );
    }

    public function getUrl()
    {
        return $this->url;
    }
}
