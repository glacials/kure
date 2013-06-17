<?php

class EntryHandler {

  private $entries;
  private $num_entries;
  private $total_entries;

  /**
   * Takes either one or two arguments.
   *
   * If one, the argument must be the modified title, without path or extension,
   * of a single entry to be displayed, i.e. the value of $_GET['e'].
   *
   * If two, the first argument must be the page the user is on, and the second
   * must be the number of entries to be displayed.
   */
  public function __construct(/* <title> | <page, num_entries_to_display> */) {

    if(func_num_args() == 1) {

      $entry_filename = func_get_arg(0);
      $entry_title = deparse_title($entry_filename);

      foreach(glob("entries/*.txt") as $file) {
        $entry = self::entry_from_file($file);
        if($entry->title == $entry_title)
          $this->entries[0] = $entry;
      }

      if(count($this->entries) == 0)
        throw new CannotFindFileException($entry_filename);

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
      if($limit != 0)
        $this->entries       = array_slice($this->entries, $page * $limit, $limit);
      $this->num_entries   = count($this->entries);

    }

  }

  public function __get($variable) {

    if($variable == 'num_entries' || $variable == 'total_entries')
      return $this->$variable;

    throw new PropertyAccessException('$' . $variable);

  }

  private static function entry_from_file($file) {

    $content = file_get_contents($file);

    // Gather JSON, if the entry has any
    $pattern = "/{(.*)}/";
    $matches = array();
    preg_match($pattern, $content, $matches);

    if(isset($matches[0])) {

      $json = json_decode($matches[0]);
      // Remove the parsed JSON from the entry content
      $content = str_replace($matches[0], "", $content);

    }

    // If there's a JSON title use it, else use the filename
    if(isset($json->title))
      $title = $json->title;
    else
      $title = deparse_title(str_replace(array('entries/', '.txt'), '', $file));

    // If there's a JSON date use it, else use the file modification time
    if(isset($json->date))
      $timestamp = strtotime($json->date);
    else
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
