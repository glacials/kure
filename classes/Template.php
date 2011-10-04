<?php

class Template {
	
	// outputs code from $page in the current template using the variables contained in array $vars
	static function run($page, $vars = null) {
		
		$code = file_get_contents('templates/' . Engine::get_config()->template . '/' . $page . '.html');
		
		// template variables start
		$vars['TITLE']    = Engine::get_config()->blog_name;
		$vars['SUBTITLE'] = Engine::get_config()->blog_sub;
		$vars['VERSION']  = Engine::get_config()->version;
		
		foreach($vars as $var => $val)
			$code = str_replace('{' . $var . '}', $val, $code);
		// template variables end

		// template conditionals start
		$vars_find = array(
			'/{IF:DOCDATES}(.*?){\/IF:DOCDATES}/is',
			'/{IF:DOCSPAGEDATES}(.*?){\/IF:DOCSPAGEDATES}/is',
			'/{IF:ADMINLINK}(.*?){\/IF:ADMINLINK}/is',
			'/{IF:POST}(.*?){\/IF:POST}/is', 
			'/{IF:DOC}(.*?)\{\/IF:DOC}/is',
		);
		
		$vars_replace[] = Engine::get_config()->show_doc_dates ? '$1' : '';
		$vars_replace[] = Engine::get_config()->show_doc_page_dates ? '$1' : '';
		$vars_replace[] = Engine::get_config()->show_admin_link ? '$1' : '';
		
		$vars_replace[] = isset($vars['ENTRYTYPE']) && $vars['ENTRYTYPE'] == 'post' ? '$1' : '';
		$vars_replace[] = isset($vars['ENTRYTYPE']) && $vars['ENTRYTYPE'] == 'doc' ? '$1' : '';
		
		$code = preg_replace($vars_find, $vars_replace, $code);
		// template conditionals end
		
		// template hooks start
		
		// todo: convert this into a preg_replace so that hooks don't need to be "defined" somewhere
		$hook_pages = array(
			'kure' => array('head', 'top', 'title_before', 'title_after', 'navtitle_before', 'navtitle_after', 'navposts_after', 'navdocs_after', 'navadmin_after', 'page_top', 'page_bottom', 'bottom'),
			'posts' => array('top', 'bottom', 'post_top', 'post-title_after', 'post-date_after', 'post-body_after'),
			'post' => array('top', 'title_after', 'date_after', 'body_after'),
			'docs' => array('top', 'bottom', 'doc-title_after', 'doc-body_after'), 
			'doc' => array('top', 'title_after', 'date_after', 'body_after'),
			'adm' => array('head', 'top'),
			'admcreate' => array('top', 'title_after', 'content_after', 'type_after', 'button_after'),
			'admmodify' => array('top', 'title_after', 'content_after', 'type_after', 'button_after'),
			'admplugins' => array('listing', 'page')
		);
		
		// call plug() for each hook on each page, passing id if dynamic
		foreach($hook_pages as $page => $locs)
			foreach($locs as $loc)
				$code = str_ireplace('{HOOK:' . $page . '-' . $loc . '}', isset($vars['id']) ? plug($page, $loc, $vars['id']) : plug($page, $loc), $code);
		// template hooks end
		
		print($code);
		
	}
	
}

?>
