<?php
/**
 * @author Borja Morales.
 */

namespace Kizilare\Framework;
ini_set('display_errors', true);
error_reporting(E_ALL);

set_error_handler ( array( 'ErrorHandler', 'error' ) );
App::getInstance()->run();

class App
{
	/**
	 * Singleton Class.
	 *
	 * @var App
	 */
	static protected $instance;

	/**
	 * Configuration file.
	 *
	 * @var string
	 */
	const CONFIG_FILE = 'config.php';

	/**
	 * Application configuration.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Restricted constructor.
	 */
	protected function __construct()
	{
		$config = array();
		if ( is_file( self::CONFIG_FILE ) )
		{
			include self::CONFIG_FILE;
		}
		$this->config = $config;
		$this->config['base'] = str_replace( 'app.php', '', $_SERVER['SCRIPT_NAME'] );
	}

	/**
	 * Singleton implementation.
	 *
	 * @return App
	 */
	static public function getInstance()
	{
		if ( empty( self::$instance ) )
		{
			self::$instance = new App();
		}
		return self::$instance;
	}

	/**
	 * Executes application.
	 */
	public function run()
	{
		try
		{
			$request = $this->getRequest();
			if ( empty( $this->config['routes'] ) )
			{
				throw new \Exception404("No routes", 1);
			}
			else
			{
				list( $controller_name, $action_name, $parameters ) = $this->findRoute( $request );
			}

			$controller = new $controller_name();
			$controller->$action_name( $parameters );
		}
		catch( \Exception404 $e )
		{
			$e->displayMessage();
		}
	}

	/**
	 * Finds the route for the current path.
	 *
	 * @param array $request Request information.
	 * @return array
	 */
	protected function findRoute( array $request )
	{
		foreach( $this->config['routes'] as $pattern => $action )
		{
			$pattern = '/^' . $pattern . '$/';
			$request_id = $request['method'] . ' ' . $request['path'];
			if ( preg_match( $pattern, $request_id, $parameters ) )
			{
				$chunks = explode( '::', $action );
				$controller_name = $chunks[0];
				$action_name = isset( $chunks[1] ) ? $chunks[1] : 'index';
				return array( $controller_name, $action_name, $this->cleanParameters( $parameters ) );
			}
		}
		throw new Exception404( "Invalid request '$request_id'" );
	}

	/**
	 * Clean parameters from integer keys.
	 *
	 * @param array $parameters Parameters to clean
	 * @return array
	 */
	protected function cleanParameters( array $parameters )
	{
		foreach( $parameters as $parameter => $value )
		{
			if( is_int( $parameter ) )
			{
				unset( $parameters[$parameter] );
			}
		}
		return $parameters;
	}

	/**
	 * Gets configuration.
	 *
	 * @param string $item Item to get from config.
	 * @return mixed
	 */
	public function getConfig( $item )
	{
		return isset( $this->config[$item] ) ? $this->config[$item] : null;
	}

	/**
	 * Get queried url.
	 *
	 * @return array
	 */
	protected function getRequest()
	{
		return array(
			'method'	=> empty( $_SERVER['REQUEST_METHOD'] ) ? 'GET' : $_SERVER['REQUEST_METHOD'],
			'path'		=> $this->getPath()
		);
	}

	/**
	 * Get queried url.
	 *
	 * @return string
	 */
	protected function getPath()
	{
		if ( empty( $_GET['queried_url'] ) )
		{
			if ( false === strstr( $_SERVER['REQUEST_URI'], $_SERVER['PHP_SELF'] ) )
			{
				return '/';
			}
			return str_replace( $_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF'] );
		}
		return $_GET['queried_url'];
	}
}