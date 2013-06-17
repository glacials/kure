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
  elseif($_GET['admin'] == 'edit')
    $config->set('blog_sub', 'edit');
  elseif(isset($_GET['admin']))
    $config->set('blog_sub', 'admin');

  $rack['kure']['page_top'] = '';
  if($GLOBALS['plugging'] && isset($_GET['admin'])) {

    $rack['kure']['page_top'] = '';

    if(isset($_POST['login'])) {

      if($_POST['password'] == $admin_config->password) {

        $_SESSION['admin'] = true;
        header('Location: ?admin');

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

    } elseif(isset($_GET['success'])) {

      $rack['kure']['page_top'] .= ('<span class="success">Entry '. $_GET['success'] .'.</span>');

    } elseif($_GET['admin'] == 'create') {

      if(isset($_POST['submit_entry'])) {

        $entry = new Entry($_POST['title'], $_POST['content'], time());

        if(@$entry->write())
          header('Location: ?admin=create&success=created');
        else
          $rack['kure']['page_top'] .= '<span style="color: red;">Couldn\'t write to file.</span><br/><br/>(Told you.)';

      } else {

        if(!is_writable('entries/'))
          $rack['kure']['page_top'] .= '<span style="color: #ffa500;">It looks like your <tt>entries</tt> folder isn\'t writable. You may want to change that before trying to post from here.</span><br/><br/>';
        $rack['kure']['page_top'] .= '<form action="?admin=create" method="post">';
        $rack['kure']['page_top'] .= 'title<br/>';
        $rack['kure']['page_top'] .= '<input class="form_text" name="title" size="50" type="text"><br/><br/>' . "\n";
        $rack['kure']['page_top'] .= 'content<br/>';
        $rack['kure']['page_top'] .= '<textarea class="form_textarea" cols="80" name="content" rows="12"></textarea><br/><br/>' . "\n";
        $rack['kure']['page_top'] .= '<input name="submit_entry" type="submit" value="create">' . "\n";
        $rack['kure']['page_top'] .= '</form>' . "\n\n";

      }
    } elseif($_GET['admin'] == 'edit') {

      if(isset($_POST['edit_entry'])) {

        $entry_handler = new EntryHandler($_POST['old_title']);
        $old_entry = $entry_handler->next();
        unlink('entries/'. $old_entry->filename . '.txt');
        //$rack['kure']['page_top'] .= 'keep_time is '. $_POST['keep_time'];
        $entry = new Entry($_POST['title'], $_POST['content'], $_POST['keep_time'] == 'on' ? $old_entry->timestamp : time());
        $entry->write();
        header('Location: ?admin=edit&success=edited');

      } elseif(isset($_POST['delete_entry'])) {

        $entry_handler = new EntryHandler($_POST['old_title']);
        $old_entry = $entry_handler->next();
        unlink('entries/'. $old_entry->filename .'.txt');
        header('Location: ?admin=edit&success=deleted');

      } elseif(isset($_GET['entry'])) {

        $entry = new EntryHandler($_GET['entry']);
        $entry = $entry->next();
        $rack['kure']['page_top'] .= '<form action="?admin=edit&entry='. $_GET['edit'] .'" method="post" style="display: inline;">';
        $rack['kure']['page_top'] .= '<input name="old_title" type="hidden" value="'. $entry->title .'"/>'."\n";
        $rack['kure']['page_top'] .= 'title<br/>';
        $rack['kure']['page_top'] .= '<input class="form_text" name="title" size="50" type="text" value="'. $entry->title .'"><br/><br/>'."\n";
        $rack['kure']['page_top'] .= 'content<br/>';
        $rack['kure']['page_top'] .= '<textarea class="form_textarea" cols="80" name="content" rows="12">'. $entry->raw_content .'</textarea><br/><br/>'."\n";
        $rack['kure']['page_top'] .= '<p><label><input name="keep_time" type="checkbox" checked/> Keep original timestamp</label></p>'."\n";
        $rack['kure']['page_top'] .= '<input name="edit_entry" type="submit" value="edit"/>'."\n";
        $rack['kure']['page_top'] .= '<input name="delete_entry" type="submit" value="delete this entry" style="background-color: red; color: white;" onclick="return confirm(\'Really delete '. $entry->title .'?\');return false;"/>'."\n";
        $rack['kure']['page_top'] .= '</form>'."\n";

      } else {

        $entry_handler = new EntryHandler(0, 0);
        while($entry_handler->has_next()) {
          $entry = $entry_handler->next();
          $rack['kure']['page_top'] .= '<a href="?admin=edit&entry='. $entry->filename .'">'. $entry->title .'</a><br/>';
        }

      }
    } else {
      $rack['kure']['page_top'] .= '<a href="?admin=create">new entry</a><br/>';
      $rack['kure']['page_top'] .= '<a href="?admin=edit">edit entries</a><br/>';
      $rack['kure']['page_top'] .= '<a href="?admin=logout">logout</a>';
    }
  }

  if($admin_config->display_link)
    $rack['kure']['bottom'] =  ' / <a class="footer" href="?admin">admin</a>';

} catch (PropertyDoesNotExistException $e) {
  $rack['kure']['page_top'] = 'It looks like you\'ve trashed the admin variables in <tt>config.php</tt>. No worries. If you want to use the admin interface, just open it up and paste the following at the very bottom:<br/><blockquote><tt>[admin]<br/>password = changeme<br/>display_link = yes</tt></blockquote> If you\'ve got no need for the admin interface, feel free to delete the plugin (<tt>plugins/admin.php</tt>).';
}
?>
