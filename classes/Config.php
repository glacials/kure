<?php

class Config {
	
	private $file;
	private $section;
	private $vars;
	
	public function __construct($file, $section) {
		
		$this->file = $file;
		$this->section = $section;
		
	}
	
	public function __get($variable) {
		
		if(!isset($this->vars[$variable]))
			throw new PropertyDoesNotExistException('Sorry, I couldn\'t find a config variable called <tt>' . $variable . '</tt>.');
		
		return $this->vars[$variable];
		
	}
	
	public function load() {
		
		$config_vars = parse_ini_file($this->file, true);
		
		if(!$config_vars)
			throw new CouldNotReadFileException('Sorry, I couldn\'t read the config file <tt>' . $this->file . '</tt>.');
		
		foreach($config_vars[$this->section] as $config_key => $config_val)
			$this->vars[$config_key] = $config_val;
		
		return $this;
		
	}
	
}

?>
