<?php

class Template {
	
	// outputs code from $page in the current template using the variables contained in array $vars
	static function run($page, $vars = null) {
		
		$config = Engine::get_config();
		
		$code = file_get_contents('templates/' . $config->template . '/' . $page . '.html');
		
		$vars['TITLE']    = $config->blog_name;
		$vars['SUBTITLE'] = $config->blog_sub;
		$vars['VERSION']  = $config->version;
		
		foreach($vars as $var => $val)
			$code = str_replace('{' . $var . '}', $val, $code);

		$vars_find = array(
			'/{IF:ADMINLINK}(.*?){\/IF:ADMINLINK}/is',
		);
		
		$vars_replace[] = $config->show_admin_link ? '$1' : '';
		
		$code = preg_replace($vars_find, $vars_replace, $code);
		
		// todo: convert this into a preg_replace so that hooks don't need to be "defined" somewhere
		$hook_pages = array(
			'kure' => array('head', 'top', 'title_before', 'title_after', 'navtitle_before', 'navtitle_after', 'naventries_after', 'navdocs_after', 'navadmin_after', 'page_top', 'page_bottom', 'bottom'),
			'entries' => array('top', 'bottom', 'entry_top', 'entry-title_after', 'entry-date_after', 'entry-body_after'),
			'entry' => array('top', 'title_after', 'date_after', 'body_after'),
			'adm' => array('head', 'top'),
			'admcreate' => array('top', 'title_after', 'content_after', 'type_after', 'button_after'),
			'admmodify' => array('top', 'title_after', 'content_after', 'type_after', 'button_after'),
			'admplugins' => array('listing', 'page')
		);
		
		// call plug() for each hook on each page, passing id if dynamic
		foreach($hook_pages as $page => $locs)
			foreach($locs as $loc)
				$code = str_ireplace('{HOOK:' . $page . '-' . $loc . '}', isset($vars['id']) ? Engine::plug($page, $loc, $vars['id']) : Engine::plug($page, $loc), $code);
		
		print($code);
		
	}
	
}

?>
