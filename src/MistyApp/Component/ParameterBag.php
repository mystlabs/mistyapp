<?php

namespace MistyApp\Component;

class ParameterBag implements \IteratorAggregate, \Countable
{
    protected $params;

    /**
     * @param array $params Initial values for this bag
     */
    public function __construct(array $params = array())
    {
        $this->params = $params;
    }

    /**
     * Add a single parameter to the bag
     *
     * @param string $name The name of the parameter
     * @param mixed $value The value of the parameter
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Add all the parameters to this bag
     *
     * @param array $params The parameters to be added to this bag
     */
    public function setAll(array $params)
    {
        $this->params = array_replace(
            $this->params,
            $params
        );
    }

    /**
     * Check whether the bag contains a parameter
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Retrieve a parameter from the bag, or return a default value
     *
     * @param string $name The name of the parameter to retrieve
     * @param mixed $default The value to return if the parameter doesn't exist
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->params[$name];
        }

        return $default;
    }

    /**
     * Get all the parameters in this bag
     *
     * @return array All the parameters
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * Remove a parameter
     *
     * @param string $name The name of the parameter to be removed
     */
    public function remove($name)
    {
        unset($this->params[$name]);
    }

    /**
     * Returns an iterator for parameters in this bag
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->params);
    }

    /**
     * Returns the number of parameters in this bag
     *
     * @return int The number of parameters
     */
    public function count()
    {
        return count($this->params);
    }
}
