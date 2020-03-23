<?php
namespace ChemCore;

class Models{
    public $tdbmService;
    function __construct($tdbmService){
        if($tdbmService === null) die();
        $this->tdbmService = $tdbmService;
    }
}
