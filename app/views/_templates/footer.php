	</div>
</div>

<!-- footer -->
<footer id="footer">
	<div class="footer-wrap"><h3>Footer content</h3></div>
</footer>

<script>
	
	//custom variables
	//https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingCustomVariables
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-53057850-2', 'auto');
	ga('send', 'pageview');
	
</script>

<?php
	/*
	echo out the content of the SESSION via KINT, a Composer-loaded much better version of var_dump
	KINT can be used with the simple function d()
	*/
	//d($_SESSION); 
?>
    
</body>
</html>