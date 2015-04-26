<?php
namespace Kizilare\Framework;

/**
 * Class Exception404
 */
class Exception404 extends \Exception
{
    /**
     * Construct the 404 exception.
     *
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param \Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct( $message = "", $code = 0, \Exception $previous = null )
    {
        parent::__construct( $message, $code, $previous );
        header( "HTTP/1.0 404 Not Found" );
    }

    /**
     * Displays the message as coded info.
     */
    public function displayMessage()
    {

        $coded = $this->simpleCrypt( $this->getTraceAsString() . "\n" . $this->getMessage(), 50 );
        echo <<<ERROR
<pre id="coded_content">$coded</pre>
ERROR;
    }

    /**
     * Code text by printing all ascii codes.
     *
     * @param string $text Text to code
     * @param integer $cols Number of columns to return to make a fancy results
     * @return string
     */
    protected function simpleCrypt( $text, $cols = 100 )
    {
        $debug = Config::getInstance()->get('debug');
        if ( $debug )
        {
            return $text;
        }

        $text = utf8_decode( $text );
        $key = rand( 0, 255 );
        $output = strtoupper( sprintf( '%02s', dechex( 255 - $key ) ) );
        $i = 0;
        while( isset( $text{$i} ) )
        {
            $index = ( ord( $text{$i} ) + $key ) % 255;
            $output .= strtoupper( sprintf( '%02s', dechex( $index ) ) );
            $i++;
        }

        $pending_chars = $cols - ( strlen( $output ) % $cols ) - 2;
        if ( $pending_chars > 0 )
        {
            $output .= strtoupper( sprintf( '%02s', dechex( $key ) ) );
        }
        while( $pending_chars-- > 0 )
        {
            $output .= strtoupper( dechex( rand( 0, 15 ) ) );
        }
        $data = chunk_split( $output, $cols, "\n" );
        return $data;
    }
}
