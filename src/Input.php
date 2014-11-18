<?php
namespace Kizilare\Framework;

/**
 * Class Input
 */
Class Input
{
    static protected $post_data;

    static protected $put_data;

    static function getPost()
    {
        return $_POST;
    }

    static function getGet()
    {
        return $_GET;
    }

    static function getPut()
    {
        if ( is_null( self::$put_data ) )
        {
            $data = file_get_contents( 'php://input' );
            self::$put_data = array();
            parse_str( $data, self::$put_data );
        }
        return self::$put_data;
    }
}