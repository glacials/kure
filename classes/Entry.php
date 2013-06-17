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
    $config = Engine::get_config();
    if($variable == 'content' && $config->markdown)
      return Markdown($this->content);
    elseif($variable == 'raw_content')
      return $this->content;
    return $this->$variable;
  }

  /*
   * Writes the constructed entry to file.
   */
  public function write() {

    $file = KURE_ROOT . 'entries/' . $this->title . '.txt';
    $file = str_replace(' ', '-', $file);

    return file_put_contents($file, $this->content)
        && touch($file, $this->timestamp);

  }

}

?>
