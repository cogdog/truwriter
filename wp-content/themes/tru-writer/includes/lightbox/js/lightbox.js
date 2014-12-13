(function($) {
 
    // Initialize the Lightbox for any links with the 'fancybox' class
    $(".fancybox").fancybox();
 
    // Initialize the Lightbox automatically for any links to images with extensions .jpg, .jpeg, .png or .gif
    // $("a[href$='.jpg'], a[href$='.png'], a[href$='.jpeg'], a[href$='.gif']").fancybox();
 
    // Initialize the Lightbox and add rel="gallery" to all gallery images when the gallery is set up using [gallery link="file"] so that a Lightbox Gallery exists
    // $(".gallery a[href$='.jpg'], .gallery a[href$='.png'], .gallery a[href$='.jpeg'], .gallery a[href$='.gif']").attr('rel','gallery').fancybox();
 
    // Initalize the Lightbox for any links with the 'video' class and provide improved video embed support
    $(".video").fancybox({
        maxWidth        : 1024,
        maxHeight       : 780,
        fitToView       : false,
        width           : '70%',
        height          : '70%',
        autoSize        : false,
        closeClick      : false,
        openEffect      : 'none',
        closeEffect     : 'none'
    });
 
})(jQuery);