<?php

namespace MistyApp\Extension;

class AllErrorsExtension implements ExtensionInterface
{
    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
		// Don't suppress any error
		error_reporting(E_ALL);
		ini_set('display_errors','On');

        return;

		// Treat all errors/notice as fatal error
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
		    if (error_reporting() !== 0) {
		        throw new \Exception(sprintf(
		            '%s in %s on line %s (%d)',
		            $errstr,
		            $errfile,
		            $errline,
                    $errno
		        ));
		    }
		});
	}
}
