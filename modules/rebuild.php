<?php
namespace ChemCore\Modules;

class Rebuild{
    private $settings;
    private $config;
    private $chem;
    public function __construct(array $settings) {
        $this->settings = $settings;
        $this->config = new \ChemCore\startup($this->settings);
        $this->chem = new \ChemCore\chemistry($this->config);
        $this->rebuild();
        exit;
    }

    public function rebuild()
    {
        
        echo "\n\n\n\n\n\n\n\n\n\n\n\n".
             "Are you sure you want to rebuild your DB models?  [y,n]: ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        if(trim($line) != 'y'){
            echo "\n\n‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼\n".
                 "Abort rebuild!".
                 "\n‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼‼\n\n\n";
            exit;
        }
        echo "\nRebuilding...⏳\n";

        try {
            $result = $this->chem->rebuildModel();
            echo "\nRebuild Status: $result->status 👌 ".
                 $result->body .
                 "\n\tGet at it!";
        } catch (\ChemCore\Result $rseult) {
            echo "\nRebuild Status: $result->status ❌ \n" .
                 $rseult->body.
                 "\n\tSorry bro...";
        }
        echo "\n\n\n";
    }
}