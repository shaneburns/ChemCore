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
    public $creds;
    public $tdbmService;

    function __construct(array $settings)
    {
        // Default vnvironment vars setup
        $stdSettings = array(
            "dr" => $_SERVER['DOCUMENT_ROOT'],
            "ds" => DIRECTORY_SEPARATOR
        );
        
        $settings = array_merge($stdSettings, $settings);
        $this->DefineConstants($settings);

        if(is_null(PROJECT_NAMESPACE) || empty(ENV_DETAILS_PATH) || ENV_DETAILS_PATH == null) die();//new Result();
        
        // Parse Configuration file        
        $this->putEnvVars(parse_ini_file(ENV_DETAILS_PATH));
        // Start TDBM services
        $this->startTDBMService();
    }
    public function putEnvVars(array $vars)
    {
        foreach($vars as $key => $val) $_ENV[$key] = $val;
    }
    public function DefineConstants(array $varsToAdd){
        foreach($varsToAdd as $var => $val ){
            if(!defined($var)){
                define($var, $val);
            }else{

            }
        }
    }
    private function startTDBMService(){
        $config = new DBAL\Configuration();

        $connectionParams = array(
            'user' => $_ENV['username'],
            'password' => $_ENV['password'],
            'host' => $_ENV['servername'],
            'driver' => $_ENV['driver'],
            'dbname' => $_ENV['myDB'],
        );

        $dbConnection = DBAL\DriverManager::getConnection($connectionParams, $config);

        // The bean and DAO namespace that will be used to generate the beans and DAOs. These namespaces must be autoloadable from Composer.
        $beanNamespace = PROJECT_NAMESPACE . '\\Beans';
        $daoNamespace = PROJECT_NAMESPACE . '\\Daos';

        $cache = new Common\Cache\ApcuCache();

        $logger = new Logger('cantina-app'); // $logger must be a PSR-3 compliant logger (optional).

        // Let's build the configuration object
        $configuration = new TDBM\Configuration(
            $beanNamespace,
            $daoNamespace,
            $dbConnection,
            null,    // An optional "naming strategy" if you want to change the way beans/DAOs are named
            $cache,
            null,    // An optional SchemaAnalyzer instance
            $logger, // An optional logger
            []       // A list of generator listeners to hook into code generation
        );

        // The TDBMService is created using the configuration object.
        $this->tdbmService = new TDBM\TDBMService($configuration);
    }
}
