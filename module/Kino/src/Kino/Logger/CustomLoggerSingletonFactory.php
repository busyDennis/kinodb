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
        
        // stream # 1: writing to an ad-hoc log file 'log/custom.log' for quick feedback in Eclipse IDE
        $this->addWriter('stream', null, array(
            'stream' => $logWriteStream
        ));
        
        // stream # 2: writing to php://stderr
        $this->addWriter('stream', null, array(
            'stream' => fopen('php://stderr', 'w')
        ));
        
        $this->debug("Logger successfully configured with custom output file '" . constant("FNAME_CUSTOM_LOG") . "'.");
    }
}

?>