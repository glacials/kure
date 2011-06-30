<?php

class DocHandler extends EntryHandler {

  public function __construct() {

    foreach(glob('docs/*.txt') as $filename)
      $entries[] = new Doc($filename, file_get_contents($filename));

  }

}

?>
