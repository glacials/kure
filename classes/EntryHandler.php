<?php

class EntryHandler {
	
	private $entries;
	private $num_entries;
	
	// Page 0 is the first page
	public function __construct($page, $limit) {
		
		// This constructor can be called with just one argument: an entry filename.
		if(func_num_args() == 1) {
			
			$entry_filename = array_pop(func_get_args());
			
			$entry_file = 'entries/' . $entry_filename . '.txt';
			
			if(file_exists($entry_file))
				$this->entries[0] = self::entry_from_file($entry_file);
			
			$this->num_entries = 1;
			
		} else {
			
			$config = Engine::get_config();
			
			$start_post = $limit * $page;
			$end_post   = $start_post + $limit - 1;
			
			$entry_files = glob('entries/*.txt');
			
			foreach(array_slice($entry_files, $page * $limit, $limit) as $file)
				$this->entries[] = self::entry_from_file($file);
			
			if(!$config->abc_entries && is_array($this->entries))
				usort($this->entries,
					function($entry_a, $entry_b) {
						if($entry_a->timestamp == $entry_b->timestamp) return 0;
						return $entry_a->timestamp > $entry_b->timestamp ? -1 : 1;
					}
				);
			
			$this->num_entries = count($this->entries);
			
		}
		
	}
	
	public function __get($variable) {
		
		if($variable == 'num_entries')
			return $this->num_entries;
		
		throw new PropertyAccessException('I will not return property <tt>$' . $variable . '</tt>.');
		
	}
		
	private static function entry_from_file($file) {
		
		$title     = deparse_title(str_replace(array('entries/', '.txt'), '', $file));
		$content   = file_get_contents($file);
		$timestamp = filemtime($file);
		
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
