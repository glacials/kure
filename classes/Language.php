<?php

class Language extends Config {
	
	public function __construct($file = 'languages/en.ini', $section = 'kure') {
		parent::__construct($file, $section);
	}
	
}

?>
