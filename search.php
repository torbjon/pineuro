<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>europ.in</title>
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<link rel="stylesheet" href="/assets/css/reset.css" />
	<link rel="stylesheet" href="/assets/css/style.css" />
	<link rel="stylesheet" href="/assets/css/bootstrap.css" />
	<link rel="stylesheet" href="/assets/fancybox/jquery.fancybox-1.3.4.css" />
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

	<style>
		#popup {
			width: 800px;
			height: 500px;
			background-color: #333;
			z-index: 1000;
			/*border: 2px solid #000;*/
		}
		#popup_img {
			float: left;
			margin: 15px;
			width: 500px;
			height: 470px;
			background: #333 no-repeat center center;
		}
		#popup_side {
			float: left;
			margin: 15px auto;
			padding: 15px;
			width: 225px;
			height: 440px;
			background-color: #efefef;
			-webkit-border-radius: 2px;
			-moz-border-radius: 2px;
			border-radius: 2px;
		}
		#popup_img_title {
			background-color: rgba(0,0,0,0.7);
			font-size: 14px;
			color: #fff;
			padding: 10px 7px;
			-webkit-border-radius: 2px;
			-moz-border-radius: 2px;
			border-radius: 2px;
		}
		#popup_side ul {
			list-style-type: none;
			margin: 0; padding: 0;
		}
		#popup_side lh {
			font-weight: bold;
		}
		#popup_side li {
			padding-left: 5px;
		}
	</style>
	<div style="display:none;">
		<div id="popup">
				<div id="popup_img">
					<div id="popup_img_title">Saldus vidusskola</div>
				</div>
				<div id="popup_side">
					<ul>
						<lh>Country:</lh>
						<li id="datacountry"></li>
						<lh>Provider:</lh>
						<li id="dataprovider"></li>
						<lh>Europeana URI:</lh>
						<li id="dataoriginaluri"></li>
					</ul>
				</div>
		</div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="/assets/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<script src="/assets/fancybox/jquery.easing-1.3.pack.js"></script>
	<script src="/assets/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script src=""></script>
	<script src="/assets/js/wookmark.js"></script>
	<script src="http://balupton.github.com/history.js/scripts/bundled/html4+html5/jquery.history.js"></script>
	<script>
		(function(window,undefined){
			var History = window.History,
				$ = window.jQuery,
				document = window.document;
			if ( !History.enabled ) {
				return false;
			}
			$(function(){

			})
		})(window);
	
		// Prepare layout options.
		var options = {
			autoResize: true, // This will auto-update the layout when the browser window is resized.
			container: $('#main'),  // Optional, used for some extra CSS styling
			offset: 4 //, // Optional, the distance between grid items
			// itemWidth: 200 // Optional, the width of a grid item
		};

		var totalpages = 0,
			api_key = "HTMQFSCKKB",
			current_page = 1,
			searchTerm = "<?php echo isset($_GET['q'])?$_GET['q']:""; ?>";
		/*
		$("#f").submit(function(){
			if($("#q").val() != ""){
				document.location.hash = "!/search/"+encodeURIComponent($.trim($("#q").val()))
			}
			return false
		})
		$("#s").on("click", function(){
			if($("#q").val() != ""){
				document.location.hash = "!/search/"+encodeURIComponent($.trim($("#q").val()))
				$("#main").css({height: 0})
				current_page = 1
				$("#tiles").html("")
				searchTerm = $("#q").val()
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})
			}
		})
		*/

		function load_images(options){
			$.getJSON("http://api.europeana.eu/api/opensearch.json?callback=?", {
					searchTerms: options.searchTerm,
					wskey: api_key,
					qf: "TYPE:IMAGE",
					startPage: options.page
				}, function(data){
					totalpages = Math.ceil(data.totalResults / data.itemsPerPage) - 1
					$("#count").html(data.totalResults)
					$.each(data.items, function(i){
						$.getJSON(data.items[i].link+"&callback=?", function(item){
							newimg = new Image()
							newimg.src = "http://social.apps.lv/image.php?w=196&zc=2&src="+encodeURIComponent(item['europeana:object'])
							newimg.onload = function(){
								//if(this.width == 200){
									$("#tiles").append("<li><a class='imagepopup' href='#popup'><img width='"+this.width+"' height='"+this.height+"' data-originaluri='"+item['europeana:uri']+"' data-provider='"+item['europeana:provider']+"' data-country='"+item['europeana:country']+"' data-imgsrc='"+item['europeana:object']+"' data-title='"+item['dc:title']+"' src='http://social.apps.lv/image.php?w=200&zc=3&src="+encodeURIComponent(item['europeana:object'])+"' /></a></li>")
									if(handler) handler.wookmarkClear();
									handler = $('#tiles li');
									handler.wookmark({
										autoResize: true, // This will auto-update the layout when the browser window is resized.
										container: $('#main'), // Optional, used for some extra CSS styling
										offset: 4 // Optional, the distance between grid items
										//itemWidth: 210 // Optional, the width of a grid item
									});
								//}
							}
						})
					})
				}
			)
			current_page++
		}
		$(function(){
			/*
			searchTerm = decodeURIComponent(document.location.hash.replace("#!/search/",""))
			*/
			if(searchTerm != ""){
				$("#q").val(searchTerm)
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})
			}
			$("#tiles").on("click", ".imagepopup", function(){
				$("#popup_img").css('background-image', 'url(http://social.apps.lv/image.php?cc=333&w=470&h=470&zc=2&src='+$(this).children("img").data("imgsrc")+')')
				if($(this).children("img").data("title") != undefined){
					$("#popup_img_title").html($(this).children("img").data("title"))
					$("#datacountry").html($(this).children("img").data("country"))
					$("#dataprovider").html($(this).children("img").data("provider"))
					$("#dataoriginaluri").html($(this).children("img").data("originaluri"))
				} else {
					$("#popup_img_title").html("")
				}
				$(".imagepopup").fancybox({
					'width'					: 800,
					'height'				: 500,
					'padding'				: 0,
					'centerOnScroll': true,
					'transitionIn'	: 'elastic',
					'transitionOut'	: 'elastic',
					'easingIn'      : 'easeOutBack',
					'easingOut'     : 'easeInBack'
				})
				return false
			})
		})

		var handler = null;

		/**
		 * When scrolled all the way to the bottom, add more tiles.
		 */
		function onScroll(event) {
			// Check if we're within 100 pixels of the bottom edge of the broser window.
			var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 100);
			if(closeToBottom) {
				// Get the first then items from the grid, clone them, and add them to the bottom of the grid.
				load_images({
					searchTerm: searchTerm,
					page: current_page
				})

				// Clear our previous layout handler.
				if(handler) handler.wookmarkClear();

				// Create a new layout handler.
				handler = $('#tiles li');
				handler.wookmark(options);
			}
		};

		$(document).ready(new function() {
			// Capture scroll event.
			$(document).bind('scroll', onScroll);

			// Call the layout function.
			handler = $('#tiles li');
			handler.wookmark(options);
		});
	</script>

</body>
</html>
