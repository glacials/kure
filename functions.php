<?php

// if php version is low enough, simulate a file_put_contents function
if(!function_exists('file_put_contents')) {

  define('FILE_APPEND', 1);

  function file_put_contents($n, $d, $flag = false) {

    $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
    $f = @fopen($n, $mode);

    if ($f === false) {

      return 0;

    } else {

      if (is_array($d))
        $d = implode($d);

      $bytes_written = fwrite($f, $d);
      fclose($f);
      return $bytes_written;

    }

  }

}

// if php version is low enough, simlate a file_get_contents function
if(!function_exists('file_get_contents')) {

  function file_get_contents($filename) {

    return implode("\n", file($filename));

  }

}

// adds dimension $dimension to each element in array $array, e.g. $x['a']['b'] = y; becomes $x['a']['b']['c'] = y;
function add_dimension($array, $dimension) {

  if(!is_array($array))
    return array($dimension => $array); // base case

  foreach($array as $key => $val)
    $array[$key] = add_dimension($val, $dimension);

  return $array;

}

// returns a parsed version of $string for use in entry content; removes any php code and applies bbcode
function parse($string) {

  $string = str_replace('<?', '&lt;?', $string);
  $string = str_replace('?>', '?&gt;', $string);
  $string = str_replace('\\', '',      $string);

  // start bbcode
  // see http://www.think-ink.net/html/bold.htm
  // for why we use <strong> and <em> instead of <b> and <i>
  $bb_find = array(
  '/\[b\](.*?)\[\/b\]/is',
  '/\[i\](.*?)\[\/i\]/is',
  '/\[u\](.*?)\[\/u\]/is',
  '/\[url\=(.*?)\](.*?)\[\/url\]/is',
  '/\[url\](.*?)\[\/url\]/is',
  '/\[img\](.*?)\[\/img\]/is',
  );

  $bb_replace = array(
  '<strong>$1</strong>',
  '<em>$1</em>',
  '<u>$1</u>',
  '<tt><a href="$1" class="content">$2</a></tt>',
  '<tt><a href="$1" class="content">$1</a></tt>',
  '<img src="$1"/>',
  );

  $string = preg_replace($bb_find, $bb_replace, $string);
  // end bbcode

  return $string;

}

// returns a parsed version of $string for use as an entry filename
function parse_title($string) {

  $string = parse($string);

  if(strpos($string, '_') ||
     strpos($string, '/') ||
     strpos($string, '\\')||
     strpos($string, '|') ||
     strpos($string, ':') ||
     strpos($string, '*') ||
     strpos($string, '?') ||
     strpos($string, '\'')||
     strpos($string, '<') ||
     strpos($string, '>') ||
     strpos($string, '../')
    )
    Engine::quit('Invalid characters in title.');

  // Convert hyphens to underscores, spaces to hyphens, and remove bad things
  $string = str_replace('-', '_',  $string);
  $string = str_replace(' ', '-',  $string);
  $string = str_replace('../', '', $string);

  return $string;

}

// inverse function of parse_title()
function deparse_title($string) {

  $string = str_replace('-', ' ', $string);
  $string = str_replace('_', '-', $string);

  return $string;

}

// cleans $string of any attempts to access things it shouldn't
function sanitize($string) {

  $string = str_replace('../', '', $string);
  $string = htmlspecialchars($string);
  return $string;

}

function compare_entries($entry_a, $entry_b) {

  if($entry_a->timestamp == $entry_b->timestamp)
    return 0;

  return $entry_a->timestamp > $entry_b->timestamp ? -1 : 1;

}

?>
