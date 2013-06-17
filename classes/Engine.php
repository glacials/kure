<?php

class Engine {

  private static $config;
  private static $language;

  /*
   * Spits error message $error, and prints a monospaced blockquote containing
   * $information if given.
   *
   * $error should be a general error message describing the operation that
   * failed (e.g. "Could not read entry."), and $information (if needed) should
   * be situation-specific information about the error (e.g. the filename of the
   * entry that failed to print).
   *
   * In general, when using exceptions, $error should be a message that is
   * determined by what type of exception was caught, and $information should
   * be information that was thrown to the catch block via the 'message' of the
   * exception ($e->getMessage()).
   */
  public static function error($message, $information = false) {

    print '<span class="error">' . $message . '</span>' . "\n";

    if($information)
      print '<blockquote><tt>' . $information . '</tt></blockquote>' . "\n";

  }

  /*
   * Spits error message $error if given, prints a monospaced blockquote
   * containing $information if given, runs the footer template, then quits.
   *
   * @uses Engine::error()
   */
  public static function quit($message = false, $information = false) {

    if($message)
      self::error($message, $information);

    Template::run('footer', array());
    exit();

  }

  public static function get_config() {

    if(self::$config == null) {

      self::$config = new Config();
      self::$config->load();

    }

    return self::$config;

  }

  public static function get_language() {

    if(self::$language == null) {

      self::$language = new Language(KURE_ROOT . 'languages/' . self::get_config()->language . '.ini', 'kure');
      self::$language->load();

    }

    return self::$language;

  }

  // Returns the size of the longest string in $strings
  public static function strlen_array($strings) {

    $longestStrings = array_keys(array_combine($array, array_map('strlen', $array)), max($mapping));
    return strlen($logestStrings);

  }

  public static function init_plugins() {

    $GLOBALS['plugging'] = false;
    $GLOBALS['rac'] = array(); // mockup array so all our foreach()s don't fail if we don't find plugins

    if(file_exists(KURE_ROOT . 'plugins/')) { // plugins dir is optional

      $findmods = glob(KURE_ROOT . 'plugins/*.php');

      // intialize our arrays so array_merge_recursive won't fail if they are not arrays by that time
      if(count($findmods) != 0) {

        $rack = array();

        foreach($findmods as $weight => $pluginfile) {

          include($pluginfile); // read the plugin ($rack will get set by the plugin during this time)
          $rack = add_dimension($rack, $pluginfile); // turns $rack['posts']['post-body_after'] into $rack['posts']['post-body_after']['pluginfilename.php']
          $GLOBALS['rac'] = array_merge_recursive($GLOBALS['rac'], $rack); // merge with all other plugins
          unset($rack); // remove all entries from $rack so that they don't overflow into the next plugin's array

        }

      }

    }

  }

  function plug($page, $hook, $id = false) {

    $GLOBALS['plugging'] = true;
    $output = '';

    if(isset($GLOBALS['rac'][$page][$hook])) {
      foreach($GLOBALS['rac'][$page][$hook] as $file => $html) {
        include($file);
        $output .= $rack[$page][$hook]; // print refreshed html
      }
    }

    if($id)
      $output .= Engine::plug($page, $hook . '#' . $id); // dynamic plug

    $GLOBALS['plugging'] = false;
    return $output;

  }

}

?>
