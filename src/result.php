<?php
namespace ChemCore;

class Result  
{
    public $body;
    public $status;

    public function __construct($body,$status = 'nuetral')
    {
        $this->body = $body;
        $this->status = $status;
    }
}
