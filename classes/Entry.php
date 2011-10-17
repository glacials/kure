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
	
}

?>
