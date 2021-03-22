<?php
namespace ChemCore;
use Doctrine\DBAL;
use Doctrine\Common;
use Monolog\Logger;
use TheCodingMachine\TDBM;
use APIconfig\bundleConfig;
/**
* Chemistry
 */
class chemistry
{
    public $config;
    public $tdbmService;

    function __construct(startup $config, bool $loadOnInit = true){
        // Store config locally
        $this->config = $config;
                
        $this->DefineConstants($this->config->settings);

        if(is_null(PROJECT_NAMESPACE) || empty(ENV_DETAILS_PATH) || ENV_DETAILS_PATH == null) {
            $this->result = new Result('A project namespace or environment details path has not been included in the configuration. '.
                ' Please see the getting started guide to set this up.(TODO: add a link to github when you get the page setup lol)');
            $this->printResult($this->result);
            throw new \Throwable('FATAL CHEMISTRY APPLICATION ERROR :: - \nThe expected PROJECT_NAMESPACE or ENV_DETAILS_PATH were not located in defined constants scope.');
        }
        // Parse Configuration file        
        $this->putEnvVars(parse_ini_file(ENV_DETAILS_PATH));
        // Start TDBM services
        $this->tdbmService = $this->createTDBMService();
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
    private function createTDBMService()
    {
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

        $cache = new Common\Cache\ArrayCache();

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
        return new TDBM\TDBMService($configuration);
    }

    public function rebuildModel(){
        try {
            $this->tdbmService->generateAllDaosAndBeans();
            return new Result("\n\tAll Daos and Beans generated from " . $_ENV['myDB'] . 
                        " for the '". PROJECT_NAMESPACE ."' namespace. ", 'Succeeded');
        } catch (\Throwable $th) {
            return new Result("$th", "Failed");
        }
    }
    
    public function printResult(...$args)
    {
        // Gurantee a response, even to say there was no reaction
        $this->overload($args, [
            function (Result $result)
            {
                echo (string)$result->body;
            },
            function (){
                return $this->printResult(new Result(['request'=> 'failed', 'message'=> 'Try again'], 400));
            },
            function (object $object){
                return $this->printResult(new Result($object, 200));
            },
            function ($whatever){
                return $this->printResult(new Result(['request'=> 'success', 'message'=> $whatever], 200));
            }
        ]);
    }
}
