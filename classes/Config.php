<?php

class Config {
	
	private $file;
	private $section;
	private $vars;
	
	public function __construct($file = 'config.php', $section = 'kure') {
		
		$this->file = $file;
		$this->section = $section;
		
	}
	
	public function __get($variable) {
		
		if(!isset($this->vars[$variable]))
			throw new PropertyDoesNotExistException('Sorry, I couldn\'t find a config variable called <tt>' . $variable . '</tt>.');
		
		return $this->vars[$variable];
		
	}
	
	public function load() {

		if(!file_exists($this->file))
			throw new CannotFindFileException($this->file);
		
		$config_vars = parse_ini_file($this->file, true);
		
		if(!$config_vars)
			throw new CannotReadFileException($this->file);
		
		foreach($config_vars[$this->section] as $config_key => $config_val)
			$this->vars[$config_key] = $config_val;
		
		return $this;
		
	}
	
}

?>
