<?php

namespace MistyApp\View;

use MistyApp\Component\ParameterBag;
use MistyApp\Exception\ConfigurationException;
use MistyApp\Filter\ResponseFilter;

use MistyDepMan\Container;

use Symfony\Component\HttpFoundation\Response;

class Theme implements ResponseFilter
{
    use Viewable, Container;

    protected $configuration;

    protected $layouts;
    protected $templateFolder;
    protected $selectedLayout;

    /**
     * Create a theme that supports the given layouts
     *
     * @param array $layouts An associative array of layout_name => layout_template
     * @param string $defaultLayout The layout to apply when there's no layout specified
     * @param string $templateFolder The folder where the template files are
     */
    public function __construct(array $layouts, $defaultLayout, $templateFolder)
    {
        $this->layouts = new ParameterBag($layouts);
        $this->templateFolder = $templateFolder;
        $this->setLayout($defaultLayout);
    }

    protected function initialize()
    {
        $this->configuration = $this->provider->lookup('configuration');
    }

    /**
     * Select which layout should me applied to this response
     *
     * @param string $layout The name of the layout to apply
     * @throws MistyApp\Exception\ConfigurationException If the layout doesn't exist
     * @chainable
     */
    public function setLayout($layout)
    {
        if (!$this->layouts->has($layout)) {
            throw new ConfigurationException(sprintf(
                "Unknown layout '%s'",
                $layout
            ));
        }

        $this->selectedLayout = $layout;
        return $this;
    }

    /**
     * Disable the layout for this request
     * @chainable
     */
    public function setNoLayout()
    {
        $this->selectedLayout =false;
        return $this;
    }

    /**
     * Wraps the response content in the requested layout
     *
     * @see ResponseFilter
     * @chainable
     */
    public function apply(Response $response)
    {
        if ($this->selectedLayout === false) {
            return; // nothing to do
        }

        if ($response->headers->get('Content-type') !== 'text/html') {
            return; // we only apply the layout to html content
        }

        $content = $this
            ->initializeView($this->templateFolder)
            ->assign('content', $response->getContent())
            ->render($this->layouts->get($this->selectedLayout));

        $response->setContent($content);
        return $this;
    }
}
