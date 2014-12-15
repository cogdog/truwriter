<?php
/*
Template Name: Write Something

Generates the form for doing all the writing and previewing
*/



if ( !is_user_logged_in() ) {
	// already not logged in? go to desk.
  	wp_redirect ( site_url() . '/desk' );
  	exit;
  	
} elseif ( !current_user_can( 'edit_others_posts' ) ) {
	// okay user, who are you? we know you are not an admin or editor
	
	$user = get_user_by( 'login', 'writer');
	
	// if the writer user not found, we send you to the desk
	if ( !$user ) {
		// log out, you!
		wp_logout();
		
		// now go to the desk and check in properly
	  	wp_redirect ( site_url() . '/desk' );
  		exit;
  	}
}

		

// ------------------------ defaults ------------------------

// default welcome message
$feedback_msg = 'Enter the content for your article below. You must save first and preview once before it goes into the system.';

$wTitle = "My Article Title";
$wAuthor = "Anonymous";
$wCats = array();

$wHeaderImage_id = truwriter_option('defheaderimg');

// ------------------------ presets ------------------------
// not yet saved
$post_id = 0;

// final status
$is_published = false;

// Get the attachment excerpt as a default caption
$wHeaderImageCaption = get_attachment_caption_by_id( $wHeaderImage_id );

// Parent category for published topics
$published_cat_id = get_cat_ID( 'Published' );

// verify that a  form was submitted and it passes the nonce check
if ( isset( $_POST['truwriter_form_make_submitted'] ) && wp_verify_nonce( $_POST['truwriter_form_make_submitted'], 'truwriter_form_make' ) ) {
 
 		// grab the variables from the form
 		$wTitle = 					sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 					( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';		
 		$wTags = 					sanitize_text_field( $_POST['wTags'] );	
 		$wText = 					$_POST['wText'];
 		$wNotes = 					sanitize_text_field( stripslashes( $_POST['wNotes'] ) );
 		$wHeaderImage_id = 			$_POST['wHeaderImage'];
 		$post_id = 					$_POST['post_id'];
 		$wCats = 					( isset ($_POST['wCats'] ) ) ? $_POST['wCats'] : array();
 		$wHeaderImageCaption = 		sanitize_text_field( stripslashes( $_POST['wHeaderImageCaption'] ) );
 		
 		
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
 		
 		if ( $wTitle == '' ) $errors[] = '<strong>Title Missing</strong> - please enter an interesting title.'; 	
 		
 		if ( strlen($wText) < 100 ) $errors[] = '<strong>Missing text?</strong> - that\'s not much text, eh?';
 		
 		if ( $wHeaderImageCaption == '' ) $errors[] = '<strong>Header Image Caption Missing</strong> - please provide a description or a credit for your header image. We would like to assume it is your own or one that is licensed for re-use'; 
 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in your entry. Please correct and try again.<ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul>';
 			
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

			if ( $post_id == 0 ) {
			
				// insert as a new post
				$post_id = wp_insert_post( $w_information );
				
				// store the author as post meta data
				add_post_meta($post_id, 'wAuthor', $wAuthor);
				
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
				
				$feedback_msg = 'Ok, we have saved this first version of your article. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '" target="_blank">preview it now</a> (opens in a new window), or make edits and save again. ';
					
			 } else {
			// the post exists, let's update
			
				// make a copy of the category array so we can append the default category ID
				$copy_cats = $wCats;

				// check if we have a publish button click
				if ( isset( $_POST['wPublish'] ) ) {
				
					// roger, we have ignition
					$is_published = true;
					
					// set the published category
					$copy_cats[] = $published_cat_id;
					
					$feedback_msg = 'Your writing <strong>"' . $wTitle . '"</strong> has been submitted for editorial review. When published, you will be able to view it at <strong>'. get_permalink( $post_id )  . '</strong>. You may want to copy and save this link now';
					
					// add here a function to notify via email??
					
					// who gets mail? They do.
					$to_recipients = explode( "," ,  truwriter_option( 'notify' ) );
					
					$subject = 'Review newly submitted writing at ' . get_bloginfo();
					
					$message = 'A writing <strong>"' . $wTitle . '"</strong> written by <strong>' . $wAuthor . '</strong>  has been submitted to ' . get_bloginfo() . ' for editorial review. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">preview it now</a>.<br /><br /> To  publish it, simply <a href="' . admin_url( 'edit.php?post_status=draft&post_type=post&cat=' . get_cat_ID( 'Published' ) ) . '">find it in the submitted works</a> and change it\'s status from <strong>Draft</strong> to <strong>Publish</strong>';
					
					if ( $wNotes ) $message .= '<br /><br />There are some extra notes from the author:<blockquote>' . $wNotes . '</blockquote>';
					
					// turn on HTML mail
					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					
					// mail it!
					wp_mail( $to_recipients, $subject, $message);
					
					// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );				
					
				} else {
					// not published, attach the default category ID
					$copy_cats[] = $def_category_id ;
					
					$feedback_msg = 'Your edits have been saved.. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '"  target="_blank">preview it now</a> (opens in a new window), or make edits and save again. ';
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
				
				// store notes for editor
				if ( $wNotes ) update_post_meta($post_id, 'wEditorNotes', $wNotes);

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
			
		<form  id="comparatorform" class="comparatorform" method="post" action="" enctype="multipart/form-data">
		
		<input name="is_previewed" type="hidden" value="<?php echo $is_previewed?>" />
		<input name="post_id" type="hidden" value="<?php echo $post_id?>" />		
			
				<fieldset>
					<label for="wTitle"><?php _e('Article Title', 'wpbootstrap' ) ?></label><br />
					<p>An interesting title goes a long way!</p>
					<input type="text" name="wTitle" id="wTitle" class="required" value="<?php echo $wTitle; ?>" tabindex="1" />
				</fieldset>	
			

				<fieldset>
					<label for="wAuthor"><?php _e('How to List Author', 'wpbootstrap' ) ?></label><br />
					<p>Publish under your name, or your secret agent name, or leave as "Anonymous"</p>
					<input type="text" name="wAuthor" id="wAuthor" class="required" value="<?php echo $wAuthor; ?>" tabindex="2" />
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
						
						echo '<br /><input type="checkbox" name="wCats[]" tabindex="3" value="' . $acat->term_id . '"' . $checked . '> ' . $acat->name;
					}
					
					?>
					
				</fieldset>

			
				<fieldset>
					<label for="wTags"><?php _e( 'Tags (optional)', 'wpbootstrap' ) ?></label>
					<p>Descriptive tags, separate multiple ones with commas</p>
					
					<input type="text" name="wTags" id="wTags" value="<?php echo $wTags; ?>"  />
				</fieldset>

				<fieldset>
					<label for="headerImage"><?php _e('Upload or Select Header Image', 'wpbootstrap') ?></label>
						
					<div class="uploader">
						<input id="wHeaderImage" name="wHeaderImage" type="hidden" value="<?php echo $wHeaderImage_id?>" />
					
					
						<?php $defthumb = wp_get_attachment_image_src( $wHeaderImage_id, 'thumbnail' );?>
					
						<img src="<?php echo $defthumb[0]?>" alt="article banner image" id="headerthumb" /><br />
					
						<input type="button" id="wHeaderImage_button" tabindex="4" class="btn btn-success btn-medium  upload_image_button" name="_wImage_button"  data-uploader_title="Set Article Header Image" data-uploader_button_text="Select Image" value="Set Image" tabindex="1" />
						<br /><br />
						
						<label for="wHeaderImageCaption"><?php _e('Caption/credits for header image', 'wpbootstrap') ?></label>
						<input type="text" name="wHeaderImageCaption" id="wHeaderImageCaption" value="<?php echo $wHeaderImageCaption; ?>" tabindex="5" />
					</div>
				</fieldset>			

				<fieldset>
						<label for="wText"><?php _e('Article text', 'wpbootstrap') ?></label>
						<p>You can copy/paste formatted content here; The TRU Writer will preserve standard headings, bold, italic, lists, footnotes, and hypertext links. See more 
<a class="video fancybox.iframe" href="<?php echo get_stylesheet_directory_uri()?>/includes/edit-help.html">editing tips</a>.</p>
						<?php
						// set up for inserting the WP post editor
						$settings = array( 'textarea_name' => 'wText', 'textarea_rows' => 40 );

						wp_editor(  stripslashes( $wText ), 'wtext', $settings );

						?>

				</fieldset>

				<fieldset>
						<label for="wNotes"><?php _e('Notes to the Editor', 'wpbootstrap') ?></label>						
						<p>Add any notes or messages to the editor, will not be part of published article. If you want to be contacted, you will have to leave some form of contact.</p>
						<textarea name="wNotes" id="wNotes" rows="15"  tabindex="10"><?php echo stripslashes( $wNotes );?></textarea>
				</fieldset>

			
				<fieldset>
					<?php 
					
					wp_nonce_field( 'truwriter_form_make', 'truwriter_form_make_submitted' ); 
					
					$save_btn_value = ( $post_id ) ? 'Save Draft' : 'Revise Draft';
					?>
					
					<?php if ( $post_id ) :?>
					
					<?php echo $box_style . $feedback_msg . '</div>';?>
					
					<input type="submit" class="pretty-button pretty-button-green" value="Revise Draft" id="wSubDraft" name="wSubDraft" tabindex="15"> Save changes, preview again.<br /><br />
					<input type="submit" class="pretty-button pretty-button-blue" value="Publish Final" id="wPublish" name="wPublish" tabindex="16"> All changes completed. You cannot edit once this is done.
					
					<?php else:?>
					
					<input type="submit" class="pretty-button pretty-button-green" value="Save Draft" id="makeit" name="makeit" tabindex="15"> Save your first draft, then preview.
					
					
					<?php endif?>
					
					
				</fieldset>
			
						
		</form>
	<?php endif?>
			
	</div> <!-- /post -->
	
</div> <!-- /content -->
								
<?php get_footer(); ?>