kizilare-framework
==================

ini_set('display_errors', true);
error_reporting(E_ALL);

set_error_handler ( array( 'Kizilare\Framework\ErrorHandler', 'error' ) );
Kizilare\Framework\App::getInstance()->run();
