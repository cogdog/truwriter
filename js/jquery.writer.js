/* TRU Writer Page Scripts
   code by Alan Levine @cogdog http://cog.dog
   
   // upload for input field style by CSS to be a drop zone
  
*/

jQuery('#wTags').suggest( writerObject.siteURL + "/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});

jQuery(document).ready(function() { 
	// called for change in input drop zone (or click)

	jQuery('#splotdropzone input').change(function () {

		if (this.value) {
			// prompt for drop area
			
			// get the file size
			let file_size_MB = (this.files[0].size / 1000000.0).toFixed(2);
			
			if ( file_size_MB >  parseFloat(writerObject.uploadMax)) { 
            	alert('Error: The size of your image, ' + file_size_MB + ' Mb, is greater than the maximum allowed for this site (' + writerObject.uploadMax + ' Mb). Try a different file or see if you can shrink the size of this one.');
            	jQuery('#wUploadImage').val("");
            } else {
            	
            	
            	jQuery('#wDefThumbURL').text( jQuery('#headerthumb').attr('src'));
				jQuery('#dropmessage').text('Selected Image: ' + this.value.substring(12));
	
				// generate a preview of image in the thumbnail source
				// h/t https://codepen.io/waqasy/pen/rkuJf
				if (this.files && this.files[0]) {
					var freader = new FileReader();

					freader.onload = function (e) {
						jQuery('#headerthumb').attr('src', e.target.result);
					};

					freader.readAsDataURL(this.files[0]);			
					
					 // update status 
				 	jQuery("#uploadresponse").html('Image selected. When you <strong>Save/Update</strong> below this file will be uploaded (' + file_size_MB + ' Mb).');
				 	
				 	// store the image caption in hidden div
					jQuery("#footlocker").text(jQuery("#wHeaderImageCaption").val());
 
				 	// clear the caption 
					jQuery("#wHeaderImageCaption").val('');

				} else {
					// no files received?
					 reset_dropzone();
				}
			}
			
		} else {
			// cancel clicked
			reset_dropzone();
		}
	});


	jQuery("#headerthumb").click(function(){
		jQuery("#splotdropzone input").click();
	});	
	
	function reset_dropzone() {
		//reset thumbnail preview
		jQuery('#headerthumb').attr('src', jQuery('#wDefThumbURL').text());
		
		// clear status field
		jQuery("#uploadresponse").text('');

		// reset drop zone prompt
		jQuery('#dropmessage').text('Drag file or click to select one to upload'); 
		
		 // return the image credit in hidden div
		jQuery("#wHeaderImageCaption").val(jQuery("#footlocker").text());

	}

	
});
