<?php
/**
 * @author Borja Morales.
 */
namespace Kizilare\Framework;

class Config
{
    /**
     * Singleton Class.
     *
     * @var Config
     */
    static protected $instance;

    /**
     * Application configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Restricted constructor.
     */
    protected function __construct( $configuration )
    {
        $this->load( $configuration );
    }

    /**
     * Singleton implantation.
     *
     * @param array $configuration
     * @return self
     */
    static public function getInstance( $configuration = array() )
    {
        if (empty( self::$instance )) {
            self::$instance = new Config( $configuration );
        }
        return self::$instance;
    }
    /**
     * Restricted constructor.
     */
    final protected function load( $configuration )
    {
        $this->config = $configuration;
    }

    /**
     * Gets configuration.
     *
     * @param string $item Item to get from config.
     * @return mixed
     */
    public function get( $item )
    {
        return isset( $this->config[$item] ) ? $this->config[$item] : null;
    }

    /**
     * Gets configuration.
     *
     * @param string $item Item to get from config.
     * @param mixed $value value of the item to set.
     * @return mixed
     */
    public function set( $item, $value )
    {
        return $this->config[$item] = $value;
    }
}
