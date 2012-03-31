<?php

class EntryHandler {
	
	private $entries;
	private $num_entries;
	private $total_entries;
	
	/**
	 * Takes either one or two arguments.
	 * 
	 * If one, the argument must be the filename, without path or extension, of
	 * a single entry to be displayed.
	 * 
	 * If two, the first argument must be the page the user is on, and the second
	 * must be the number of entries to be displayed.
	 */
	public function __construct() {
		
		if(func_num_args() == 1) {
			
			$entry_filename = func_get_arg(0);
			
			$entry_file = 'entries/' . $entry_filename . '.txt';
			
			if(file_exists($entry_file))
				$this->entries[0] = self::entry_from_file($entry_file);
			else
				throw new CannotFindFileException($entry_file);
			
			$this->num_entries = 1;
			
		} else {
			
			$page  = func_get_arg(0);
			$limit = func_get_arg(1);
			
			$config = Engine::get_config();
			
			foreach(glob('entries/*.txt') as $file)
				$this->entries[] = self::entry_from_file($file);
			
			if(is_array($this->entries))
				usort($this->entries, 'compare_entries');
			else
				$this->entries = array();
			
			$this->total_entries = count($this->entries);
			$this->entries       = array_slice($this->entries, $page * $limit, $limit);
			$this->num_entries   = count($this->entries);
			
		}
		
	}
	
	public function __get($variable) {
		
		if($variable == 'num_entries')
			return $this->num_entries;
		
		if($variable == 'total_entries')
			return $this->total_entries;
		
		throw new PropertyAccessException('I will not return property <tt>$' . $variable . '</tt>.');
		
	}
		
	private static function entry_from_file($file) {
		
		$title     = deparse_title(str_replace(array('entries/', '.txt'), '', $file));
		$content   = file_get_contents($file);
		$timestamp = filemtime($file);
		
		if($content === false || !$timestamp)
			throw new CannotReadFileException($file);
		
		return new Entry($title, $content, $timestamp);
		
	}
	
	// Returns the next entry in the queue
	public function next() {
		return $this->has_next() ? array_shift($this->entries) : false;
	}
	
	public function has_next() {
		return isset($this->entries[0]);
	}
	
}

?>
