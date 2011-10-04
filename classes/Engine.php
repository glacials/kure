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

class Engine {

  public static function error($message = '') {

    print '<span class="error">' . $message . '</span>';

  }

  // Exits all PHP processing on-the-spot and spits error message $error.
  public static function quit($message = '') {

    self::error($message);
		Template::run("footer", array());
    exit();

  }

  // Returns the size of the longest string in $strings
  public static function strlen_array($strings) {

        $longestStrings = array_keys(array_combine($array, array_map('strlen', $array)), max($mapping));
        return strlen($logestStrings);

  }

};

?>
