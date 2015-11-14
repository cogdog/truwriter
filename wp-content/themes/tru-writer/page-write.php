<?php

// check for query vars that indicate this is a edit request
$wid = get_query_var( 'wid' , 0 );   // id of post
$tk  = get_query_var( 'tk', 0 );    // magic token to check

if ( ! ($wid and $tk) ) {

	// ------------------------ front gate ------------------------
	if ( !is_user_logged_in() ) {
		// not already logged in? go to desk.
		wp_redirect ( site_url() . '/desk' );
		exit;
	
	} elseif ( !current_user_can( 'edit_others_posts' ) ) {
		// okay user, who are you? we know you are not an admin or editor
		
		// if the collector user not found, we send you to the desk
		if ( !truwriter_check_user() ) {
			// now go to the desk and check in properly
			wp_redirect ( site_url() . '/desk' );
			exit;
		}
	}
}

// ------------------------ defaults ------------------------

// default welcome message
$feedback_msg = 'Enter the content for your article below. You must save first and preview once before it goes into the system. After that you can continue to edit and preview, but make sure you submit it when it is done.';

$wTitle = '';
$wAuthor = "Anonymous";
$wEmail = '';
$wText =  truwriter_option('def_text'); // pre-fill the writing area
$wCats = array( truwriter_option('def_cat')); // preload default category

// creative commons usage mode
$my_cc_mode = truwriter_option( 'use_cc' ); 
$wLicense = truwriter_option( 'cc_site' ); //default if used

$wStatus = "New, not saved";

$wHeaderImage_id = truwriter_option('defheaderimg');
$wNotes_required = truwriter_option('require_extra_info');

// not yet saved
$post_id = 0;
$revcount = 0;
$formclass = 'writenew';

// final status
$is_published = false;

// flag for re-edits
$is_re_edit = false;

// Get the attachment excerpt as a default caption
$wHeaderImageCaption = get_attachment_caption_by_id( $wHeaderImage_id );

// Parent category for published topics
$published_cat_id = get_cat_ID( 'Published' );

// no special query params, let's see if we got the right codes to do an edit.
if ( $wid and $tk ) {

	// look up the stored edit key
	$wEditKey = get_post_meta( $wid, 'wEditKey', 1 );
	
		if (  $tk == $wEditKey) {
		// keys match, we are GOLDEN
		
		// default welcome message
		$feedback_msg = 'You can now re-edit any part of this previously published writing. If you do not save any final changes, it will be left as it was before.';

		$writing = get_post( $wid );

		$wTitle = get_the_title( $wid );
		$wAuthor =  get_post_meta( $wid, 'wAuthor', 1 );
		$wEmail =  get_post_meta( $wid, 'wEmail', 1 );
		$wText = $writing->post_content; 
		
		// get categories
		$categories = get_the_category( $wid);
		foreach ( $categories as $category ) { 
			$wCats[] = $category->term_id;
		}
	
		$revcount = 1;
		$post_id = $wid;
		$wStatus = 'Re-edit (revision #' . $revcount . ' last saved ' . get_the_time( '', $wid) . ')';
 		$formclass = 'writedraft';
		$wHeaderImage_id = get_post_thumbnail_id( $wid);
		
		// Get the attachment excerpt as a default caption
		$wHeaderImageCaption = get_attachment_caption_by_id( $wHeaderImage_id );
		
		$wNotes = get_post_meta( $wid, 'wEditorNotes', 1 );
		
		// load the tags
		$wTags = implode(', ', wp_get_post_tags( $wid, array( 'fields' => 'names' ) ) );
		
		$is_re_edit = true;

		
	} else {

		// updates for display	
		$errors[] = '<strong>Token Mismatch</strong> - please check the url provided.';
		$wStatus = 'Form input error';
		$formclass = 'writeoops';	
		// default welcome message
		$feedback_msg = 'This URL does not match the edit key. Please check the link from your email again, or return to your published writing and click the button at the bottom to send an edit link.';
		$is_published = true;  // not really but it serves to hide the form.
	}
	
} 

// verify that a  form was submitted and it passes the nonce check
if ( isset( $_POST['truwriter_form_make_submitted'] ) && wp_verify_nonce( $_POST['truwriter_form_make_submitted'], 'truwriter_form_make' ) ) {
 
 		// grab the variables from the form
 		$wTitle = 					sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 					( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';
 		$wEmail = 					sanitize_text_field( $_POST['wEmail'] );			
 		$wTags = 					sanitize_text_field( $_POST['wTags'] );	
 		$wText = 					$_POST['wText'];
 		$wNotes = 					sanitize_text_field( stripslashes( $_POST['wNotes'] ) );
 		$wFooter = 					sanitize_text_field( stripslashes( $_POST['wFooter'] ) ) ;
 		$wHeaderImage_id = 			$_POST['wHeaderImage'];
 		$post_id = 					$_POST['post_id'];
 		$wCats = 					( isset ($_POST['wCats'] ) ) ? $_POST['wCats'] : array();
 		$wLicense = 				$_POST['wLicense'];
 		$wHeaderImageCaption = 		sanitize_text_field( stripslashes( $_POST['wHeaderImageCaption'] ) );
 		$revcount =					$_POST['revcount'] + 1;		
 		
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
 		
 		if ( $wTitle == '' ) $errors[] = '<strong>Title Missing</strong> - please enter an interesting title.'; 	
 		
 		if ( strlen($wText) < 8 ) $errors[] = '<strong>Missing text?</strong> - that\'s not much text, eh?';
 		
 		if ( $wHeaderImageCaption == '' ) $errors[] = '<strong>Header Image Caption Missing</strong> - please provide a description or a credit for your header image. We would like to assume it is your own image or one that is licensed for re-use.'; 
 		
 		if ( $wNotes_required == 1  AND $wNotes == '' ) $errors[] = '<strong>Extra Information Missing</strong> - please provide the requested extra information.';
 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in your entry. Please correct and try again.<ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul>';
 			
 			// updates for display
 			$revcount =	$_POST['revount'];		
 			$wStatus = 'Form input error';
 
 			$formclass = 'writeoops';
 			
 		} else {
 			
 			// good enough, let's make a post! 
 			
 			// the default category for in progress
 			$def_category_id = get_cat_ID( 'In Progress' );
 			
			$w_information = array(
				'post_title' => $wTitle,
				'post_content' => $wText,
				'post_status' => 'draft', 
				'post_category' => 	array( $def_category_id )		
			);
			
			
			// updates for display
			
			$wStatus = 'In Draft (revision #' . $revcount . ' last saved ' . get_the_time( '', $post_id) . ')';
			$formclass = 'writedraft';
 		
			if ( $post_id == 0 ) {
			
				// insert as a new post
				$post_id = wp_insert_post( $w_information );
				
				// store the author as post meta data
				add_post_meta($post_id, 'wAuthor', $wAuthor);
				
				// store the author as post meta data
				add_post_meta($post_id, 'wEmail', $wEmail);				
				
				// add the tags
				wp_set_post_tags( $post_id, $wTags);
			
				// set featured image
				set_post_thumbnail( $post_id, $wHeaderImage_id);
				
				// Add caption to featured image if there is none, this is 
				// stored as post_excerpt for attachment entry in posts table
				
				if ( !get_attachment_caption_by_id( $wHeaderImage_id ) ) {
					$i_information = array(
						'ID' => $wHeaderImage_id,
						'post_excerpt' => $wHeaderImageCaption
					);
					
					wp_update_post( $i_information );
				}
				
				// store the header image caption as post metadata
				add_post_meta($post_id, 'wHeaderCaption', $wHeaderImageCaption);
				
				// store notes for editor
				if ( $wNotes ) add_post_meta($post_id, 'wEditorNotes', $wNotes);

				// store notes for editor
				if ( $wFooter ) add_post_meta($post_id, 'wFooter', nl2br( $wFooter ) );
				
				// user selected license
				if ( $my_cc_mode != 'none' ) add_post_meta( $post_id,  'wLicense', $wLicense);
				
				// add a token for editing
				// ----h/t via http://www.sitepoint.com/generating-one-time-use-urls/
				add_post_meta( $post_id, 'wEditKey',sha1( uniqid( $wTitle, true ) ) );
				
				$feedback_msg = 'Ok, we have saved this first version of your article. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '" target="_blank">preview it now</a> (opens in a new window), or make edits and save again. ';
					
			 } else {
			// the post exists, let's update
			
				// make a copy of the category array so we can append the default category ID
				$copy_cats = $wCats;

				// check if we have a publish button click or this is a re-edit,
				// in this case we update the post with the form information
				if ( isset( $_POST['wPublish'] ) OR  $is_re_edit ) {
				
					// ---------- FINAL PUBLISH -----------
					
					// roger, we have ignition
					$is_published = true;
					
					// set the published category
					$copy_cats[] = $published_cat_id;
									
					 if ( $is_re_edit ) {
					 
					 	// revise status to pending (new ones) 
					 	
						$w_information['post_status'] = 'publish';
						$feedback_msg = 'Your writing <strong>"' . $wTitle . '"</strong> has been updated. You can view it now at  <a href="'. get_permalink( $post_id )  . '">' .  get_permalink( $post_id ) . '</a>. Enjoy!';
					 
					} else {
						// revise status to pending (new ones) 
						$w_information['post_status'] = truwriter_option('pub_status');
						
						if ( truwriter_option('pub_status') == 'pending' ) {
							$feedback_msg = 'Your writing <strong>"' . $wTitle . '"</strong> has been submitted for editorial review. When it is approved you will be able to view it at <strong>'. get_permalink( $post_id )  . '</strong>. ';

							if ( $wEmail == '' ) {
								$feedback_msg .= 'HINT- you may want to copy this link now. Got it?';
							} else {
								$feedback_msg .=  'We will notify you by email at <strong>' . $wEmail . '</strong> when it has been published to this site.';
							}
							
							// set up admin email
							$subject = 'Review newly submitted writing at ' . get_bloginfo();
					
							$message = '<strong>"' . $wTitle . '"</strong> written by <strong>' . $wAuthor . '</strong>  has been submitted to ' . get_bloginfo() . ' for editorial review. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">preview it now</a>.<br /><br /> To  publish it, simply <a href="' . admin_url( 'edit.php?post_status=pending&post_type=post') . '">find it in the submitted works</a> and change it\'s status from <strong>Draft</strong> to <strong>Publish</strong>';
							
						} else {
						
							$feedback_msg = 'Your writing <strong>"' . $wTitle . '"</strong> has been published to the site. you can view it now at  <a href="'. get_permalink( $post_id )  . '">' .  get_permalink( $post_id ) . '</a>. Enjoy!';
							
							// set up admin email
							$subject = 'Recently published writing at ' . get_bloginfo();
					
							$message = '<strong>"' . $wTitle . '"</strong> written by <strong>' . $wAuthor . '</strong>  has been published to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">view it now</a> and review / edit if needed, or just enjoy the feeling of free publishing.';
						
						}

						// Let's do some EMAIL! 
					
						// who gets mail? They do.
						$to_recipients = explode( "," ,  truwriter_option( 'notify' ) );
								
						if ( $wNotes ) $message .= '<br /><br />There are some extra notes from the author:<blockquote>' . $wNotes . '</blockquote>';
					
						// turn on HTML mail
						add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					
						// mail it!
						wp_mail( $to_recipients, $subject, $message);
					
						// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
						remove_filter( 'wp_mail_content_type', 'set_html_content_type' );				

					} //is re-edit
					
				} else {
					// in draft mode, first save
				
					// not published, attach the default category ID
					$copy_cats[] = $def_category_id ;
					
					$feedback_msg = 'Your edits have been saved. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '"  target="_blank">preview it now</a> (opens in a new window), or make edits and save again. ';
				}
			
				// add the id to our array of post information so we can issue an update
				$w_information['ID'] = $post_id;
				$w_information['post_category'] = $copy_cats;
		 
		 		// update the post
				wp_update_post( $w_information );
				
				// update the tags
				wp_set_post_tags( $post_id, $wTags);
				
				// update featured image
				set_post_thumbnail( $post_id, $wHeaderImage_id);
				
				// Update caption to featured image if there is none, this is 
				// stored as post_excerpt for attachment entry in posts table

				if ( !get_attachment_caption_by_id( $wHeaderImage_id ) ) {
					$i_information = array(
						'ID' => $wHeaderImage_id,
						'post_excerpt' => $wHeaderImageCaption
					);
					
					wp_update_post( $i_information );
				}

				// store the author's name
				update_post_meta($post_id, 'wAuthor', $wAuthor);
															
				// store the header image caption as post metadata
				update_post_meta($post_id, 'wHeaderCaption', $wHeaderImageCaption);


				// user selected license
				if ( $my_cc_mode != 'none' ) update_post_meta( $post_id,  'wLicense', $wLicense);

				// store notes for editor
				if ( $wNotes ) update_post_meta($post_id, 'wEditorNotes', $wNotes);

				// store any end notes
				if ( $wFooter ) update_post_meta($post_id, 'wFooter', nl2br( $wFooter ) );

				// log them out if they are not end editor or better
				if ( $is_published and !current_user_can( 'edit_pages' )  ) wp_logout();
				
			}
			 	
		} // count errors		
				
				
					
		
} // end form submmitted check
?>

<?php get_header(); ?>
			
<div class="content">		

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>				
	
		<div <?php post_class('post single'); ?>>
		
			<?php if ( has_post_thumbnail() ) : ?>
			
				<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail_size' ); $thumb_url = $thumb['0']; ?>
		
				<div class="featured-media">
				
					<script type="text/javascript">
	
						jQuery(document).ready(function($) {
				
							$(".featured-media").backstretch("<?php echo $thumb_url; ?>");
							
						});
						
					</script>
		
					<?php the_post_thumbnail('post-image'); ?>
	
	
						<div class="media-caption-container">
							<p class="media-caption"><?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?></p>
						</div>				
				</div> <!-- /featured-media -->
					
			<?php endif; ?>
											
			<div class="post-header section">
		
				<div class="post-header-inner section-inner">
																									
					<h2 class="post-title"><?php the_title(); ?></h2>
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
			    
		    <div class="post-content section-inner">
		    
		    	<?php the_content(); ?>
		    		
		    	<?php 
					if ( !is_user_logged_in() ) :?>
						<a href="<?php echo get_bloginfo('url')?>/wp-login.php?autologin=writer">activate lasers</a>
				<?php endif?>
			    		
		    	<?php  
		    	// set up box code colors CSS

		    	if ( count( $errors ) ) {
		    		$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
		    	} elseif ( $post_id == 0 ) {
		    		$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';
		    	} else {
		    		$box_style = '<div class="notify notify-green"><span class="symbol icon-tick"></span> ';
		    	}
		    			    	
		    	echo $box_style . $feedback_msg . '</div>';
		    	?>   
		    	
				<div class="clear"></div>
							
				<?php wp_link_pages('before=<p class="page-links">' . __('Pages:','radcliffe') . ' &after=</p>&seperator= <span class="sep">/</span> '); ?>
		    
					
		<?php endwhile; else: ?>
	
			<p><?php _e("Danger, danger Will Robinson, somethng bad happened.", "radcliffe"); ?></p>

		<?php endif; ?>
			
			
		
			
	<?php if ( is_user_logged_in() and !$is_published ) : // show form in logged in and it has not been published ?>
		
		<form  id="writerform" class="<?php echo $formclass?>" method="post" action="">
		
		<div class="writestatus">STATUS: <span class="statnow"><?php echo $wStatus?></span></div>
		
		<input name="is_previewed" type="hidden" value="<?php echo $is_previewed?>" />
		<input name="post_id" type="hidden" value="<?php echo $post_id?>" />
		<input name="revcount" type="hidden" value="<?php echo $revcount?>" />	
		
			
			
				<fieldset>
					<label for="wTitle"><?php _e('The Title', 'wpbootstrap' ) ?></label><br />
					<p>An interesting title is very important, make sure it is something that will make someone want to read what you have written.</p>
					<input type="text" name="wTitle" id="wTitle" class="required" value="<?php echo $wTitle; ?>" tabindex="1" />
				</fieldset>	
			

				<fieldset>
					<label for="wAuthor"><?php _e('How to List Author', 'wpbootstrap' ) ?></label><br />
					<p>Publish under your name, twitter handle, secret agent name, or remain "Anonymous". If you include a twitter handle such as @billyshakespeare, when someone tweets your work you will get a lovely notification.</p>
					<input type="text" name="wAuthor" id="wAuthor" class="required" value="<?php echo $wAuthor; ?>" tabindex="2" />
				</fieldset>	
				
				<fieldset>
						<label for="wText"><?php _e('Article text', 'wpbootstrap') ?></label>
						<p>Use the editing area below the tool bar to write and format your writing. You can also paste formatted content here (e.g. from MS Word or Google Dics). The editing tool will do it's best to preserve standard formatting- headings, bold, italic, lists, footnotes, and hypertext links. Click "Add Media" to upload images to include in your writing. You can also embed audio and video from many social sites simply by putting it's URL on  separate line (you will see a place holder in the editor, but the media will show in preview and when published).  Click and drag the icon in the lower right to resize the editing space.</p>
						
						<p> See more details in the  
<a class="video fancybox.iframe" href="<?php echo get_stylesheet_directory_uri()?>/includes/edit-help.html">editing tips</a>.</p>
						<?php
						// set up for inserting the WP post editor
						$settings = array( 'textarea_name' => 'wText', 'editor_height' => '400',  'tabindex'  => "3");

						wp_editor(  stripslashes( $wText ), 'wtext', $settings );
						?>
				</fieldset>

				<fieldset>
						<label for="wFooter"><?php _e('Additional Information', 'wpbootstrap') ?></label>						
						<p>Add any text you wish to append to the end, such as a citation to where it was previously published or any other meta information. URLs will be hyperlinked when published. </p>
						<textarea name="wFooter" id="wFooter" rows="15"  tabindex="4"><?php echo stripslashes( $wFooter );?></textarea>
				</fieldset>

				
				<fieldset>
					<label for="headerImage"><?php _e('Header Image', 'wpbootstrap') ?></label>
					
						
					<div class="uploader">
						<input id="wHeaderImage" name="wHeaderImage" type="hidden" value="<?php echo $wHeaderImage_id?>" />

						<?php $defthumb = wp_get_attachment_image_src( $wHeaderImage_id, 'thumbnail' );?>
					
						<img src="<?php echo $defthumb[0]?>" alt="article banner image" id="headerthumb" /><br />
					
						<input type="button" id="wHeaderImage_button"  class="btn btn-success btn-medium  upload_image_button" name="_wImage_button"  data-uploader_title="Set Header Image" data-uploader_button_text="Select Image" value="Set Header Image" tabindex="5" />
						
						</div>
						
						<p>You can upload any image file to be used in the header of what you write or choose from ones that have already been added to the site. Ideally it should be 800px in width or bigger. It will automatically be cropped along the middle of the image to like the one is you see on this page.</p><p> Any uploaded image should either be your own or one licensed for re-use; provide an attribution in the caption field below.<br clear="left"></p>
						
						<label for="wHeaderImageCaption"><?php _e('Caption/credits for header image', 'wpbootstrap') ?></label>
						<input type="text" name="wHeaderImageCaption" id="wHeaderImageCaption" value="<?php echo $wHeaderImageCaption; ?>" tabindex="6" />
					
				</fieldset>						
				
				<fieldset>
					<label for="wCats"><?php _e( 'Kind of Writing', 'wpbootstrap' ) ?></label>
					<p>Check as many that apply.</p>
					<?php 
					
					// set up arguments to get all categories that are children of "Published"
					$args = array(
						'child_of'                 => $published_cat_id,
						'hide_empty'               => 0,
					); 
					
					$article_cats = get_categories( $args );

					foreach ( $article_cats as $acat ) {
					
						$checked = ( in_array( $acat->term_id, $wCats) ) ? ' checked="checked"' : '';
						
						echo '<br /><input type="checkbox" name="wCats[]" tabindex="7" value="' . $acat->term_id . '"' . $checked . '> ' . $acat->name . ' <em style="font-size:smaller">' . $acat->description . '</em>';
					}
					
					?>
					
				</fieldset>

				<fieldset>
					<label for="wTags"><?php _e( 'Tags', 'wpbootstrap' ) ?></label>
					<p>Descriptive tags, separate multiple ones with commas</p>
					
					<input type="text" name="wTags" id="wTags" value="<?php echo $wTags; ?>" tabindex="8"  />
				</fieldset>


				<fieldset>
					<label for="wEmail"><?php _e('Your Email Address', 'wpbootstrap' ) ?> (optional)</label><br />
					<p>If you provide an email address, once your writing is published, you can request a special link that will allow you to edit it again in the future.</p>
					<input type="text" name="wEmail" id="wTitle"  value="<?php echo $wEmail; ?>" tabindex="9" />
				</fieldset>	
				

				<fieldset>
						<?php $req_state = ( $wNotes_required == 1 ) ? 'Required' : 'Optional';?>
						<label for="wNotes"><?php _e('Extra Information for Editors (' . $req_state . ')' , 'wpbootstrap') ?></label>						
						<p><?php echo truwriter_option('extra_info_prompt')?> This information will <strong>not</strong> be published with your work, it is informational for the editor's use only. </p>
						<textarea name="wNotes" id="wNotes" rows="15"  tabindex="12"><?php echo stripslashes( $wNotes );?></textarea>
				</fieldset>




					<?php if ( $my_cc_mode != 'none' ):?>
						<!-- creative commons options -->
						<fieldset>
				
					
							<?php if ( $my_cc_mode == 'site' ) :?>
					
							<label for="wLicense"><?php _e( 'Creative Commons License Applied', 'wpbootstrap' )?></label>
								<p>All writing added to this site will be licensed:</p>
								<p class="form-control"><?php echo cc_license_html( truwriter_option( 'cc_site' ), $wAuthor );?></p>
								<input type="hidden" name="wLicense" id="wLicense" value="<?php echo truwriter_option( 'cc_site' )?>">
								
				
							<?php elseif  ( $my_cc_mode == 'user' ) :?>
								<label for="wLicense"><?php _e( 'Creative Commons License',  'wpbootstrap' )?></label>
								<p>Choose your preferred license:</p>
								<select name="wLicense" id="wLicense" class="form-control">
								<option value="--">Select...</option>
								<?php echo cc_license_select_options( $wLicense )?>
								</select>				
							<?php endif; // -- cc_mode type = site or user?>
						</fieldset>
						<?php endif; // -- cc_mode != none?>

			
				<fieldset>
					<?php 
					
					wp_nonce_field( 'truwriter_form_make', 'truwriter_form_make_submitted' ); 
					
					?>
					
					<?php if ( $post_id ) :?>
					
					
					<?php
					// set up button names
					if ( $is_re_edit ) {
						$revise_btn_txt = "Review Edits";
						$save_btn_txt = "Republish Changes";
					} else {
						$revise_btn_txt = "Revise Draft";
						$save_btn_txt = "Publish Final";
					
					}
					
					
					?>
					
					
					<input type="submit" class="pretty-button pretty-button-green" value="<?php echo $revise_btn_txt?>" id="wSubDraft" name="wSubDraft" tabindex="10"> Save changes, preview again.<br /><br />
					<input type="submit" class="pretty-button pretty-button-blue" value="<?php echo $save_btn_txt?>" id="wPublish" name="wPublish" tabindex="11"> All changes completed. 
					
					<?php else:?>
					
					<input type="submit" class="pretty-button pretty-button-green" value="Save Draft" id="makeit" name="makeit" tabindex="12"> Save your first draft, then preview.
					
					
					<?php endif?>
					
					
				</fieldset>
			
				<div class="writestatus">STATUS: <span class="statnow"><?php echo $wStatus?></span></div>
		
		</form>
	<?php endif?>
			
	</div> <!-- /post -->
		
	
</div> <!-- /content -->
								
<?php get_footer(); ?>