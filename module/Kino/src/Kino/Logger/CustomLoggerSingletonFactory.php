<?php
namespace Kino\Logger;

use Zend\Log\Logger;
define("FNAME_CUSTOM_LOG", "custom.log");

final class CustomLoggerSingletonFactory extends Logger
{

    static $theUniqueLogger = null;

    /**
     * Access the singleton
     *
     * @return CustomLoggerSingletonFactory
     */
    public static function Logger()
    {
        if (! isset($theUniqueLogger) || $theUniqueLogger === null) {
            $theUniqueLogger = new CustomLoggerSingletonFactory();
        }
        
        return $theUniqueLogger;
    }

    public function __construct()
    {
        parent::__construct();
        
        $logDir = $_SERVER['DOCUMENT_ROOT'] . '/..' . '/log/';
        
        // check if the log dir exists, if not - create one
        if (! file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logWriteStream = fopen($logDir . constant("FNAME_CUSTOM_LOG"), "w+", false);
        
        $this->addWriter('stream', null, array(
            'stream' => $logWriteStream
        ));
        
        $this->debug("Logger successfully configured with custom output file '" . constant("FNAME_CUSTOM_LOG") . "'.");
    }
}

?>