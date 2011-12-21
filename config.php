<?
/*
 * This file holds the format of an INI file, because that's how it is parsed.
 * However it holds the extension and opening tag of a PHP file so that we don't
 * output information to the browser if called directly.
 */

[kure]
; Name & description of site
blog_name = kure
blog_sub  = beta

; Template -- must be a folder in `templates`
; Default is k1
template = k1

; Number of entries to display per page
; Default is 8; no limit is 0
entries_per_page = 8

; Alphabetize entries? (no to display by most recent)
; Default is no
abc_entries = no

; Language -- must be an ini file in `languages`
language = en

version = 0.7.2
