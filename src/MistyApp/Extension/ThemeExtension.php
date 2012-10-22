<?php

namespace MistyApp\Extension;

use MistyApp\Exception\ConfigurationException;

class ThemeExtension implements ExtensionInterface
{
    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
        $provider->register('theme', $provider->create(
            'MistyApp\View\Theme',
            $configuration->get('theme.layouts'),
            $configuration->get('theme.default'),
            $configuration->get('theme.folder')
        ));

    }
}
