<?php

namespace MistyApp;

use MistyUtils\ExceptionFormatter;

use Symfony\Component\HttpFoundation\Response;

/**
 * Very very basic error page for when the Kernel fails to render
 * Display developer-friendly information about the Exception
 */
class ErrorPage
{
    protected $kernel;

    /**
     * Act as a proxy for all the calls to the Kernel, and display information about
     * the Exception
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Forward all the calls to the Kernel
     */
    public function __call($method, $args)
    {
        try {
            return call_user_func_array(array($this->kernel, $method), $args);
        } catch(\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Display a generic error page, and information about the error
     */
    private function handleError($exception)
    {
        $formatter = new ExceptionFormatter($exception);
        $content = sprintf('
            <!DOCTYPE html>
            <html>
              <head>
                <meta charset="utf-8">
                <title>%s</title>
              </head>
              <body>
                <h1>%s</h1>
                <p>%s</p>
                %s
              </body>
            </html>',
            $exception->getMessage(),
            'There\'s been an error',
            'The page could not be displayed',
            $formatter->format()
        );

        $response = new Response($content, 503);
        $response->send();
    }
}
