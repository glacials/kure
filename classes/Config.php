<?php

class Config {
	
	private $version;
	
	private $admin_password;
	private $show_admin_link;
	
	private $blog_name;
	private $blog_sub;
	
	private $template;
	
	private $entries_per_page;
	
	private $abc_entries;
	
	public function __get($variable) {
		return $this->$variable;
	}
		
	public function __set($variable, $value) {
		$this->$variable = $value;
	}
	
	// Writes the current configuration to file.
	// Returns true on success; false otherwise.
	public function save() {
		
		$write = '<?php' . "\n\n";
		
		// Reflections let us see the names of class properties
		$reflection = new ReflectionClass('Config');
		
		foreach($reflection->getProperties() as $property) {
			
			$property_name = $property->getName();
			
			$write .= '$this->' . $property_name . ' = ';
			$write .= self::variable_to_string($this->$property_name) . ';' . "\n";
			
		}
		
		$write .= "\n" . '?>' . "\n";
		return (boolean)(file_put_contents('config.php', $write));
		
	}
	
	public function load() {
		
		include 'config.php';
		return $this;
		
	}
	
	public static function variable_to_string($var) {
		
		if($var == 'true' || $var == 'false')
			return $var;
		if(is_bool($var))
			return $var ? 'true' : 'false';
		if(is_string($var))
			return '\'' . $var . '\'';
		return $var;
		
	}
	
};

?>
