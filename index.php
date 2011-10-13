<?php

/* LICENSE INFORMATION
 * kure is distributed under the terms of the GNU General Public License
 * (http://www.gnu.org/licenses/gpl.html).
 * kure Copyright 2007-2011 Ben Carlsson
 * 
 * This file is part of kure.
 * 
 * kure is free software: you can redistribute it and/or modify it under the
 * terms of the * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * kure is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; * without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * kure. If not, see <http://www.gnu.org/licenses/>.
 * 
 * NOTES
 * kure is in BETA. Be aware that there may be BUGS and/or SECURITY HOLES
 * in the engine, and that you are using it at your own risk. Please be
 * cautious.
 * 
 * I graciously accept bug reports and suggestions for kure's engine. Visit
 * kure's repo at github.com/skoh-fley/kure. My email is breachreach@gmail.com
 * if you wish to reach me directly.
 *  
 * If you wish to remove the footer's "powered by kure" text, feel free. I
 * always appreciate recognition somewhere, but I'll let you decide what's fair.
 * :P
 */

// Tell all files to include relative to THIS FILE's directory
set_include_path(dirname($_SERVER['SCRIPT_FILENAME']));

// Autoload any class which is used in this file
function __autoload($class) {
	include 'classes/' . $class . '.php';
}

require_once 'functions.php';

if(!file_exists('config.php'))
	exit('<p>It looks like you haven\'t installed kure yet!<br/>Proceed to <a href="install.php">installation</a> if you need to install.<br/>If you don\'t, be sure to make sure your kure-related directories exist.</p>' . "\n");

Engine::init_plugins();
$config = Engine::get_config();

Template::run('header');

/***** Entry Viewer *****/
if(isset($_GET['entry'])) { // if a specific entry has been requested
		
	$filename = $_GET['entry'];

	$filename = sanitize($filename);
	print Engine::plug('entry', 'top');
	
	if(!file_exists('entries/' . $filename . '.txt')) {
		
		print 'The requested file <tt>entries/' . $filename . '.txt</tt> doesn\'t exist.';
		
	} else {
		
		$title   = str_replace('_', ' ', $filename);
		$file    = 'entries/' . $filename . '.txt';
		$content = str_replace('\n', '<br/>', file_get_contents($file));
		$date    = filemtime($file);

		$template_vars = array('ENTRYTITLE'   => $title,
		                       'ENTRYADDRESS' => $file,
		                       'ENTRYDAY'     => date('j', $date),
		                       'ENTRYMONTH'   => date('F', $date),
		                       'ENTRYYEAR'    => date('Y', $date),
		                       'ENTRYCONTENT' => $content
		                      );
		
		Template::run('entry', $template_vars);
		
	}
	
/***** Entry Listing (Home) *****/
} else {
	
	Template::run('entrylist_header');
	
	if(!isset($_GET['page']))
		$_GET['page'] = 1; // default to page 1
	
	$entries = glob('entries/*.txt');

	if(!$entries)
		Engine::quit('No entries to display.');
	
	$num_entries = count($entries);
	
	// if the total number of entries isn't divisible by the number we want to display,
	// then we want to make $num_entries / $config->entries_per_page round up one. (think it out.) this is for pagination.
	if($num_entries % $config->entries_per_page != 0)
		$num_entries += $config->entries_per_page;
	
	if(!$config->abc_entries) // if we're NOT sorting alphabetically
		usort($entries, 'sort_by_mtime');
	
	$first_entry_on_page = ($_GET['page'] * $config->entries_per_page) - $config->entries_per_page;
	$entry_offset = 0;
	$i = 0; // monitor how many entries we display
	
	foreach($entries as $entry) {
		
		if($i == $config->entries_per_page && $config->entries_per_page != 0)
			break;
		
		if(isset($_GET['page']) && ($entry_offset < $first_entry_on_page) || ($entry_offset > ($first_entry_on_page + $config->entries_per_page))) {
			
			$first_entry_on_page++;
			continue;
			
		}
		
		$title   = str_replace('_', ' ', $entry);
		$title   = str_replace('entries/', '', $title);
		$title   = str_replace('.txt', '', $title);
		$file    = $entry;
		$address = '?entry=' . str_replace('entries/', '', $entry);
		$address = str_replace('.txt', '', $address);
		$content = str_replace('\n', '<br/>\n', file_get_contents($file));
		$date    = filemtime($file);

		$template_vars = array('ENTRYTITLE'   => $title,
		                       'ENTRYADDRESS' => $address,
		                       'ENTRYDAY'     => date('j', $date),
		                       'ENTRYMONTH'   => date('F', $date),
		                       'ENTRYYEAR'    => date('Y', $date),
		                       'ENTRYCONTENT' => $content
		                      );
		
		Template::run('entry', $template_vars);
		
		$i++;
		
	}
	
	// Display "previous entries" / "more recent entries" links if necessary
	if($config->entries_per_page != 0 && $num_entries > $config->entries_per_page) {
		
		if($_GET['page'] + 1 <= $num_entries / $config->entries_per_page)
			print '<a class="navitem" href="?page=' . ($_GET['page'] + 1) . '"><font size="1">&lt;&lt;</font>previous entries</a>';
		
		if($_GET['page'] != 1) {
			
			$next = '?page=' . ($_GET['page'] - 1);
			print '| <a class="navitem" href="' . $next . '">more recent entries<font size="1">&gt;&gt;</font></a>';
			
		}
		
	}
	
	Template::run('entrylist_footer');
	
}

Template::run('footer');

?>
