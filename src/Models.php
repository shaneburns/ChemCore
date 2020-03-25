<?php
namespace ChemCore;

class models{
    public $tdbmService;
    function __construct($tdbmService){
        if($tdbmService === null) die();
        $this->tdbmService = $tdbmService;
    }
}
