<?php

// Enter your Disqus shortname here
// If you don't have one, sign up @ disqus.com
$shortname = 'changeme';

// Want to display links under each entry to view comments at the entry's page?
// If false, comments still display at the entry's page
$show_comments_link = true;

if($shortname == 'changeme')
  exit("Please set your shortname at the top of <tt>plugins/disqus.php</tt>, or
  remove this file if you do not want Disqus comments.");

if(isset($_GET['e'])) {
  $rack['entry']['bottom'] = '<br/><div id="disqus_thread"></div>
<script type="text/javascript">
(function() {
  var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
  dsq.src = "//'. $shortname .'.disqus.com/embed.js";
  (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
})();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>';

} elseif($show_comments_link) {

  $rack['kure']['page_bottom'] = '<script type="text/javascript">
(function () {
  var s = document.createElement("script"); s.type= "text/javascript"; s.async = true;
  s.type = "text/javascript";
  s.src = "http://'. $shortname .'.disqus.com/count.js";
  (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(s);
}());
</script>';

  $rack['entry']['bottom'] = '<p style="text-align: right; margin-top: 20px;"><a href="{ENTRYADDRESS}#disqus_thread"></a></p>';

}

?>
