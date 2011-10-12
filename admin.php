<?php

define('KURE_ROOT', '');

// Autoload any class which is used in this file
function __autoload($class) {
	include KURE_ROOT . 'classes/' . $class . '.php';
}

$config = Engine::get_config();

require_once KURE_ROOT . 'functions.php';
session_start();

// logout
if(isset($_GET['logout'])) {
	
	unset($_SESSION['admin']);
	session_destroy();
	header('Location: ' . KURE_ROOT . 'index.php');
	
}

// login
if(!isset($_SESSION['admin']) || $_SESSION['admin'] != $config->admin_password) {
	
	if(isset($_POST['login'])) {
		
		if(md5($_POST['password']) == $config->admin_password) {
			
			$_SESSION['admin'] = $config->admin_password;
			header('Location: ' . KURE_ROOT . 'admin.php');
			
		} else {
			
			Template::run('admin_header');
			print('<div style="position: absolute; left: 400px; top: 180px;">');
			Engine::error('Invalid password.', false);
			
		}
		
	} else {
		
		Template::run('admin_header');
		
	}
	
	print('<div style="position: absolute; left: 400px; top: 200px;">');
	
	if(isset($_SESSION['admin'])) // bad session
		print('<span class="error">Session invalid; please login again.</span><br/>');
	
	print('<span class="sitetitle">administrate</span> <span class="sitesub">' . $config->blog_name . '</span><br/><br/>');
	print('<form action="?" method="post">');
	print('<a type="blog_title">enter password</a><br/><input type="password" name="password">');
	print('&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="login" value="login"></form>');
	
	Engine::quit(); // don't allow any further access of administration unless logged in
	
}

Template::run('admin_header');

if(isset($_GET['config'])) {
	
	print '<span class="pagesub">config</span><br/><br/>' . "\n";
	
	if(isset($_POST['config_submit'])) {
		
		$config->blog_name           = $_POST['blog_name'];
		$config->blog_sub            = $_POST['blog_sub'];
		$config->posts_per_page      = $_POST['posts_per_page'];
		$config->show_doc_dates      = $_POST['show_doc_dates'];
		$config->show_doc_page_dates = $_POST['show_doc_page_dates'];
		$config->abc_docs            = $_POST['abc_docs'];
		$config->abc_posts           = $_POST['abc_posts'];
		$config->show_admin_link     = $_POST['show_admin_link'];
		
		if(!$config->save())
			Engine::quit('<span class="error">Couldn\'t write to <tt>config.php</tt>; check permissions and try again.</span>');
		
		print '<span class="success">Configuration saved.</span>';
		
	} else {
		
		print '<form action="?config" method="post">';
		
		print 'blog name<br/><input type="text" name="blog_name" value="' . $config->blog_name . '" class="form_text"><br/><br/>';
		print 'subname<br/><input type="text" name="blog_sub" value="' . $config->blog_sub . '" class="form_text"><br/><br/>';
		print 'posts per page<br/><input type="text" name="posts_per_page" value="' . $config->posts_per_page . '" class="form_text" size="3"> <span class="note">0 for unlimited</span><br/><br/>';
		
		print '<select name="show_doc_dates">';
		print '<option value="true"' . ($config->show_doc_dates ? 'selected' : '') . '>Yes</option>';
		print '<option value="false"' . (!$config->show_doc_dates ? 'selected' : '') . '>No</option>';
		print '</select> display dates on docs<br/><br/>';
		
		print '<select name="show_doc_page_dates">';
		print '<option value="true"' . ($config->show_doc_page_dates ? 'selected' : '') . '>Yes</option>';
		print '<option value="false"' . (!$config->show_doc_page_dates ? 'selected' : '') . '>No</option>';
		print '</select> display dates on doc listing<br/><br/>';
		
		print '<select name="abc_docs">';
		print '<option value="true"' . ($config->abc_docs ? 'selected' : '') . '>Alphabetical</option>';
		print '<option value="false"' . (!$config->abc_docs ? 'selected' : '') . '>Date descending</option>';
		print '</select> doc order<br/><br/>';
		
		print '<select name="abc_posts">';
		print '<option value="true"' . ($config->abc_posts ? 'selected' : '') . '>Alphabetical</option>';
		print '<option value="false"' . (!$config->abc_posts ? 'selected' : '') . '>Date descending</option>';
		print '</select> post order<br/><br/>';
		
		print '<select name="show_admin_link">';
		print '<option value="true"' . ($config->show_admin_link ? 'selected' : '') . '>Yes</option>';
		print '<option value="false"' . (!$config->show_admin_link ? 'selected' : '') . '>No</option>';
		print '</select> show admin panel link in sidebar<br/><br/>';
		
		print '<input type="submit" name="config_submit" value="save" class="form_submit">';
		
	}
	
} elseif(isset($_GET['plugins'])) {
	
	print('<span class="pagesub">plugins</span><br/>' . "\n");
	print('<div style="position: relative; left: 8px;">');
	Engine::plug('admplugins', 'listing');
	print('</div>');
	
} elseif(isset($_GET['plugin'])) {
	
	// blank page for plugins to use as a config/about page
	Engine::plug('admplugins', 'page');
	
} elseif(isset($_GET['templates'])) {
	
	print('<span class="pagesub">templates</span><br/><br/>' . "\n");
	
	if(isset($_POST['template_submit'])) {
		
		$config->template = $_POST['template'];
		
		if($config->save())
			print('<span class="success">Template changed.</span><br/><br/>');
		else
			Engine::quit('Error writing to <tt>config.php</tt>. Check permissions and try again.');
		
	}
	
	print('<form action="?templates" method="post">' . "\n");
	$templates = glob(KURE_ROOT . 'templates/*', GLOB_ONLYDIR);
	
	foreach($templates as $template) {
		
		$template = str_replace(KURE_ROOT . 'templates/', '', $template);
		print('<input type="radio" name="template" value="' . $template . '"');
		
		if($template == $config->template)
			print(' checked');
		
		print('> <tt>' . $template. '</tt><br/>' . "\n");
		
	}
	
	print('<br/><input type="submit" name="template_submit" value="save" class="form_submit"></form>');
	
} elseif(isset($_GET['create'])) {
	
	print('<span class="pagesub">create</span><br/><br/>' . "\n");
	
	if(isset($_POST['submit_post'])) {
	
		if(create_entry($_POST['title'], $_POST['content'], $_POST['type']))
			print('<span class="success">Entry created.</span>');
		
	} else {
		
		Engine::plug('admcreate', 'top');
		print('<form action="?create" method="post">' . "\n");
		print('title<br/><input class="form_text" name="title" size="50" type="text"><br/><br/>' . "\n");
		Engine::plug('admcreate', 'title_after');
		print('content<br/><textarea class="form_textarea" cols="80" name="content" rows="12"></textarea><br/><br/>' . "\n");
		Engine::plug('admcreate', 'content_after');
		print('<input checked name="type" type="radio" value="posts">post' . "\n");
		print('<input name="type" type="radio" value="docs">doc' . "\n");
		Engine::plug('admcreate', 'type_after');
		print('<br><br>' . "\n");
		print('<input class="form_submit" name="submit_post" type="submit" value="post">' . "\n");
		Engine::plug('admcreate', 'button_after');
		print('</form>' . "\n\n");
		
	}

} elseif(isset($_GET['modify'])) {
	
	print('<span class="pagesub">modify</span><br/><br/>' . "\n");
	
	if($_GET['modify'] != null) {
		
		if(isset($_POST['modify_entry'])) {
			
			$oldname = $_POST['oldfile'];
			
			if(strstr($oldname, 'docs/')) {
				
				$type = 'docs';
				$oldname = str_replace(KURE_ROOT . 'docs/', '', $oldname);
				
			} elseif(strstr($oldname, 'posts/')) {
				
				$type = 'posts';
				$oldname = str_replace(KURE_ROOT . 'posts/', '', $oldname);
				
			}
			
			if(!delete_entry($oldname, $type))
				Engine::quit('<span class="error">Old entry could not be removed. Check permissions and try again.</span>');
			
			if(create_entry($_POST['title'], $_POST['content'], $_POST['type']))
				print '<span class="success">Entry modified.</span>';
			
		} else {
			
			if(substr($_GET['modify'], 0, 5) == 'posts') {
				
				$oldtype = 'posts';
				$oldtitle = str_replace('posts/', '', $_GET['modify']);
				
			} elseif(substr($_GET['modify'], 0, 4) == 'docs') {
				
				$oldtype = 'docs';
				$oldtitle = str_replace('docs/', '', $_GET['modify']);
				
			} else {
				
				Engine::quit('<span class="error">Bad entry type.</span>');
				
			}
			
			$oldtitle = deparse_title($oldtitle);
			$oldcontent = file_get_contents($_GET['modify'] . '.txt');
			
			Engine::plug('admmodify', 'top');
			print('<form action="?modify=submit" method="post">' . "\n");
			print('title<br/><input class="form_text" name="title" size="50" type="text" value="' . $oldtitle . '"><br><br>' . "\n");
			Engine::plug('admmodify', 'title_after');
			print('content<br/><textarea class="form_textarea" cols="80" name="content" rows="12">' . $oldcontent . '</textarea><br><br>' . "\n");
			Engine::plug('admmodify', 'content_after');
			
			print '<input ' . ($oldtype == 'posts' ? 'checked' : '') . 'name="type" type="radio" value="posts">post' . "\n";
			print '<input ' . ($oldtype == 'docs' ? 'checked' : '') . 'name="type" type="radio" value="docs">doc' . "\n";
			
			Engine::plug('admmodify', 'type_after');
			print('<br><br>' . "\n");
			print('<input type="hidden" name="oldfile" value="' . $_GET['modify'] . '">' . "\n");
			print('<input class="form_submit" name="modify_entry" type="submit" value="modify">' . "\n");
			Engine::plug('admmodify', 'button_after');
			print('</form>' . "\n\n");
			
		}
		
	} else {
		
		$posts = glob(KURE_ROOT . "posts/*.txt");
		$docs = glob(KURE_ROOT . "docs/*.txt");
		
		usort($posts, "sort_by_mtime");
		usort($docs, "sort_by_mtime");
		
		$poststr = "";
		$docstr = "";
		
		foreach($posts as $post) {
			
			$post = str_replace('posts/', '', $post);
			$post = str_replace('.txt', '', $post);
			$post_title = deparse_title($post);
			$poststr .= '&nbsp;&nbsp;<a href="?del=posts/' . $post . '" class="small">[del]</a>&nbsp;<a href="?modify=posts/' . $post . '">' . $post_title . '</a><br/>';
			
		}
		
		foreach($docs as $doc) {
			
			$doc = str_replace('docs/', '', $doc);
			$doc = str_replace('.txt', '', $doc);
			$doc_title = deparse_title($doc);
			$docstr .= '&nbsp;&nbsp;<a href="?del=docs/' . $doc . '" class="small">[del]</a>&nbsp;<a href="?modify=docs/' . $doc . '">' . $doc_title . '</a><br/>';
			
		}
		
		$poststr = str_replace('\'', "\\'", $poststr);
		$docstr = str_replace('\'', "\\'", $docstr); // escape the ' character so it doesn't interefere with the javascript
		
		print '<div id="tabs"></div>';
		print '<script src="js/tabs.js" type="text/javascript"></script>';
		print '<script type="text/javascript">';
		print 'var tabs = new Tabs(document.getElementById(\'tabs\'));';
		print 'tabs.Add(\'posts\', postsTabSwitch);';
		print 'tabs.Add(\'docs\', docsTabSwitch);';
		print 'function postsTabSwitch(paneElement) {';
		print 'if(paneElement.innerHTML == \'\')';
		print 'paneElement.innerHTML = \'' . $poststr . '\'';
		print '}';
		print 'function docsTabSwitch(paneElement) {';
		print 'if(paneElement.innerHTML == \'\')';
		print 'paneElement.innerHTML = \'' . $docstr . '\'';
		print '}';
		print '</script>';
		
	}
	
} elseif(isset($_GET['del'])) {
	
	if(strstr($_GET['del'], 'docs/')) {
		
		$type = 'doc';
		$title = str_replace('docs/', '', $_GET['del']);
		
	} elseif(strstr($_GET['del'], 'posts/')) {
		
		$type = 'post';
		$title = str_replace('posts/', '', $_GET['del']);
		
	}
	
	if(isset($_POST['confirm_delete'])) {
		
		if(delete_entry($title, $type))
			print('<span class="success">Entry deleted.</span>');
		else
			print('<span class="error">Couldn\'t delete $type <tt>' . deparse_title($title) . '</tt>. Check permissions and try again.</span>');
		
	} else {
		
		print('<span class="pagesub">delete entry</span><br/><br/>' . "\n");
		print('Are you sure you want to delete the ' . $type . '<b><tt>' . deparse_title($title) . '</tt></b>? This cannot be undone.<br/><br/>' . "\n");
		print('<div align="right"><form action="?del=' . $_GET['del'] . '" method="post"><input type="submit" name="confirm_delete" value="Yes, delete this ' . $type . '"></form>' . "\n");
		print('<a href="?modify" class="navitem">Go back</a></div>');
		
	}
	
} elseif(isset($_GET['password'])) {
	
	print('<span class="pagesub">change password</span><br/><br/>' . "\n");
	
	if(isset($_POST['pass_submit'])) {
		
		if($_POST['newpass1'] != $_POST['newpass2'] || $_POST['newpass1'] == "")
			Engine::quit('Passwords did not match or were not entered. <a href="?password">Try again</a>.');
		if(md5($_POST['curpass']) != $config->admin_password)
			Engine::quit('Incorrect current password. <a href="?password">Try again</a>.');
		
		$config->admin_password = md5($_POST['newpass1']);

		if($config->save())
			print('<span class="success">Password changed.</span>');
		
	} else {
		
		print'<form action="?password" method="post">';
		print'current password<br/><input type="password" name="curpass" class="form_text"><br/><br/>';
		print'new password<br/><input type="password" name="newpass1" class="form_text"><br/><br/>';
		print'confirm<br/><input type="password" name="newpass2" class="form_text"><br/><br/>';
		print'<input type="submit" name="pass_submit" value="change password" class="form_submit"></form>';
		
	}

} else { // main
	
	print '<p>&gt;&gt; <a href="?config" class="pagesub">config</a> &bull;';
	print 'change site options + variables</p>';
	print '<p>&gt;&gt; <a href="?plugins" class="pagesub">plugins</a> &bull;';
	print 'enable, disable, and manage plugins</p>';
	print '<p>&gt;&gt; <a href="?templates" class="pagesub">templates</a> &bull;';
	print 'swap templates</p>';
	print '<br/>';
	print '<p>&gt;&gt; <a href="?create" class="pagesub">create</a> &bull;';
	print 'make a new post or doc</p>';
	print '<p>&gt;&gt; <a href="?modify" class="pagesub">modify</a> &bull;';
	print 'edit or delete posts and docs</p>';
	print '<br/>';
	print '<p>&gt;&gt; <a href="?password" class="pagesub">change password</a> &bull;';
	print 'change your administration password</p>';
	print '<p>&gt;&gt; <a href="?logout" class="pagesub">logout</a> &bull;';
	print 'destroy your administration session and return to your blog</p>';
	print '<br/>';
	print '<p>&lt;&lt; <a href="index.php" class="pagesub">back to site</a> &bull;';
	print 'return to your blog</p>';
	
}

Template::run('admin_footer');

?>
