<?php

/* NOTES
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

define('KURE_ROOT', '');
session_start();

// Tell all files to include relative to THIS FILE's directory
set_include_path(dirname($_SERVER['SCRIPT_FILENAME']));

// Autoload any class which is used in this file
function __autoload($class) {
	if(file_exists('classes/' . $class . '.php'))
		include 'classes/' . $class . '.php';
	else
		include_once 'classes/Exceptions.php';
}

require_once 'functions.php';

if(!file_exists('config.ini'))
	exit('<p>It looks like you haven\'t installed kure yet!<br/>Proceed to <a href="install.php">installation</a> if you need to install.<br/>If you don\'t, be sure to make sure your kure-related directories exist.</p>' . "\n");

Engine::init_plugins();
$config = Engine::get_config();
$language = Engine::get_language();

Template::run('header');

/***** Entry Viewer *****/
if(isset($_GET['entry'])) { // if a specific entry has been requested
	
	$filename = sanitize($_GET['entry']);
	
	$entry_handler = new EntryHandler($filename, null);
	
	if(!$entry_handler->has_next())
		Engine::quit('The requested file <tt>entries/' . $filename . '.txt</tt> doesn\'t exist.');
	
/***** Entry Listing (Home) *****/
} elseif(empty($_GET) || isset($_GET['page'])) {
	
	if(!isset($_GET['page']))
		$_GET['page'] = 0; // default to page 0
	
	// Avoid injection stuff
	if(!is_numeric($_GET['page']))
		Engine::quit('Invalid page.');
	
	$entry_handler = new EntryHandler($_GET['page'], $config->entries_per_page);
	
	if(!$entry_handler->has_next())
		Engine::quit('No entries to display.');
	
}

while($entry_handler->has_next()) {
	
	$entry = $entry_handler->next();
	
	$template_vars = array('{ENTRYTITLE}'   => $entry->title,
	                       '{ENTRYADDRESS}' => '?entry=' . $entry->filename,
	                       '{ENTRYDAY}'     => date('j', $entry->timestamp),
	                       '{ENTRYMONTH}'   => date('F', $entry->timestamp),
	                       '{ENTRYYEAR}'    => date('Y', $entry->timestamp),
	                       '{ENTRYCONTENT}' => $entry->content
	                      );
	
	Template::run('entry', $template_vars);
	
}

// Display "previous entries" / "more recent entries" links if necessary
if(($_GET['page'] + 1) * $config->entries_per_page < $entry_handler->total_entries) {
	
	$last = '?page=' . ($_GET['page'] + 1);
	print '<a class="navitem" href="' . $last . '">less recent</a>';
	
}

if($_GET['page'] != 0) {
	
	if($_GET['page'] == 1)
		$next = '?';
	else
		$next = '?page=' . ($_GET['page'] - 1);
	
	print '<a class="navitem" href="' . $next . '"> more recent</a>';
	
}

Template::run('footer');

?>
