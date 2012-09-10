<?php

namespace MistyApp\View;

trait Viewable
{
    protected $view;

    /**
     * Initialize the view
     * Require the class to have an instance of the provider
     */
    protected function initializeView()
    {
        if (!$this->view) {
            $configuration = $this->provider->lookup('configuration');
            $this->view = new Smarty();

            // setup the template folder
            $appFolder = $configuration->get('system.app.folder');
            $module = $this->getModuleFromNamespace();
            $this->setTemplateDir($appFolder . '/frontend/' . $module . '/views/');

            // setup the temporary folders
            $tempFolder = $configuration->get('system.temp.folder');
            $this->setCompileDir($tempFolder . '/smarty_compiled/');
            $this->setCacheDir($tempFolder . '/smarty_cache/');
            $this->setConfigDir($tempFolder . '/smarty_config/');

            // add all the plugin folders to the view
            foreach ($configuration->get('view.plugin.folders', array()) as $pluginFolder) {
                $this->addPluginsDir($pluginFolder);
            }

            // if we are in development mode we want to regenerate the views at every render
            if ($configuration->get('system.development.mode')) {
                $this->view->compile_check = true;
                $this->view->force_compile = true;
            }
        }
    }

    /**
     * Assign a variable to the underlying view
     *
     * @param string $name
     * @param mixed $value
     */
    protected function assign($name, $value)
    {
        $this->initializeView();
        $this->view->assign($name, $value);
    }

    /**
     * Render the given template
     *
     * @param string $template The template to render
     */
    protected function render($template)
    {
        $this->initializeView();
        return $this->view->fetch($template);
    }

    /**
     * Extract the module name from the class using this trait
     * e.g. News\Controller\NewsController => 'News'
     */
    private function getModuleFromNamespace()
    {
        $className = get_class($this);
        $tokens = explode( "\\", $className );
        return $tokens[0];
    }
}
