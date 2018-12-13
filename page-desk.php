<?php
/*
Template Name: Welcome Desk
*/

// ------------------------ defaults ------------------------

// defaultz
$feedback_msg = $log_out_warning = $wAccess = '';

$errors = array();

// the passcode to enter
$wAccessCode = truwriter_option('accesscode');

// ------------------------ door check -----------------------

// already logged in? go directly to the tool
if ( is_user_logged_in() ) {

	
	if ( current_user_can( 'edit_others_posts' ) ) {
		// If user has edit/admin role, send them to the tool
		
		wp_redirect( splot_redirect_url() );
  		exit;

	} else {
	
		// if the correct user already logged in, go directly to the tool
		if ( truwriter_check_user() ) {			
	  		wp_redirect( splot_redirect_url() );
  			exit;
  			
  		} else {
			// we need to force a click through a logout
			$log_out_warning = true;
			$feedback_msg = 'First, please <a href="' . wp_logout_url( home_url('/') . 'write' ) . '" class="pretty-button pretty-button-green">activate lasers</a>';
  		}
  	}	
	
  	  	
} elseif ( $wAccessCode == '')  {
	// go write something, willya?
	
	splot_user_login();
	exit;

}

// ------------------------ presets ------------------------


// verify that a  form was submitted and it passes the nonce check
if ( 	isset( $_POST['truwriter_form_access_submitted'] ) 
		&& wp_verify_nonce( $_POST['truwriter_form_access_submitted'], 'truwriter_form_access' ) ) {
 
	// grab the variables from the form
	$wAccess = 	stripslashes( $_POST['wAccess'] );
	
	// let's do some validation, store an error message for each problem found

	
	if ( $wAccess != $wAccessCode ) $errors[] = '<p><strong>Incorrect Access Code</strong> - try again? Hint: ' . truwriter_option('accesshint'); 	
	
	if ( count($errors) > 0 ) {
		// form errors, build feedback string to display the errors
		$feedback_msg = '';
		
		// Hah, each one is an oops, get it? 
		foreach ($errors as $oops) {
			$feedback_msg .= $oops;
		}
		
		$feedback_msg .= '</p>';
		
	} else {
		// send 'em on their way
		splot_user_login();
		exit;
	}

		
} // end form submmitted check
?>

<?php get_header(); ?>
			
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
			    
		   <div class="post-content section-inner thin">
		    
		    	<?php the_content(); ?>

	
				<?php if ($log_out_warning):?>
					<div class="notify notify-green"><span class="symbol icon-tick"></span>
					<?php echo $feedback_msg?>
					</div>

				<?php else:?>
		    	
			    	
		    	
						<?php  
						// set up box code colors CSS

						if ( count( $errors ) ) {
							$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
							echo $box_style . $feedback_msg . '</div>';
						}
								
				
						?>   
				

									
				<form  id="writerform" class="writenew" method="post" action="">
					
						<fieldset>
							<label for="wAccess"><?php _e('Access Code', 'radcliffe' ) ?></label><br />
							<p>Enter a proper code</p>
							<input type="text" name="wAccess" id="wAccess" class="required" value="<?php echo $wAccess; ?>" tabindex="1" />
						</fieldset>	
			
						<fieldset>
							<?php wp_nonce_field( 'truwriter_form_access', 'truwriter_form_access_submitted' ); ?>
							<input type="submit" class="pretty-button pretty-button-blue" value="Check Code" id="checkit" name="checkit" tabindex="15">
						</fieldset>
				
				</form>
				<?php endif?>
		
				<div class="clear"></div>
	
			</div>

		</div><!-- .post -->
			
				
			<?php
		
		endwhile; 
	
	endif; ?>
		
		
			
	<div class="clear"></div>
	
</div> <!-- /content -->
								
<?php get_footer(); ?>