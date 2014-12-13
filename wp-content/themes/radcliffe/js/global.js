jQuery(document).ready(function($) {
	
	
	// toggle blog-menu
	$(".nav-toggle").on("click", function(){	
		$(this).toggleClass("active");
		$(".mobile-menu-container").slideToggle();
	});
	
	
	// Toggle search form
	$(".search-toggle").on("click", function(){	
		$(this).toggleClass("active");
		$(".header-search-block").slideToggle();
		$(".header-search-block #s").focus();
		return false;
	});
	
	
	// Hide mobile-menu > 1000
	$(window).resize(function() {
		if ($(window).width() > 1000) {
			$(".nav-toggle").removeClass("active");
			$(".mobile-menu-container").hide();
		}
	});
	
	
	// Hide header search block at < 1000
	$(window).resize(function() {
		if ($(window).width() < 1000) {
			$(".search-toggle").removeClass("active");
			$(".header-search-block").hide();
		}
	});
	
	
	// Smooth scroll to the top	
    $('.tothetop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 500);
        return false;
    });
    
    
    // resize videos after container
	var vidSelector = ".post iframe, .post object, .post video, .widget-content iframe, .widget-content object, .widget-content iframe";	
	var resizeVideo = function(sSel) {
		$( sSel ).each(function() {
			var $video = $(this),
				$container = $video.parent(),
				iTargetWidth = $container.width();

			if ( !$video.attr("data-origwidth") ) {
				$video.attr("data-origwidth", $video.attr("width"));
				$video.attr("data-origheight", $video.attr("height"));
			}

			var ratio = iTargetWidth / $video.attr("data-origwidth");

			$video.css("width", iTargetWidth + "px");
			$video.css("height", ( $video.attr("data-origheight") * ratio ) + "px");
		});
	};

	resizeVideo(vidSelector);

	$(window).resize(function() {
		resizeVideo(vidSelector);
	});
	
});