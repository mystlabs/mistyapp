<?php

namespace MistyApp\Routing;

class ModuleAdminRoute extends AbstractModuleRoute
{
	public function __construct()
	{
		parent::__construct(
			'admin',
			'/admin/:module/:action/:*',
			':module\Controller\:moduleAdminController',
			':action'
		);
	}
}
