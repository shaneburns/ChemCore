<?php
namespace ChemCore;
use Doctrine\DBAL;
use Doctrine\Common;
use Monolog\Logger;
use TheCodingMachine\TDBM;
use APIconfig\bundleConfig;
/**
 * Startup
 */
class startup
{
    public $settings;
    private $stdSettings;

    function __construct(array $settings)
    {
        // Default vnvironment vars setup
        // Add more stuff to fluff out the configuration here
        $this->stdSettings = array(
            "dr" => $_SERVER['DOCUMENT_ROOT'],
            "ds" => DIRECTORY_SEPARATOR
        );
        
        $this->settings = array_merge($stdSettings, $settings);
    }
}
