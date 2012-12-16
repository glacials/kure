<?php

class Config {

  private $file;
  private $section;
  private $vars;

  public function __construct($section = 'kure', $file = 'config.php') {

    $this->section = $section;
    $this->file = $file;

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

  /*
   * Sets config variable $variable to $value without writing to file.
   *
   * Warning: This override will only last until end of page! No config
   * variables in-file will actually be changed. This is used for things like
   * changing the blog subtitle on a page that could use a title there (the
   * admin plugin does this).
   */
  public function set($variable, $value) {

    $this->vars[$variable] = $value;

  }

}

?>
