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
var totalpages = 0,
	current_page = 1,
	doscrollevent = true,
	imagesLoaded = 0,
	objectsLoaded = 0,
	imagesToLoad = 0,
	objectsTotal = 0;
function fancing() {
	$(".imagepopup").fancybox({
		'width'			: 800,
		'height'		: 500,
		'padding'		: 0,
		'centerOnScroll': true,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic',
		'easingIn'		: 'easeOutBack',
		'easingOut'		: 'easeInBack'
	})
}
function wookmarking(){
	$('#tiles li').wookmark({
		autoResize: true,
		container: $('#main'),
		offset: 4 //,
		//itemWidth: 210
	});
	fancing();
}
function load_images(){
	doscrollevent = false
	$.getJSON("/request.php?callback=?", {
		searchTerms: searchTerm,
		qf: "TYPE:IMAGE",
		startPage: current_page
	}, function(data){
		objectsTotal = data.totalResults
		totalpages = Math.ceil(data.totalResults / data.itemsPerPage) - 1
		$("#count").html(data.totalResults)
		$.each(data.items, function(i){
			$.getJSON("/request_object.php?uri="+encodeURIComponent(data.items[i].link), function(item){
				objectsLoaded++
				if(item['europeana:object'] != undefined){
					newimg = new Image()
					newimg.src = "http://social.apps.lv/image.php?w=196&zc=2&src="+encodeURIComponent(item['europeana:object'])
					newimg.onload = function(){
						imagesLoaded++
						var subjects = []
						if(typeof(item['dc:subject']) == "object"){
							$.each(item['dc:subject'], function(i){
								subjects.push("<a href='/search?q="+encodeURIComponent(item['dc:subject'][i].replace("'","%27"))+"'>"+item['dc:subject'][i]+"</a>")
							})
						} else if(typeof(item['dc:subject']) == "string") {
							subjects.push("<a href='/search?q="+encodeURIComponent(item['dc:subject'].replace("'","%27"))+"'>"+item['dc:subject']+"</a>")
						}
						//if(this.width == 196){
							imagesLoaded++
							$("#tiles").append(
								"<li><a class='imagepopup' href='#popup'><img width='"+this.width+
								"' height='"+this.height+
								"' data-subjects=\""+encodeURIComponent(subjects.join(", "))+
								"\" data-description='"+encodeURIComponent(item['dc:description'])+
								"' data-originaluri='"+item['europeana:uri']+
								"' data-provider='"+item['europeana:provider']+
								"' data-country='"+item['europeana:country']+
								"' data-creator='"+item['dc:creator']+
								"' data-imgsrc='"+item['europeana:object'].replace(/\s/g,"%20")+
								"' data-title='"+item['dc:title']+
								"' src='http://social.apps.lv/image.php?w=200&zc=3&src="+encodeURIComponent(item['europeana:object'])+
								"' /></a></li>")
							if(handler) handler.wookmarkClear();
							wookmarking();
						//}
					}
				}
			})
		})
		doscrollevent = true
	})
	current_page++
}
$(function(){
	if(searchTerm != ""){
		$("#q").val(searchTerm)
		load_images()
	}
	var initialloads = 0;
	$('#main').ajaxComplete(function() {
		initialloads++
		if(initialloads == 12){
			if(objectsTotal > 0 && $("#main").height() < $(window).height() && objectsLoaded < objectsTotal){
				load_images()
			}
			initialloads = 0
		}
	})
	$("#tiles").on("click", ".imagepopup", function(){
		history.pushState(null, null, "/item/"+$(this).children("img").data("originaluri").replace("http://www.europeana.eu/resolve/record/","")+"?q="+encodeURIComponent(searchTerm))
		$("#popup_img").css('background-image', 'url(http://social.apps.lv/image.php?cc=333&w=470&h=470&zc=2&src='+encodeURIComponent($(this).children("img").data("imgsrc"))+')')
		if($(this).children("img").data("title") != undefined){
			$("#popup_img_title").html($(this).children("img").data("title"))
			$("#datacountry").html($(this).children("img").data("country").capitalize())
			$("#dataprovider").html($(this).children("img").data("provider"))
			if($(this).children("img").data("creator") != "undefined"){
				$("#datacreator").prev("lh").show()
				$("#datacreator").html($(this).children("img").data("creator"))
			} else {
				$("#datacreator").prev("lh").hide()
			}
			$("#dataoriginaluri").html('<a target="_blank" href="'+$(this).children("img").data("originaluri")+'">view this item at Europeana</a>')
			if($(this).children("img").data("subjects").length){
				$("#datasubjects").prev("lh").show()
				$("#datasubjects").html(decodeURIComponent($(this).children("img").data("subjects")))
			} else {
				$("#datasubjects").prev("lh").hide()
			}
			if($(this).children("img").data("description") != "undefined"){
				$("#datadescription").html(decodeURIComponent($(this).children("img").data("description")))
			}
			$("#pinbutton").html(
				'<a href="http://pinterest.com/pin/create/button/?url='+encodeURIComponent(document.location.href)+'&media='+encodeURIComponent($(this).children("img").data("imgsrc"))+'&description='+encodeURIComponent($(this).children("img").data("title"))+'" class="pin-it-button" count-layout="vertical"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a><script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"><'+'/script>'+
				'<iframe src="//www.facebook.com/plugins/like.php?href='+encodeURIComponent(document.location.href)+'&amp;send=false&amp;layout=box_count&amp;width=55&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60&amp;appId=389315061119000" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:55px; height:60px; margin-left: 6px;" allowTransparency="true"></iframe>'
			);
		} else {
			$("#popup_img_title").html("")
		}
		fancing();
		return false
	})
	$("body").on("click", "#fancybox-close", function(){
		history.pushState(null, null, "/search?q="+encodeURIComponent(searchTerm))
	})
})
String.prototype.capitalize = function(){
	str = this;
	var doneStr = '';
	var len = str.length;
	var wordIdx = 0;
	var char;
	for (var i = 0;i < len;i++) {
		char = str.substring(i,i + 1);
		if (' -/#$&.()'.indexOf(char) > -1) {
			wordIdx = -1;
		}
		if (wordIdx == 0) {
			char = char.toUpperCase();
		} else if (wordIdx > 0) {
			char = char.toLowerCase();
		}
		doneStr += char;
		wordIdx++;
	}
	return doneStr;
}
var handler = null;
function onScroll(event) {
	if(doscrollevent){
		// Check if we're within 100 pixels of the bottom edge of the broser window.
		var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 1);
		if(closeToBottom) {
			// Get the first then items from the grid, clone them, and add them to the bottom of the grid.
			load_images()
			// Clear our previous layout handler.
			if(handler) handler.wookmarkClear();
			// Create a new layout handler.
			wookmarking();
		}
	}
};
$(document).ready(new function() {
	// Capture scroll event.
	$(document).bind('scroll', onScroll);
	// Call the layout function.
	wookmarking();
});