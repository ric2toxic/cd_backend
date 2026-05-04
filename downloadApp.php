<script type="text/javascript" src="/catalog/view/javascript/jquery/jquery-3.2.0.min.js"></script>
<script type="text/javascript">
	$(function(){
		var isiDevice = /ipad|iphone|ipod/i.test(navigator.userAgent.toLowerCase());

		if (isiDevice)
		{
		  window.location.href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8";
		}

		var isAndroid = /android/i.test(navigator.userAgent.toLowerCase());

		if (isAndroid)
		{
		  window.location.href="https://play.google.com/store/apps/details?id=in.wholesalebox&hl=en";
		}
	});
</script>
