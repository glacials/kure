<?php

class Config {
	
	private $section;
	private $vars;
	
	public function __construct($section) {
		$this->section = $section;
	}
	
	public function __get($variable) {
		
		if(!isset($this->vars[$variable]))
			throw new PropertyDoesNotExistException('Sorry, I couldn\'t find a config var named <tt>' . $variable . '</tt>.');
		
		return $this->vars[$variable];
		
	}
	
	public function load() {
		
		$config_vars = parse_ini_file('config.ini', true);
		
		foreach($config_vars[$this->section] as $config_key => $config_val)
			$this->vars[$config_key] = $config_val;
		
		return $this;
		
	}
	
}

?>
