<?php

class Entry {
	
	private $filename;
	private $title;
	private $content;
	private $timestamp;
	
	public function __construct($title, $content, $timestamp) {
		
		$this->filename  = parse_title($title);
		$this->title     = $title;
		$this->content   = $content;
		$this->timestamp = $timestamp;
		
	}
		
	public function __get($variable) {
		return $this->$variable;
	}

	/*
	 * Writes the constructed entry to file.
	 */
	public function write() {
		
		return file_put_contents(KURE_ROOT . 'entries/' . $this->title . '.txt', $this->content)
		   and touch(KURE_ROOT . 'entries/' . $this->title . '.txt', $this->timestamp);
		
	}
	
}

?>
