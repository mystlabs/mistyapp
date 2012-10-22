<?php

namespace MistyApp\Extension;

class FiltersExtension implements ExtensionInterface
{
    private $filterName;
    private $filters;

    public function __construct($filterName, array $filters)
    {
        $this->filterName = $filterName;
        $this->filters = $filters;
    }

    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
        $filters = $provider->lookup($this->filterName);
        foreach ($this->filters as $key => $filter)
        {
            $filters->set($key, $filter);
        }
    }
}
