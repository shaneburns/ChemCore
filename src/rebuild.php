<?php
namespace ChemCore;

class Rebuild{
    private $settings;
    public function __construct(array $settings) {
        $this->settings = $settings;
        $this->config = new startup($this->settings);
        $this->chem = new chemistry($this->config);
	$this->chem->rebuild();
    }
}