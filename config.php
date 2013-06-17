;<?php/*
;
; This file holds the format of an INI file, because that's how it is parsed.
; However it holds the extension and opening tag of a PHP file so that we don't
; output information to the browser if called directly. Your text editor
; probably hates this, and the whole file probably looks like it's commented.
; I'm working on a more friendly solution.
;

[kure]
; Name & subtitle of site
blog_name = kure
blog_sub  = beta

; Number of entries to display per page
; Default is 8; no limit is 0
entries_per_page = 8

; Alphabetize entries? (no to display by most recent)
; Default is no
abc_entries = no

; Parse entries with Markdown? See http:daringfireball.net/projects/markdown/
; Default is yes
markdown = yes

; Template (must be a folder in `templates`)
; Default is k1
template = k1

; Language (must be an ini file in `languages`)
; Default is en
language = en

; No need to change
version =

; If you ditch the admin plugin, you can also ditch this and all following lines
[admin]
; Admin login password
; Default is changeme (which you must change)
password = changeme

; Display link to admin page in footer?
; If no, you can access the page at <kure's url>/?admin
; Default is yes
display_link = yes

;*/?>
