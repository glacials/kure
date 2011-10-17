<?php

/* LICENSE INFORMATION
 * kure is distributed under the terms of the GNU General Public License
 * (http://www.gnu.org/licenses/gpl.html).
 * kure Copyright 2007-2011 Ben Carlsson
 * 
 * This file is part of kure.
 * 
 * kure is free software: you can redistribute it and/or modify it under the terms of the 
 * GNU General Public License as published by the Free Software Foundation, either version
 * 3 of the License, or (at your option) any later version.
 * 
 * kure is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with kure.
 * If not, see <http://www.gnu.org/licenses/>.
 */

/***** FILE HANDLING ********************************************************************/

function sort_by_mtime($entry1, $entry2) {

  $time1 = $entry1->timestamp;
  $time2 = $entry2->timestamp;

  if ($time1 == $time2)
    return 0;

  return ($time1 < $time2) ? 1 : -1;

}

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

// returns a format of $var friendly to writing into a config file (as a variable name or key)
function config_key($var) {

  $disallowed = array(
    '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '=', '+', '\t', '[',
    '{', ']', '}', '\\', '|', ';', ':', '\'', '\n', ',', '<', '.', '>', '/', '?', '"'
  );

  $var = str_replace($disallowed, '', $var);
  return $var;

}

// returns a format of $var friendly to writing into a config file (as a variable value), e.g.
// if $var is a boolean false, "false" (a string) will be returned;
// if $var is a string "banana", "\"banana\"" will be returned;
// if $var is an int 71, 71 will be returned (no change), etc.
// note that passing this function "false" (string) is the same as passing it false (boolean),
// likewise passing it "14" (string) is the same as passing it 14 (int)
function config_value($var) {

 // if it's a boolean, make it a string so it will write as a boolean
 if(is_bool($var) || $var === 'true' || $var === 'false') {

    if($var === 'true' || $var === 'false')
      return $var;
    else
      return ($var ? 'true' : 'false');

  }

  // if it only contains numeric digits, return its int value
  if(ctype_digit($var))
    return (int)$var;

  // if it's a number but contains more than just numeric digits, return its double value
  if(is_numeric($var))
    return (double)$var;

  // if it's a string, surround it with quotes
  if(is_string($var))
    return '"' . $var . '"';

  exit('<span class="error">Invalid datatype: <tt>' . write_value($var) . '</tt></span>');

}

// creates entry with title $title, content $content, with creation time $time
// returns true on success. exit()s otherwise.
function create_entry($title, $content) {

  global $root;

  $title = parse_title($title);
  $content = parse($content);

  if(file_exists($root . 'entries/' . $title . '.txt'))
    Engine::quit('<span class="error">An entry with that name already exists.</span>');
  elseif(!file_put_contents($root . 'entries/' . $title . '.txt', $content))
    Engine::quit('Could not create file <tt>entries/' . $title . '.txt</tt>. Check permissions and try again.<br/><br/>It is also possible that you used an invalid character in your title (titles are used as filenames).</span>');

	// Set creation time
	touch($root . 'entries/' . $title . '.txt', time());

  return true;

}

// attempts to delete entry $title
// returns true on success; false otherwise
function delete_entry($title) {

  global $root;

  $title = str_replace('../', '', $title);
  $title = str_replace('/', '', $title);
  $title = str_replace('\\', '', $title);
  $title = str_replace(' ', '_', $title);
  
  return unlink($root . 'entries/' . $title . '.txt');

}

// adds dimension $dimension to each element in array $array, e.g. $x['a']['b'] = y; becomes $x['a']['b']['c'] = y;
function add_dimension($array, $dimension) {

  if(!is_array($array))
    return array($dimension => $array); // base case

  foreach($array as $key => $val)
    $array[$key] = add_dimension($val, $dimension);

  return $array;

}

// writes all values in array $config to /plugins/config/$plugin.php with keys as variable names
// this function will OVERWRITE the whole file and start clean each time it is called,
// writing only the variables that it is given. this means it must be passed ALL variables every time.
// note that if passed string "true" or "false" it will convert to a boolean value, otherwise datatype will be preserved
// returns true on success; false otherwise
function set_config($plugin, $config) {

  global $root;

  $plugin = config_key($plugin);

  if(!file_exists($root . 'plugins/config'))
    mkdir($root . 'plugins/config');

  $write = '<?php\n\n// plugin config file for $plugin\n// autogenerated by kure\n\n';

  foreach($config as $option => $value)
    $write .= '$' . $plugin . '[\'' . $option . '\'] = ' . config_value($value) . ';\n';

  if(!file_put_contents('plugins/config/' . $plugin . '.php', $write))
    return false;

  return true;

}

// returns an array containing all config variables for plugin $plugin,
// with variable names as the key and variable values as the value
function get_config($plugin) {

  $plugin = config_key($plugin);
  include('plugins/config/' . $plugin . '.php');

  return $$plugin; // variable variables: http://php.net/manual/en/language.variables.variable.php

}

// returns a parsed version of $string for use in entry content; removes any php code and applies bbcode
function parse($string) {

  $string = str_replace('<?', '&lt;?', $string);
  $string = str_replace('?>', '?&gt;', $string); // Please ignore this comment; it is meant to fix broken syntax coloring in some editors caused by this line. <?php
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

?>
