kure
============
kure is a PHP blogging engine that runs on a stupidly simple flat-file database.

One text file represents one blog post;

* the filename is the post title (with a couple of exceptions),
* the file contents is the post itself, and
* the file's "last modified" timestamp is the post date.

kure comes with a small, removable admin plugin to manage posts, but for all I
care you can keelhaul and manage them with git, vim, ftp, your own scripts, your
little sister, or any combination thereof. Just make sure she's a good typist.

What else you got?
---------------------
kure's meant to be like a LEGO Star Wars ship. It's got a hull, and everything
else is detachable. It sports a templating engine, a plugin engine, and basic
localization. Strip everything away, and it's `cat` plus chips.

kure is built to be a simple, portable, easy-to-understand engine. If you want
something you can hack into shape yourself, or if you're new to programming and
looking to mess with something, kure might tickle some fancies. It's also pretty
fast to set up because it doesn't mess with any databases, so there's that.

Contributing
------------
Wrote a plugin? Fork the `plugins` branch, throw it in there, and pull request
me. Got a template? Fork `templates` and do the same.

And if you know another language, fork `master`, make a language definition (e.g. `languages/en.ini`), and pull request me.
