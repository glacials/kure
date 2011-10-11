<?php

class PostHandler extends EntryHandler {

	// Page 0 is the first page
	public function __construct($page) {
		
		$start_post = Engine::get_config()->posts_per_page * $page;
		$end_post	 = $start_post + Engine::get_config()->posts_per_page - 1;
		
		$this->entries = glob('posts/*.txt');
		
		if(!$this->entries)
			$this->entries = array();
		
		foreach(array_slice($this->entries, $page * Engine::get_config()->posts_per_page) as $filename)
			$entries[] = new Post($filename, file_get_contents($filename));
		
	}

}

?>
