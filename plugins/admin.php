<?php

$password = 'changeme';

$config = Engine::get_config();

// logout
if($_GET['admin'] == 'logout') {
	
	unset($_SESSION['admin']);
	//session_destroy();
	
}

if($_GET['admin'] == 'create')
	$config->set('blog_sub', 'create');
elseif(isset($_GET['admin']))
	$config->set('blog_sub', 'admin');

$rack['kure']['page_top'] = '';
if($GLOBALS['plugging'] && isset($_GET['admin'])) {
	
	$rack['kure']['page_top'] = '';
	
	if(isset($_POST['login'])) {
		
		if(md5($_POST['password']) == md5($password)) {
			
			$_SESSION['admin'] = true;
			header('Location: ' . KURE_ROOT . '?admin');
			
		} else {
			$rack['kure']['page_top'] .= '<div class="error">Invalid password.</div>';
		}
	}
	
	if(!$_SESSION['admin']) {
		
		if(isset($_SESSION['admin'])) // bad session
			$rack['kure']['page_top'] .= '<span class="error">Session invalid; please login again.</span><br/>';
		
		$rack['kure']['page_top'] .= '<span class="sitetitle">password</span>';
		$rack['kure']['page_top'] .= '<form action="?admin" method="post">';
		$rack['kure']['page_top'] .= '<input type="password" name="password">';
		$rack['kure']['page_top'] .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="login" value="login"></form>';
		
	} elseif($_GET['admin'] == 'create') {
		
		if(isset($_POST['submit_entry'])) {
			
			$entry = new Entry($_POST['title'], $_POST['content'], time());
			
			if(@$entry->write())
				$rack['kure']['page_top'] .= ('<span class="success">Entry created.</span>');
			else
				$rack['kure']['page_top'] .= '<span style="color: red;">Couldn\'t write to file.</span><br/><br/>(Told you.)';
			
		} else {
			
			if(!is_writable(KURE_ROOT . 'entries/'))
				$rack['kure']['page_top'] .= '<span style="color: #ffa500;">It looks like your <tt>entries</tt> folder isn\'t writable. You may want to change that before trying to post from here.</span><br/><br/>';
			$rack['kure']['page_top'] .= '<form action="?admin=create" method="post">';
			$rack['kure']['page_top'] .= 'title<br/><input class="form_text" name="title" size="50" type="text"><br/><br/>' . "\n";
			$rack['kure']['page_top'] .= 'content<br/><textarea class="form_textarea" cols="80" name="content" rows="12"></textarea><br/><br/>' . "\n";
			$rack['kure']['page_top'] .= '<input name="submit_entry" type="submit" value="create">' . "\n";
			$rack['kure']['page_top'] .= '</form>' . "\n\n";
			
		}
	} else {
		$rack['kure']['page_top'] .= '<a href="?admin=create">new entry</a><br/>';
		$rack['kure']['page_top'] .= '<a href="?admin=logout">logout</a>';
	}
}

?>
