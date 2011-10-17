<?php

$version = '0.7.1';
@include_once 'functions.php'; // supress errors because the file & directory check below will handle it

// Autoload any class which is used in this file
function __autoload($class) {
	include 'classes/' . $class . '.php';
}

$head = 
'<html>
	<head>
		<title>kure ' . $version . '</title>
		<style type="text/css">
			body {
				font-family: trebuchet ms, trebuchet, arial;
				font-size: 14px;
			}
			.pagetitle {
				color: #404040;
				font-family: trebuchet ms, trebuchet, arial;
				font-size: 20px;
				letter-spacing: -1px;
			}
		</style>
	</head>
	<body>
		<span class="pagetitle">Install kure ' . $version . '</span><br/>
		<p>Detecting tasks...</p>
';

if(!isset($_POST['create'])) // so that we can use header() to refresh later
	print $head;

// find out what needs to be done

clearstatcache(); // continue

if(!is__writable('./'))
	$todo_user[] = 'make the current directory (<tt>' . dirname($_SERVER['SCRIPT_NAME']) . '</tt>) writeable';

if(!file_exists('config.php'))
	$todo_kure['config'] = 'generate <tt>config.php</tt>';

if(!file_exists('entries/'))
	$todo_kure['entries'] = 'create an <tt>entries</tt> directory';
elseif(!is__writable('entries'))
	$todo_user[] = 'make the <tt>entries/</tt> directory writeable';

if(isset($_POST['create'])) {
	
	if(isset($todo_kure['config'])) {
		
		if($_POST['pass1'] != $_POST['pass2'] || $_POST['pass1'] == "")
			Engine::quit($head .  'Passwords did not match or were not entered. <a href="?">Try again</a>.');
		
		$prefs = array(
			'version'             => $version,
			'admin_password'      => md5($_POST['pass1']),
			'show_admin_link'     => true,
			'blog_name'           => 'kure',
			'blog_sub'            => 'beta',
			'template'            => 'k1',
			'entries_per_page'      => 8,
			'abc_entries'           => false,
		);
		
		$config = new Config();
		
		foreach($prefs as $key => $value)
			$config->$key = $value;
		
		$config->save();
		
	}
	
	if(isset($todo_kure['entries']) && !mkdir('entries/'))
		Engine::quit($head . '<span class="error">Couldn\'t create directory <tt>entries/</tt></span>');
	
	header('Location: ?'); // refresh the page to refresh the tasks
	
}

if(!isset($todo_user) && !isset($todo_kure))
	Engine::quit('<b>All done!</b> Be sure to DELETE THIS FILE before <a href="index.php">moving on</a>.<br/><br/>Keep in mind that <b>kure is still in beta</b>. This means that there may (and probably will) be some bugs and possible security holes. In most cases, security holes in kure will only affect kure\'s directory, but this does not rule out the possbile risk of other files on your server. It is a good practice in general, even if you\'re not using kure, to backup important files and information on your server regularly.');

print 'Okay. ';
if(isset($todo_user))
	print 'Let\'s see what\'s on the agenda for today:<br/><br/>';

if(isset($todo_user)) {
	
	print '<b>You</b> need to:<br/><br/>';
	
	foreach($todo_user as $task)
		print '&bull; ' . $task . '<br/>';
	
	print '<br/>';
	
	if(isset($todo_kure))
		print 'So that <b>kure</b> can:';
	else
		print 'Then you\'ll be done.';
	
} else {
	
	print 'Looks like kure is ready to do the following.';
  
}

print '<br/><br/>';

if(isset($todo_kure)) {
		
	foreach($todo_kure as $task)
		print '&bull; ' . $task . '<br/>';
	
	if(isset($todo_user))
		print '<br/>Or, if you are able, feel free to perform any of kure\'s tasks yourself. Either way, refresh this page when you are finished.';
	
} else {
	
	print 'Refresh this page when you\'re finished.';
	
}

print '<br/>';

if(!isset($todo_user)) {
	
	print '<form action="?" method="post">';
	
	if(isset($todo_kure['config'])) {
		
		print 'Before the config file is generated, <b>you must set a password with which you will access the administration interface</b>.<br/>It will be encrypted and stored in <tt>config.php</tt> with all other configuration variables, which is why you need to set it now.<br/><br/>You can change this later.<br/><br/>';
    print '<span class="pagetitle" style="color: #000000; font-size: 16px;">password:</span>';
		print '<p><input type="password" name="pass1"> (enter)</p><p><input type="password" name="pass2"> (confirm)</p>';
		
	}
	
	print 'Ready to go?<br/><br/><input type="submit" name="create" value="Let\'s do it"></form>';
	
}

// from http://php.net/manual/en/function.is-writable.php#73596
function is__writable($path) {
	
	// will work despite Windows ACLs bug
	// NOTE: use a trailing slash for folders!!!
	// see http://bugs.php.net/bug.php?id=27609
	// see http://bugs.php.net/bug.php?id=30931

	if($path{strlen($path) - 1} == '/') // recursively return a temporary file path
		return is__writable($path . uniqid(mt_rand()) . '.tmp');
	elseif(is_dir($path))
		return is__writable($path . '/' . uniqid(mt_rand()) . '.tmp');
	
	// check tmp file for read/write capabilities
	$rm = file_exists($path);
	$f = @fopen($path, 'a');
	
	if($f === false)
		return false;
	
	fclose($f);
	
	if(!$rm)
		unlink($path);
	
	return true;
	
}

?>

