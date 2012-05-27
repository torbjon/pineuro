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
	api_key = "HTMQFSCKKB",
	current_page = 1;
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
						$("#tiles").append("<li><a class='imagepopup' href='#popup'><img width='"+this.width+"' height='"+this.height+"' data-description='"+encodeURIComponent(item['dc:description'])+"' data-originaluri='"+item['europeana:uri']+"' data-provider='"+item['europeana:provider']+"' data-country='"+item['europeana:country']+"' data-imgsrc='"+item['europeana:object'].replace(/\s/g,"%20")+"' data-title='"+item['dc:title']+"' src='http://social.apps.lv/image.php?w=200&zc=3&src="+encodeURIComponent(item['europeana:object'])+"' /></a></li>")
						if(handler) handler.wookmarkClear();
						wookmarking();
					//}
				}
			})
		})
	})
	current_page++
}
$(function(){
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
		$("#popup_img").css('background-image', 'url(http://social.apps.lv/image.php?cc=333&w=470&h=470&zc=2&src='+encodeURIComponent($(this).children("img").data("imgsrc"))+')')
		if($(this).children("img").data("title") != undefined){
			$("#popup_img_title").html($(this).children("img").data("title"))
			$("#datacountry").html($(this).children("img").data("country").capitalize())
			$("#dataprovider").html($(this).children("img").data("provider"))
			$("#dataoriginaluri").html('<a target="_blank" href="'+$(this).children("img").data("originaluri")+'">'+$(this).children("img").data("originaluri")+'</a>')
			if($(this).children("img").data("description") != undefined){
				$("#datadescription").html(decodeURIComponent($(this).children("img").data("description")))
			}
		} else {
			$("#popup_img_title").html("")
		}
		fancing();
		return false
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
		wookmarking();
	}
};
$(document).ready(new function() {
	// Capture scroll event.
	$(document).bind('scroll', onScroll);
	// Call the layout function.
	wookmarking();
});