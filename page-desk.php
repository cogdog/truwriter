<?php
/*
Template Name: Welcome Desk
*/
?>

<?php get_header();?>
			
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
			    
		   <div class="post-content section-inner <?php truwriter_layout_width()?>">
		    
		    	<?php the_content(); ?>
		    	
		    	<?php
			//passcode to enter
			$wAccessCode = truwriter_option('accesscode');
	
			// holder for output and the passcode needed to enter
			$output = $wAccess =  '';

			// already logged in but as different user on multisite?
	
			if ( is_user_logged_in() and !truwriter_check_user()  ) {
				// we need to force a click through a logout
				return '<div class="notify notify-green"><span class="symbol icon-tick"></span>' .'First! <a href="' . splot_redirect_url() . '" class="pretty-button pretty-button-green">activate the writing tool</a>.</div>';	  	
			}

			// verify that a  form was submitted and it passes the nonce check
			if ( isset( $_POST['truwriter_form_access_submitted'] ) 
					&& wp_verify_nonce( $_POST['truwriter_form_access_submitted'], 'truwriter_form_access' ) ) {

				// grab the variables from the form
				$wAccess = 	stripslashes( $_POST['wAccess'] );

				// let's do some validation of the code

				if ( $wAccess != $wAccessCode ) {
					$output .= '<div class="notify notify-red"><span class="symbol icon-error"></span> <p><strong>Incorrect Access Code</strong> - try again? Hint: ' . truwriter_option('accesshint') . '</p></div>'; 	

				} // end form submmitted check
			}		
				// add the form code	
				$output .= '<form  id="writerform" class="writenew" method="post" action="">
							<fieldset>
								<label for="wAccess">' . __('Access Code', 'radcliffe' ) . '</label><br />
								<p>Enter a proper code</p>
								<input type="text" name="wAccess" id="wAccess" class="required" value="' . $wAccess . '" tabindex="1" />
							</fieldset>	
		
							<fieldset>' . wp_nonce_field( 'truwriter_form_access', 'truwriter_form_access_submitted' ) . '
								<input type="submit" class="pretty-button pretty-button-blue" value="Check Code" id="checkit" name="checkit" tabindex="15">
							</fieldset>
					</form>';
	
			echo $output;		    		
		    	
		    	?>
		
				<div class="clear"></div>
	
			</div>

		</div><!-- .post -->
			
				
			<?php
		
		endwhile; 
	
	endif; ?>
		
		
			
	<div class="clear"></div>
	
</div> <!-- /content -->
								
<?php get_footer(); ?>