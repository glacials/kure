<?
/*
 * This file holds the format of an INI file, because that's how it is parsed.
 * However it holds the extension and opening tag of a PHP file so that we don't
 * output information to the browser if called directly.
 */

[kure]
# Name & subtitle of site
blog_name = kure
blog_sub  = beta

# Number of entries to display per page
# Default is 8; no limit is 0
entries_per_page = 8

# Alphabetize entries? (no to display by most recent)
# Default is no
abc_entries = no

# Markdown is a simple parsing format to pick up on certain cues in plaintext
# and convert them to their HTML equivalents. Unless you write your own HTML in
# your entries, you probably want this on.
# See http://daringfireball.net/projects/markdown/ for more info.
# Default is yes
markdown = yes

# Template -- must be a folder in `templates`
# Default is k1
template = k1

# Language -- must be an ini file in `languages`
# Default is en
language = en

# No need to change
version =
