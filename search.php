<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo isset($_GET['q'])? ($_GET['q']." - "):""; ?>Europ.in - explore and share Europeana content</title>
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans&subset=latin,cyrillic-ext,cyrillic,latin-ext' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="/assets/css/reset.css" />
	<link rel="stylesheet" href="/assets/css/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT']."/assets/css/style.css"); ?>" />
	<link rel="stylesheet" href="/assets/css/bootstrap.css" />
	<link rel="stylesheet" href="/assets/fancybox/jquery.fancybox-1.3.4.css" />
	<?php
	if(isset($_GET['itemid'])):
		$ch = curl_init("http://europ.in/request_object.php?uri=".rawurlencode("http://www.europeana.eu/portal/record/".$_GET['itemid'].".json?wskey=HTMQFSCKKB"));
		curl_setopt_array($ch, array(
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_FOLLOWLOCATION => 1
		));
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpcode >= 200 && $httpcode < 400):
			$data = (array)json_decode($data);
	?>
	<meta property="og:title" content="<?php echo str_replace(array("\n","\r"), "", $data["dc:title"]); ?>" />
	<meta property="og:image" content="<?php echo $data["europeana:object"]; ?>" />
	<meta property="og:description" content="<?php echo str_replace(array("\n","\r",'"',"'"), "", is_array($data["dc:description"])?$data["dc:description"][0]:$data["dc:description"]); ?>" />
<?php
	endif;
endif;
?>
</head>
<body>
	<div id="header">
		<a href="/"><img src="/assets/images/logo_small.png" height="30" width="118" alt="" class="logo"></a>
		<form id="f" action="/search" class="search">
			<input type="text" id="q" name="q" value="<?php echo isset($_GET['q'])?$_GET['q']:""; ?>" class="search_bar" />
			<button id="s" class="btn btn-small">Search</button>
		</form>
		<div id="count"></div>
	</div>
	<div id="main" class="clearfix">
		<ul id="tiles"></ul>
	</div>
	<div style="display:none;">
		<div id="popup">
			<div id="popup_img">
				<div id="popup_img_title"></div>
			</div>
			<div id="popup_side">
				<div id="pinbutton">

				</div>
				<ul>
					<li id="datadescription"></li>
					<lh>Creator:</lh>
					<li id="datacreator"></li>
					<lh>Country:</lh>
					<li id="datacountry"></li>
					<lh>Data provider:</lh>
					<li id="dataprovider"></li>
					<lh>Europeana:</lh>
					<li id="dataoriginaluri"></li>
					<lh>Subjects:</lh>
					<li id="datasubjects"></li>
				</ul>
			</div>
		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="/assets/fancybox/jquery.fancybox-1.3.4.js"></script>
	<script src="/assets/fancybox/jquery.easing-1.3.pack.js"></script>
	<script src="/assets/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script src="/assets/js/wookmark.js"></script>
	<script src="http://balupton.github.com/history.js/scripts/bundled/html4+html5/jquery.history.js"></script>
	<script src="/assets/js/app.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT']."/assets/js/app.js"); ?>"></script>
	<script>
		var searchTerm = "<?php echo isset($_GET['q'])?$_GET['q']:""; ?>";
	</script>
<?php
	if(isset($_GET['itemid'])):
?>
	<script>
		$(function(){
			$.getJSON("http://www.europeana.eu/portal/record/<?php echo $_GET['itemid']; ?>.json?wskey=HTMQFSCKKB&callback=?", function(item){
				$("#popup_img").css('background-image', 'url(http://social.apps.lv/image.php?cc=333&w=470&h=470&zc=2&src='+encodeURIComponent(item['europeana:object'].replace(/\s/g,"%20"))+')')
				if(item['dc:title'] != undefined){
					$("#popup_img_title").html(item['dc:title'])
					$("#datacountry").html(item['europeana:country'].capitalize())
					$("#dataprovider").html(item['europeana:provider'])
					if(item['dc:creator'] != undefined){
						$("#datacreator").prev("lh").show()
						$("#datacreator").html(item['dc:creator'])
					} else {
						$("#datacreator").prev("lh").hide()
					}
					$("#dataoriginaluri").html('<a target="_blank" href="'+item['europeana:uri']+'">view this item at Europeana</a>')
					var subjects = []
					if(typeof(item['dc:subject']) == "object"){
						$.each(item['dc:subject'], function(i){
							subjects.push("<a href='/search?q="+encodeURIComponent(item['dc:subject'][i].replace("'","%27"))+"'>"+item['dc:subject'][i]+"</a>")
						})
					} else if(typeof(item['dc:subject']) == "string") {
						subjects.push("<a href='/search?q="+encodeURIComponent(item['dc:subject'].replace("'","%27"))+"'>"+item['dc:subject']+"</a>")
					}
					$("#datasubjects").html(subjects.join(", "));
					if(item['dc:description'] != undefined){
						$("#datadescription").html(item['dc:description'].toString())
					}
					$("#pinbutton").html(
						'<a href="http://pinterest.com/pin/create/button/?url='+encodeURIComponent(document.location.href)+'&media='+encodeURIComponent(item['europeana:object'].replace(/\s/g,"%20"))+'&description='+encodeURIComponent(item['dc:title'].toString())+'" class="pin-it-button" count-layout="vertical"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a><script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"><'+'/script>'+
						'<iframe src="//www.facebook.com/plugins/like.php?href='+encodeURIComponent(document.location.href)+'&amp;send=false&amp;layout=box_count&amp;width=55&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60&amp;appId=389315061119000" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:55px; height:60px; margin-left: 6px;" allowTransparency="true"></iframe>'
					);
				} else {
					$("#popup_img_title").html("")
				}
				$.fancybox(
					$("#popup").html(),
					{
						'width'			: 800,
						'height'		: 500,
						'padding'		: 0,
						'centerOnScroll': true,
						'transitionIn'	: 'elastic',
						'transitionOut'	: 'elastic',
						'easingIn'		: 'easeOutBack',
						'easingOut'		: 'easeInBack',
						'onClosed'		: function() {
	    					history.pushState(null, null, "/search?q="+encodeURIComponent(searchTerm))
						}
					}
				)
			})
		})
	</script>
<?
	endif;
?>

<script>
	var _gaq=[['_setAccount','UA-32161057-1'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<div id="loading_status"></div>
</body>
</html>
