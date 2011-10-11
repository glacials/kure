<?php

class EntryHandler {

  private $entries;

  // Returns the next entry in the queue
  public function getNext() {

    if(!isset($this->entries[0]))
      return false;

    return array_shift($this->entries);

  }

}
