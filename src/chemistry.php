<?php
namespace ChemCore;
/**
* Chemistry
 */
class chemistry
{
    public $config;

    function __construct(startup $config, bool $loadOnInit = true){
        // Store config locally
        $this->config = $config;
    }

    public function rebuild()
    {
        
        echo "\n\n\n\n\n\n\n\n\n\n\n\nAre you sure you want to rebuild your DB models?  [y,n]: ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) != 'y'){
            echo "\n\n‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼\nAbort rebuild!\n‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼\n\n\n";
            exit;
        }
        echo "\n";
        echo "Rebuilding...⏳\n";

        try {
            $this->config->tdbmService->generateAllDaosAndBeans();
            echo "\nRebuild Status: Succeeded 👌 \n\tAll Daos and Beans generated from " . $_ENV['myDB'] . " for the '". PROJECT_NAMESPACE ."' namespace. \n\tGet at it!";
        } catch (\Throwable $th) {
            echo "\nRebuild Status: Failed ❌ \n";
            echo $th;
        }
        echo "\n\n\n";
    }
}
