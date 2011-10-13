<?php

class Engine {
	
	private static $config;
	
	public static function error($message = '') {
		print '<span class="error">' . $message . '</span>';
	}
	
	// Exits all PHP processing on-the-spot and spits error message $error.
	public static function quit($message = '') {
		
		self::error($message);
		Template::run("footer", array());
		exit();
		
	}
	
	public static function get_config() {
		
		if(self::$config == null) {
			
			self::$config = new Config();
			self::$config->load();
			
		}
		
		return self::$config;

	}
	
	// Returns the size of the longest string in $strings
	public static function strlen_array($strings) {
		
		$longestStrings = array_keys(array_combine($array, array_map('strlen', $array)), max($mapping));
		return strlen($logestStrings);
		
	}
	
	public static function init_plugins() {
		
		$plugging = false;
		$rac[] = true; // mockup array so all our foreach()s don't fail if we don't find plugins
		
		if(file_exists(KURE_ROOT . 'plugins/')) { // plugins dir is optional
			
			$findmods = glob($root . 'plugins/*.php');
			
			// intialize our arrays so array_merge_recursive won't fail if they are not arrays by that time
			if(count($findmods) != 0) {
				
				$rac = array();
				$rack = array();
				
				foreach($findmods as $weight => $pluginfile) {
					
					include($pluginfile); // read the plugin ($rack will get set by the plugin during this time)
					$rack = add_dimension($rack, $pluginfile); // turns $rack['posts']['post-body_after'] into $rack['posts']['post-body_after']['pluginfilename.php']
					$rac = array_merge_recursive($rac, $rack); // merge with all other plugins
					unset($rack); // remove all entries from $rack so that they don't overflow into the next plugin's array
					
				}
				
			}
			
		}
		
	}

function plug($page, $hook, $id = false) {
	
	$GLOBALS['plugging'] = true;
	
	$output = '';
	
	if(isset($GLOBALS['rac'][$page][$hook])) {
		
		foreach($GLOBALS['rac'][$page][$hook] as $file => $html) {
			
			include($file);
			$output .= ($rack[$page][$hook]); // print refreshed html
			
		}
		
	}
	
	if($id)
		$output .= Engine::plug($page, $hook . '#' . $id); // dynamic plug
	
	$GLOBALS['plugging'] = false;
	
	return $output;
	
}


	
};

?>
