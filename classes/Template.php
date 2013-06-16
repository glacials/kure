<?php

class Template {

  // outputs code from $page in the current template using the variables contained in array $vars
  public static function run($page, $vars = null) {

    $config = Engine::get_config();

    $html = file_get_contents('templates/' . $config->template . '/' . $page . '.html');

    if(!$html)
      throw new CannotFindFileException('templates/' . $config->template . '/' . $page . '.html');

    $vars['{TITLE}']    = $config->blog_name;
    $vars['{SUBTITLE}'] = $config->blog_sub;
    $vars['{VERSION}']  = $config->version;

    $html = str_replace(array_keys($vars), $vars, $html);

    // todo: convert this into a preg_replace so that hooks don't need to be "defined" somewhere
    $hook_pages = array(
      'kure'       => array('head', 'top', 'title_before', 'title_after', 'subtitle_before', 'subtitle_after', 'navtitle_before', 'navtitle_after', 'naventries_after', 'navdocs_after', 'navadmin_after', 'page_top', 'page_bottom', 'bottom'),
      'entries'    => array('top', 'bottom', 'entry_top', 'entry-title_after', 'entry-date_after', 'entry-body_after'),
      'entry'      => array('top', 'title_after', 'date_after', 'body_after', 'bottom'),
      'adm'        => array('head', 'top'),
      'admcreate'  => array('top', 'title_after', 'content_after', 'type_after', 'button_after'),
      'admmodify'  => array('top', 'title_after', 'content_after', 'type_after', 'button_after'),
      'admplugins' => array('listing', 'page')
    );

    // call plug() for each hook on each page, passing id if dynamic
    foreach($hook_pages as $page => $locs)
      foreach($locs as $loc)
        $html = str_ireplace('{HOOK:' . $page . '-' . $loc . '}', isset($vars['id']) ? Engine::plug($page, $loc, $vars['id']) : Engine::plug($page, $loc), $html);

    print($html);

  }

}

?>
