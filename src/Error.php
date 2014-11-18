<?php
namespace Kizilare\Framework;

class Error
{
    /**
     * Handlers errors in the app.
     *
     * @static
     * @param string $type Contains the message of the error.
     * @param string $message Contains the message of the error.
     * @param string $file The filename that the error was raised in.
     * @param integer $line The line number the error was raised at.
     */
    static function error( $type, $message, $file, $line )
{
    $error = <<<ERROR
<pre>
<a href="corebrowser:$file:$line">$file:$line</a>
#$type $message
</pre>
ERROR;
    if ( $type !== E_NOTICE )
    {
        die( $error );
    }
}
}