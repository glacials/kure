<?php

/* Config options */
$display_link = true;
$config = Engine::get_config();
$admin_config = new Config('admin');
$admin_config->load();

try {
  $admin_config->password;

  // logout
  if($_GET['admin'] == 'logout') {

    unset($_SESSION['admin']);
    //session_destroy();

  }

  // Modify site subtitle to be a link to admin home on hover
  if(!empty($_GET['admin'])) {
    $rack['kure']['head'] = "\n    <style type='text/css'>
          div#header-right:hover span#site-subtitle, span.secret {
            display: none;
          }
          div#header-right:hover span#site-subtitle.secret {
            display: inline;
          }
        </style>";

    $rack['kure']['subtitle_after'] = "\n<span id='site-subtitle' class='secret'><a href='?admin'>admin</a></span>";
  }

  if($_GET['admin'] == 'create')
    $config->set('blog_sub', 'create');
  elseif(isset($_GET['admin']))
    $config->set('blog_sub', 'admin');

  $rack['kure']['page_top'] = '';
  if($GLOBALS['plugging'] && isset($_GET['admin'])) {

    $rack['kure']['page_top'] = '';

    if(isset($_POST['login'])) {

      if($_POST['password'] == $admin_config->password) {

        $_SESSION['admin'] = true;
        header('Location: ' . KURE_ROOT . '?admin');

      } else {
        $rack['kure']['page_top'] .= '<div class="error">Invalid password.</div>';
      }
    }

    if(!$_SESSION['admin']) {

      if(isset($_SESSION['admin'])) // bad session
        $rack['kure']['page_top'] .= '<span class="error">Session invalid; please login again.</span><br/>';

      if($admin_config->password == 'changeme') {
        $rack['kure']['page_top'] .= 'Before logging in, you must change the admin password in <tt>config.php</tt>.';
      } else {
        $rack['kure']['page_top'] .= '<span class="sitetitle">password</span>';
        $rack['kure']['page_top'] .= '<form action="?admin" method="post">';
        $rack['kure']['page_top'] .= '<input type="password" name="password">';
        $rack['kure']['page_top'] .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="login" value="login"></form>';
      }

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
        $rack['kure']['page_top'] .= 'title<br/>';
        $rack['kure']['page_top'] .= '<input class="form_text" name="title" size="50" type="text"><br/><br/>' . "\n";
        $rack['kure']['page_top'] .= 'content<br/>';
        $rack['kure']['page_top'] .= '<textarea class="form_textarea" cols="80" name="content" rows="12"></textarea><br/><br/>' . "\n";
        $rack['kure']['page_top'] .= '<input name="submit_entry" type="submit" value="create">' . "\n";
        $rack['kure']['page_top'] .= '</form>' . "\n\n";

      }
    } else {
      $rack['kure']['page_top'] .= '<a href="?admin=create">new entry</a><br/>';
      $rack['kure']['page_top'] .= '<a href="?admin=logout">logout</a>';
    }
  }

  if($admin_config->display_link)
    $rack['kure']['bottom'] =  ' / <a class="footer" href="?admin">admin</a>';

} catch (PropertyDoesNotExistException $e) {
  $rack['kure']['page_top'] = 'It looks like you\'ve trashed the admin variables in <tt>config.php</tt>. No worries. If you want to use the admin interface, just open it up and paste the following at the very bottom:<br/><blockquote><tt>[admin]<br/>password = changeme<br/>display_link = yes</tt></blockquote> If you\'ve got no need for the admin interface, feel free to delete the plugin (<tt>plugins/admin.php</tt>).';
}
?>
