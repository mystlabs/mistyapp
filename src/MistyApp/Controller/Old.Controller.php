<?php

namespace MistyApp;

use Mist\Form\Form;

use Mist\Presentation\View;

use Mist\DependencyInjection\IProvider;
use Mist\DependencyInjection\Container;
use Mist\DependencyInjection\Proxy;

use Mist\Http\HtmlResponse;
use Mist\Http\Response;
use Mist\Http\Request;

class Controller extends Container
{
	protected $request;
	protected $view;

	protected $sessionManager;
	protected $permissionManager;
	protected $router;

	protected $user;

	public function __construct( IProvider $provider, Request $request )
	{
		parent::__construct( $provider );

		$this->request = $request;
		$this->view = new View( $this->provider, $this->getModuleFromNamespace() );
		$this->view->assign( '_request', $request );

		$this->sessionManager = $this->provider->getSessionManager();
		$this->permissionManager = $this->provider->get( 'Mist\Permission\PermissionManager' );
		$this->router = $this->provider->getRouter();

		$this->user = $this->sessionManager->getUser();

		$this->initialize();
	}

	protected function initialize()
	{
		// nothing to do
	}

	public function createForm( $formId, $handlerName )
	{
		$handler = new $handlerName( $this->provider, $this->request, $this->view );
		$form = new Form( $formId, $handler, $this->request );

		$this->view->assign( $formId, $form );
	}

	public function handle( $actionName )
	{
		$response = $this->$actionName();
		if( !$response instanceof Response )
		{
			$response = new HtmlResponse( $response );
		}

		return $response;
	}

	protected function requireLogin()
	{
		if( !$this->sessionManager->isLoggedIn() )
		{
			$this->redirectTo( 'Users', array(
				'action' => 'login',
				'redirect' => urlencode( urlencode( $this->request->getRequestUri() ) )
			) );
		}
	}

	protected function redirectTo( $module, array $params=array(), $anchor=false, $absolute=false )
	{
		$params['module'] = $module;
		$redirectUrl = $this->router->encode( $params, $anchor, $absolute );
		Response::redirect( $redirectUrl );
	}

	private function getModuleFromNamespace()
	{
		$className = get_class($this);
		$tokens = explode( "\\", $className );
		return $tokens[0];
	}
}
