<?php

namespace MistyApp\View;

use MistyApp\Component\ParameterBag;
use MistyApp\Exception\ConfigurationException;
use MistyDepMan\Container;

class Theme
{
    use Viewable, Container;

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

    /**
     * Select which layout should me applied to this response
     *
     * @param string $layout The name of the layout to apply
     * @throws ConfigurationException If the layout doesn't exist
     * @return $this
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
     * @return $this
     */
    public function setNoLayout()
    {
        $this->selectedLayout =false;
        return $this;
    }

    /**
     * Wraps the content in the requested layout
     *
     * @param string $content The content to apply the theme to
     * @return string $content
     */
    public function apply($content)
    {
        if ($this->selectedLayout !== false) {
            $content = $this
                ->initializeView($this->templateFolder)
                ->assign('content', $content)
                ->render($this->layouts->get($this->selectedLayout));
        }

        return $content;
    }

    /**
     * @see Viewable
     */
    protected function getConfiguration()
    {
        return $this->provider->lookup('configuration');
    }
}
