<?php

class PostHandler extends EntryHandler {

  // Page 0 is the first page
  public function __construct($page) {

    $startPost = Config::get('postsPerPage') * $page;
    $endPost   = $startPost + Config::get('postsPerPage') - 1;

    foreach(array_slice(glob('posts/*.txt'), $page * Config::get('postsPerPage')), $page * Config as $filename)
      $entries[] = new Post($filename, file_get_contents($filename));

  }

}

?>
