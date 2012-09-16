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
    protected $defaultLayout;
    protected $templateFolder;
    protected $selectedLayout;

    /**
     * Create a theme that supports the given layouts
     *
     * @param array $layouts The layouts enabled
     * @param string $defaultLayout The layout to apply when there's no layout specified
     * @param string $templateFolder The folder where the template files are
     */
    public function __construct(array $layouts, $defaultLayout, $templateFolder)
    {
        $this->layouts = new ParameterBag();
        foreach ($layouts as $layout) {
            $this->layouts->set($layout, $layout);
        }
        $this->defaultLayout = $defaultLayout;
        $this->templateFolder = $templateFolder;
        $this->selectedLayout = null;
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

        $layout = $this->selectedLayout ? $this->selectedLayout : $this->defaultLayout;

        $content = $this
            ->initializeView($this->templateFolder)
            ->assign('content', $response->getContent())
            ->render(sprintf(
                '%s/layout.tpl',
                $layout
            ));

        $response->setContent($content);
        return $this;
    }
}
