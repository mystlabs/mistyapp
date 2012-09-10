<?php

namespace MistyApp\Routing;

class ModuleRoute extends AbstractModuleRoute
{
	public function __construct()
	{
		parent::__construct(
			'module',
			'/:module/:action/:*',
			':module\Controller\:moduleController',
			':action'
		);
	}
}
