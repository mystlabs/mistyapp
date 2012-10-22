<?php

namespace MistyApp\Extension;

use MistyDepMan\Provider;
use MistyApp\Component\Configuration;

interface ExtensionInterface
{
    /**
     * Extend the syst
     *
     * @param Provider $provider
     * @param Configuration $configuration
     */
    function register($provider, $configuration);
}
