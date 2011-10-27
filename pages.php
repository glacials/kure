<?php

/* pages
 * a kure plugin
 *
 * This plugin enables minimally cleaner entry URLs. It will turn
 *
 *   example.com/?entry=My-first-day
 *
 * into
 *
 *   example.com/?My-first-day
 *
 * This plugin will also instantly redirect any page requests matching the first
 * format to the second format.
 *
 * This is mainly meant for those who wish to turn kure entries into more of a
 * "page" feel than a blog post, but can be used for just cleaner URLs alone.
 *
 * To install, put this file in the plugins folder in your main kure directory.
 * If one doesn't exist, create it.
 *
 */

if(isset($_GET['entry']))
	header('Location: ?' . $_GET['entry']);
elseif(count($_GET) != 0)
	$_GET['entry'] = array_pop(array_keys($_GET));

?>
