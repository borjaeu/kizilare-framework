<?php
/**
 * @author Borja Morales.
 */
namespace Kizilare\Framework;

class App
{
    /**
     * Singleton Class.
     *
     * @var App
     */
    static protected $instance;

    /**
     * Application configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * Pretty urls used.
     *
     * @var boolean
     */
    protected $pretty_urls = true;

    /**
     * Restricted constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Sets the pretty urls value.
     *
     * @param boolean $pretty_urls
     */
    public function setPrettyUrls( $pretty_urls )
    {
        $this->pretty_urls = $pretty_urls;
    }

    /**
     * Singleton implantation.
     *
     * @param array $configuration
     * @return App
     */
    static public function getInstance( $configuration = array() )
    {
        if (empty( self::$instance )) {
            self::$instance = new App( $configuration );
        }
        return self::$instance;
    }

    /**
     * Executes application.
     *
     * @param Config $configuration
     */
    public function run(Config $configuration)
    {
        $this->config = $configuration;
        $this->config->set('base', str_replace('app.php', '', $_SERVER['SCRIPT_NAME']));
        try {
            $request = $this->getRequest();
            if (!$this->config->get('routes')) {
                throw new Exception404( "No routes", 1 );
            } else {
                list($controller_name, $action_name, $parameters) = $this->findRoute($request);
            }

            $controller = new $controller_name();
            $controller->$action_name( $parameters );
        } catch ( Exception404 $e ) {
            $e->displayMessage();
        }
    }

    /**
     * Finds the route for the current path.
     *
     * @param string $request_id Request information.
     * @return array
     * @throws Exception404
     */
    protected function findRoute($request_id)
    {
        $routes = $this->config->get('routes');
        foreach ($routes as $pattern => $action) {
            $pattern = '/^' . $pattern . '$/';
            if (preg_match( $pattern, $request_id, $parameters )) {
                $chunks = explode( '::', $action );
                $controller_name = $chunks[0];
                $action_name = isset($chunks[1]) ? $chunks[1] : 'index';
                return array($controller_name, $action_name, $this->cleanParameters($parameters));
            }
        }
        throw new Exception404( "Invalid request '$request_id'" );
    }

    /**
     * Clean parameters from integer keys.
     *
     * @param array $parameters Parameters to clean
     *
     * @return array
     */
    protected function cleanParameters( array $parameters )
    {
        foreach ($parameters as $parameter => $value) {
            if (is_int( $parameter )) {
                unset( $parameters[$parameter] );
            }
        }
        return $parameters;
    }

    /**
     * Get queried url.
     *
     * @return array
     */
    protected function getRequest()
    {
        global $argc, $argv, $args;

        if (php_sapi_name() == 'cli') {
            return trim('CLI ' . (isset($argv[1]) ? $argv[1] : ''));
        } else {
            return (empty($_SERVER['REQUEST_METHOD']) ? 'GET' : $_SERVER['REQUEST_METHOD']) . ' ' . $this->getPath();
        }
    }

    /**
     * Get queried url.
     *
     * @return string
     */
    protected function getPath()
    {
        if (empty( $_GET['queried_url'] )) {
            if (false === strstr( $_SERVER['REQUEST_URI'], $_SERVER['PHP_SELF'] )) {
                if( $this->pretty_urls ){
                    return '/';
                } else {
                    $new_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '/';
                    header( "Location: $new_url" );
                    exit;
                }
            }
            return str_replace( $_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF'] );
        }
        return $_GET['queried_url'];
    }
}
