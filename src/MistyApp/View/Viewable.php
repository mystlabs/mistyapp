<?php

namespace MistyApp\View;

use MistyApp\Component\Configuration;
use MistyApp\Exception\ConfigurationException;
use MistyDepMan\Provider;

trait Viewable
{
    /** @var \Smarty */
    protected $view;

    /**
     * Initialize the view
     * Require the class to have an instance of the provider
     *
     * @param string $templateFolder Optional template folder to use instead of the default /frontend/module/views/
     * @return Viewable
     */
    protected function initializeView($templateFolder = null)
    {
        if (!$this->view) {
            $this->view = new \Smarty();
            $configuration = $this->getConfiguration();

            $appFolder = $configuration->get('system.app.folder');
            if ($templateFolder === null) {
                $module = $this->getModuleFromNamespace();
                $templateFolder = $appFolder . '/frontend/' . $module . '/views/';
            }

            // setup the template folder
            $this->view->setTemplateDir($templateFolder);

            // setup the temporary folders
            $tempFolder = $configuration->get('system.temp.folder');
            $this->view->setCompileDir($this->checkWritable($tempFolder . '/smarty_compiled/'));
            $this->view->setCacheDir($this->checkWritable($tempFolder . '/smarty_cache/'));
            $this->view->setConfigDir($this->checkWritable($tempFolder . '/smarty_config/'));

            // add all the plugin folders to the view
            foreach ($configuration->get('view.plugin.folders', array()) as $pluginFolder) {
                $this->view->addPluginsDir($pluginFolder);
            }
            $this->view->addPluginsDir($appFolder . '/../vendor/mystlabs/mistyforms/src/MistyForms/smarty_plugins');
            $this->view->addPluginsDir($appFolder . '/../vendor/mystlabs/mistyapp/src/MistyApp/smarty_plugins');

            // if we are in development mode we want to regenerate the views at every render
            if ($configuration->get('system.development.mode', false)) {
                $this->view->compile_check = true;
                $this->view->force_compile = true;
            }
        }

        return $this;
    }

    /**
     * Assign a variable to the underlying view
     *
     * @param string $name
     * @param mixed $value
     * @return Viewable
     */
    protected function assign($name, $value = null)
    {
        $this->initializeView();
        $this->view->assign($name, $value);

        return $this;
    }

    /**
     * Render the given template
     *
     * @param string $template The template to render
     * @return string The output of the template
     */
    protected function render($template)
    {
        $this->initializeView();
        return $this->view->fetch($template);
    }

    /**
     * Add a form to the view, and use the given handler to
     *
     * @param Handler $handler The name of the handler
     */
    protected function handleForm($handler)
    {
        $this->initializeView();
        \MistyForms\Form::setupForm(
            $this->view,
            $handler
        );
    }

    /**
     * Extract the module name from the class using this trait
     * e.g. News\Controller\NewsController => 'News'
     *
     * @return string
     */
    private function getModuleFromNamespace()
    {
        $className = get_class($this);
        $tokens = explode( "\\", $className );
        return $tokens[0];
    }

    /**
     * Check that the folder is writable, or throw an exception
     *
     * @param string $folder The folder that must be writable
     * @return string The folder
     * @throws ConfigurationException
     */
    private function checkWritable($folder)
    {
        if (!is_dir($folder)) {
            throw new ConfigurationException(sprintf(
                "'%s' doesn't exist or is not a folder",
                $folder
            ));
        }

        if (!is_writable($folder)) {
            throw new ConfigurationException(sprintf(
                "The folder '%s' must be writable",
                $folder
            ));
        }

        return $folder;
    }

    /**
     * @return Configuration
     */
    abstract function getConfiguration();
}
