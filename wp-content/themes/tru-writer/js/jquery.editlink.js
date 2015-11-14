/* Comparator: Writer Page Scripts
   code by Alan Levine @cogdog http://cogdog.info
   
   media uploader scripts somewhat lifted from
   http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
  
*/

function emailInstructions( url ) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("getEditLinkResponse").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

jQuery(document).ready(function() { 
	jQuery(document).on('click', '#getEditLink', function(e){

		// disable default behavior
		e.preventDefault();
		// initiate engines
		emailInstructions( jQuery( this ).data( 'widurl' ) );		
	});
});
