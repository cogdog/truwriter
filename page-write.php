<?php
/*
Template Name: Writing Pad
*/

// ------------------------ defaults ------------------------

// Parent category for published topics
$published_cat_id = get_cat_ID( 'Published' );

// track errors
$errors = array();

// creative commons usage mode
$my_cc_mode = truwriter_option( 'use_cc' ); 

$is_published = $is_re_edit = false; 
$post_id = $revcount = 0;
$formclass = 'writenew';
$wStatus = "New, not saved";

$wTitle = $wEmail = $wFooter = $wTags = $wNotes = $wLicense =  '';	

// default welcome message
$feedback_msg = truwriter_form_default_prompt();

$wAuthor = "Anonymous";
$wText =  truwriter_option('def_text'); // pre-fill the writing area
$wCats = array( truwriter_option('def_cat')); // preload default category

$wHeaderImage_id = truwriter_option('defheaderimg');
$wNotes_required = truwriter_option('require_extra_info');
$wLicense = truwriter_option( 'cc_site' ); //default if used

// Get the attachment excerpt as a default caption
$wHeaderImageCaption = get_attachment_caption_by_id( $wHeaderImage_id );



// ------------------------ front gate ------------------------
	
// check for query vars that indicate this is a edit request
$wid = get_query_var( 'wid' , 0 );   // id of post
$tk  = get_query_var( 'tk', 0 );    // magic token to check

if ( ( $wid  and $tk )  ) {
	// re-edit attempt
	$is_re_edit = true;
	$formclass = 'writedraft';	
	
	// log in as author
	if ( !is_user_logged_in() ) {
		splot_user_login( 'writer', false );
	}
} 

if ( $is_re_edit and !isset( $_POST['truwriter_form_make_submitted'] )) {
	// check for first entry of re-edit.

	// look up the stored edit key
	$wEditKey = get_post_meta( $wid, 'wEditKey', 1 );


	if (  $tk == $wEditKey) {
		// keys match, we are GOLDEN

		// default welcome message for a re-edit
		$feedback_msg = truwriter_form_re_edit_prompt();

		$writing = get_post( $wid );

		$wTitle = get_the_title( $wid );
		$wAuthor =  get_post_meta( $wid, 'wAuthor', 1 );
		$wEmail =  get_post_meta( $wid, 'wEmail', 1 );
		$wText = $writing->post_content; 
		$wHeaderImage_id = get_post_thumbnail_id( $wid);

		// get categories
		$categories = get_the_category( $wid);
		foreach ( $categories as $category ) { 
			$wCats[] = $category->term_id;
		}
		// Get the attachment excerpt as a default caption
		$wHeaderImageCaption = get_attachment_caption_by_id( $wHeaderImage_id );

		$wNotes = get_post_meta( $wid, 'wEditorNotes', 1 );

		$wLicense = get_post_meta( $wid, 'wLicense', 1 );

		// load the tags
		$wTags = implode(', ', wp_get_post_tags( $wid, array( 'fields' => 'names' ) ) );
	

		$revcount = 1;
		$post_id = $wid;
		$wStatus = 'Re-edit (revision #' . $revcount . ' last saved ' . get_the_time( '', $wid) . ')';
		

	
	
		} else {

			$is_re_edit = false;

			// updates for display	
			$errors[] = '<strong>Token Mismatch</strong> - please check the url provided.';
			$wStatus = 'Form input error';
			$formclass = 'writeoops';	
			// default welcome message
			$feedback_msg = 'This URL does not match the edit key. Please check the link from your email again, or return to your published writing and click the button at the bottom to send an edit link.';
			$is_published = true;  // not really but it serves to hide the form.
		}

} 


// verify that a form was submitted and it passes the nonce check
if ( isset( $_POST['truwriter_form_make_submitted'] )  )  {
 
 		// grab the variables from the form
 		$wTitle = 					sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 					( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';
 		$wEmail = 					sanitize_text_field( $_POST['wEmail'] );			
 		$wTags = 					sanitize_text_field( $_POST['wTags'] );	
 		$wText = 					wp_kses_post( $_POST['wText'] );
 		$wNotes = 					sanitize_text_field( stripslashes( $_POST['wNotes'] ) );
 		$wFooter = 					sanitize_text_field( stripslashes( $_POST['wFooter'] ) ) ;
 		$wHeaderImage_id = 			$_POST['wHeaderImage'];
 		$post_id = 					$_POST['post_id'];
 		$wCats = 					( isset ($_POST['wCats'] ) ) ? $_POST['wCats'] : array();
 		$wLicense = 				( isset ($_POST['wLicense'] ) ) ? $_POST['wLicense'] : '';
 		$wHeaderImageCaption = 		sanitize_text_field(  $_POST['wHeaderImageCaption']  );
 		$revcount =					$_POST['revcount'] + 1;		
 		
 		// let's do some validation, store an error message for each problem found

 		
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
 			$revcount =	$_POST['revcount'];		
 			$wStatus = 'Form input error';
 
 			$formclass = 'writeoops';
 			
 		} else { // good enough, let's set up a post! 
 			
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
 		
 			// is this a first draft?
			if ( $post_id == 0 ) {
			
				// insert as a new post
				$post_id = wp_insert_post( $w_information );
				
				// store the author as post meta data
				add_post_meta($post_id, 'wAuthor', $wAuthor);
				
				// store the email as post meta data
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
				truwriter_make_edit_link( $post_id,  $wTitle );
				
				$feedback_msg = 'We have saved this first version of your writing. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '" target="_blank">preview it now</a> (opens in a new window), or make edits and save again. ';
									
			 } else { // the post exists, let's update
					
				// make a copy of the category array so we can append the default category ID
				$copy_cats = $wCats;

				// check if we have a publish button click or this is a re-edit,
				// in this case we update the post with the form information
				if ( isset( $_POST['wPublish'] ) OR  $is_re_edit ) {
											
					// roger, we have ignition
					$is_published = true;
					
					// set the published category
					$copy_cats[] = $published_cat_id;
																	
					 if ( $is_re_edit ) { 
					 
					 	$formclass = 'writedraft';	
					 	
					 	// set it as published
						$w_information['post_status'] = 'publish';

						$feedback_msg = 'Your edits to <strong>"' . $wTitle . '"</strong> has been updated. You can at  <a href="'.  get_permalink( $post_id )   . '"  target="_blank" >view the updated version now</a> (opens in a new window), make more edits, or <a href="' . site_url()  . '">return to ' . get_bloginfo() . '</a>.';
					 
					} else { // not re-edit
					
						// revise status to pending (new ones) 
						$w_information['post_status'] = truwriter_option('pub_status');
						
						if ( truwriter_option('pub_status') == 'pending' ) {
							$feedback_msg = 'Your writing <strong>"' . $wTitle . '"</strong> will appear on <strong>' . get_bloginfo() . '</strong> as soon as it has been reviewed. ';

							if ( $wEmail == '' ) {
								$feedback_msg .= '';
							} else {
								$feedback_msg .=  'We will notify you by email at <strong>' . $wEmail . '</strong> when it has been published.';
							}
							
							$feedback_msg .= ' Now please <a href="' . site_url()  . '">return to ' . get_bloginfo() . '</a>.';
							
							// set up admin email
							$subject = 'Review newly submitted writing at ' . get_bloginfo();
					
							$message = '<strong>"' . $wTitle . '"</strong> written by <strong>' . $wAuthor . '</strong>  has been submitted to ' . get_bloginfo() . ' for editorial review. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">preview it now</a>.<br /><br /> To  publish simply <a href="' . admin_url( 'edit.php?post_status=pending&post_type=post') . '">find it in the submitted works</a> and change it\'s status from <strong>Draft</strong> to <strong>Publish</strong>';
							
						} else {
							$feedback_msg = 'Your writing <strong>"' . $wTitle . '"</strong> has been published to <strong>' . get_bloginfo(). '</strong>. You can  <a href="'.  get_permalink( $post_id )   . '" >view it now</a> or <a href="' . site_url()  . '">return to ' . get_bloginfo() . '</a>.';
							
							// set up admin email
							$subject = 'Recently published writing at ' . get_bloginfo();
					
							$message = '<strong>"' . $wTitle . '"</strong> written by <strong>' . $wAuthor . '</strong>  has been published to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">view it now</a> and review / edit if needed, or just enjoy the feeling of being published.';
							
							// if user provided email address, send instructions to use link to edit
							if ( !isset($wEmail) ) truwriter_mail_edit_link();
						
						} // is_status pending
						

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
					
					// logout the special user if we are publishing				
																	
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


				// update the email as post meta data
				update_post_meta($post_id, 'wEmail', $wEmail);	
				
																			
				// store the header image caption as post metadata
				update_post_meta($post_id, 'wHeaderCaption', $wHeaderImageCaption);

				
				// user selected license
				if ( $my_cc_mode != 'none' ) update_post_meta( $post_id,  'wLicense', $wLicense);

				// store notes for editor
				if ( $wNotes ) update_post_meta($post_id, 'wEditorNotes', $wNotes);

				// store any end notes
				if ( $wFooter ) update_post_meta($post_id, 'wFooter', nl2br( $wFooter ) );
				
				if ( truwriter_check_user()=== true and isset( $_POST['wPublish'] ) ) wp_logout();

								
			} // post_id = 0
						 	
		} // count errors	
						
} // end form submmitted check

get_header();
?>

<div class="content">		

	<?php if ( have_posts() ) : 
		
		while ( have_posts() ) : the_post(); ?>
	
			<div <?php post_class( 'post single' ); ?>>
		
				<?php if ( has_post_thumbnail() ) : ?>
					
					<div class="featured-media" style="background-image: url( <?php the_post_thumbnail_url( $post->ID, 'post-image' ); ?> );">
			
						<?php 
						
						the_post_thumbnail( 'post-image' );
						
						$image_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
						
						if ( $image_caption ) : 
							?>
												
							<div class="media-caption-container">
								<p class="media-caption"><?php echo $image_caption; ?></p>
							</div>
							
						<?php endif; ?>
						
					</div><!-- .featured-media -->
						
				<?php endif; ?>

											
			<div class="post-header section">
		
				<div class="post-header-inner section-inner">
																									
					<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
				
				</div><!-- .post-header-inner section-inner -->
														
			</div><!-- .post-header section -->
			    
		    <div class="post-content section-inner medium">
		    
		    	<?php the_content(); ?>
		    		
			    		
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
			
			
		
			
	<?php if ( is_user_logged_in() and (!$is_published or $is_re_edit) ) : // show form in logged in and it has not been published ?>
		
		<form  id="writerform" class="<?php echo $formclass?>" method="post" action="">
		
		<div class="writestatus">STATUS: <span class="statnow"><?php echo $wStatus?></span></div>
		
		<input name="post_id" type="hidden" value="<?php echo $post_id?>" />
		<input name="revcount" type="hidden" value="<?php echo $revcount?>" />	
		
			
			
				<fieldset id="theTitle">
					<label for="wTitle"><?php truwriter_form_item_title() ?></label><br />
					<p><?php truwriter_form_item_title_prompt()?></p>
					<input type="text" name="wTitle" id="wTitle" class="required writerfield" value="<?php echo $wTitle; ?>" tabindex="1" />
				</fieldset>	
			

				<fieldset id="theAuthor">
					<label for="wAuthor"><?php truwriter_form_item_byline() ?></label><br />
					<p><?php truwriter_form_item_byline_prompt() ?></p>
					<input type="text" name="wAuthor" id="wAuthor" class="required writerfield" value="<?php echo $wAuthor; ?>" tabindex="2" />
				</fieldset>	
				
				<fieldset id="theText">
						<label for="wText"><?php truwriter_form_item_writing_area() ?></label>
						<p><?php truwriter_form_item_writing_area_prompt() ?></p>
						
						<p> See details on the  tools in the  
<a class="video fancybox.iframe" href="<?php echo get_stylesheet_directory_uri()?>/includes/edit-help.html">editing tips</a>.</p>
						<?php
						// set up for inserting the WP post editor
						$settings = array( 
							'textarea_name' => 'wText', 
							'editor_height' => '400', 
							'tabindex'  => "3", 
							'drag_drop_upload' => true, 
						);

						wp_editor(  stripslashes( $wText ), 'wtext', $settings );
						?>
				</fieldset>


				<?php if (truwriter_option('show_footer') ):?>
				<fieldset id="theFooter">
						<label for="wFooter"><?php truwriter_form_item_footer() ?></label>						
						<p><?php truwriter_form_item_footer_prompt() ?></p>
						<textarea name="wFooter" id="wFooter" class="writerfield" rows="15"  tabindex="4"><?php echo stripslashes( $wFooter );?></textarea>
				</fieldset>
				<?php endif?>
				
				<fieldset id="theHeaderImage">
					<label for="headerImage"><?php truwriter_form_item_header_image() ?></label>
					
						
					<div class="uploader">
						<input id="wHeaderImage" name="wHeaderImage" type="hidden" value="<?php echo $wHeaderImage_id?>" />

						<?php 
						
						if ($wHeaderImage_id) {
							$defthumb = wp_get_attachment_image_src( $wHeaderImage_id, 'thumbnail' );
						} else {
							$defthumb = [];
							$defthumb[] = get_stylesheet_directory_uri() . '/images/default-header-thumb.jpg';
							$wHeaderImageCaption = 'flickr photo by Lívia Cristina https://flickr.com/photos/liviacristinalc/3402221680 shared under a Creative Commons (BY-NC-ND) license';
						}
						
						?>
					
						<img src="<?php echo $defthumb[0]?>" alt="article banner image" id="headerthumb" /><br />
					
						<input type="button" id="wHeaderImage_button"  class="btn btn-success btn-medium  upload_image_button" name="_wImage_button"  data-uploader_title="Set Header Image" data-uploader_button_text="Select Image" value="Set Header Image" tabindex="5" />
						
						</div>
						
						<p><p><?php truwriter_form_item_header_image_prompt() ?><br clear="left"></p>
						
						<label for="wHeaderImageCaption"><?php truwriter_form_item_header_caption() ?></label>
						<p><?php truwriter_form_item_header_caption_prompt() ?></p>
						<input type="text" name="wHeaderImageCaption" class="writerfield" id="wHeaderImageCaption" value="<?php echo htmlentities( stripslashes( $wHeaderImageCaption ), ENT_QUOTES); ?>" tabindex="6" />
				
				</fieldset>						
				
				
				<?php if (truwriter_option('show_cats') ):?>
				
				<fieldset  id="theCats">
					<label for="wCats"><?php truwriter_form_item_categories() ?></label>
					<p><?php truwriter_form_item_categories_prompt() ?></p>
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

				<?php endif?>
				
				<?php if (truwriter_option('show_tags') ):?>
				
				<fieldset id="theTags">
					<label for="wTags"><?php truwriter_form_item_tags() ?></label>
					<p><?php truwriter_form_item_tags_prompt() ?></p>
					
					<input type="text" name="wTags" id="wTags" class="writerfield" value="<?php echo $wTags; ?>" tabindex="8"  />
				</fieldset>

				<?php endif?>

				<fieldset id="theEmail">
					<label for="wEmail"><?php truwriter_form_item_email() ?> (optional)</label><br />
					<p><?php truwriter_form_item_email_prompt() ?> </p>
					<input type="text" name="wEmail" id="wTitle" class="writerfield"  value="<?php echo $wEmail; ?>" autocomplete="on" tabindex="9" />
				</fieldset>	
				

				<?php if ( $wNotes_required != -1 ):?>
				
				<fieldset id="theNotes">
						<?php $req_state = ( $wNotes_required == 1 ) ? 'required' : 'optional';?>
						<label for="wNotes"><?php truwriter_form_item_editor_notes(); _e(' (' . $req_state . ')' , 'radcliffe') ?></label>						
						<p><?php truwriter_form_item_editor_notes_prompt()?></p>
						<textarea name="wNotes" class="writerfield" id="wNotes" rows="15"  tabindex="12"><?php echo stripslashes( $wNotes );?></textarea>
				</fieldset>
				<?php endif?>



					<?php if ( $my_cc_mode != 'none' ):?>
						<!-- creative commons options -->
						<fieldset  id="theLicense">
				
							<label for="wLicense"><?php truwriter_form_item_license()?></label>
							<?php if ( $my_cc_mode == 'site' ) :?>
					
								<p>All writing added to this site will be published under a rights statement like:</p>
								
								<p class="form-control"><?php echo truwriter_license_html( truwriter_option( 'cc_site' ), $wAuthor );?></p>
								<input type="hidden" name="wLicense" id="wLicense" value="<?php echo truwriter_option( 'cc_site' )?>">
								
				
							<?php elseif  ( $my_cc_mode == 'user' ) :?>
								
								<p><?php truwriter_form_item_license_prompt()?></p>
								
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
							$save_btn_txt = "Republish Changes";
						} else {
							$save_btn_txt = "Publish Now";
							echo '<input type="submit" class="pretty-button pretty-button-green" value="Revise Draft" id="wSubDraft" name="wSubDraft" tabindex="10"> Save changes, preview again.<br /><br />';
						}
					
					?>
						
						
						<input type="submit" class="pretty-button pretty-button-blue" value="<?php echo $save_btn_txt?>" id="wPublish" name="wPublish" tabindex="11"> All changes completed. 
					
					<?php else:?>
					
						<input type="submit" class="pretty-button pretty-button-green" value="Save Draft" id="makeit" name="makeit" tabindex="12"> Save your first draft, then preview.
					
					<?php endif?>
					
					
				</fieldset>
			
				<div class="writestatus">STATUS: <span class="statnow"><?php echo $wStatus?></span></div>
		
		</form>
	<?php endif?>
	
	<div class="clear"></div>
			
	</div> <!-- /post -->
		

<div class="clear"></div>	
</div> <!-- /content -->
								
<?php get_footer(); ?>