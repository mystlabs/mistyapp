<?php

namespace MistyApp\Routing;

use MistyRouting\Route\Route;

abstract class AbstractModuleRoute extends Route
{
	/**
	 * @see IRoute
	 */
	public function encode(array $params)
	{
		if (isset($params['module'])) {
			$params['module'] = strtolower($params['module']);
		}

		return parent::encode($params);
	}

	/**
	 * @see IRoute
	 */
	public function decode($path)
	{
		$controllerActionParams = parent::decode($path);

		if ($controllerActionParams !== null ){
			$controllerActionParams->params['module'] = ucfirst($controllerActionParams->params['module']);
		}

		return $controllerActionParams;
	}
}
