<?php get_header(); ?>

<div class="content">
											        
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php if ( has_post_thumbnail() ) : ?>
			
				<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'post-image' ); $url = $thumb['0']; ?>
		
				<div class="featured-media">
				
					<script type="text/javascript">
	
						jQuery(document).ready(function($) {
				
							$(".featured-media").backstretch("<?php echo $url; ?>");
							
						});
						
					</script>
		
					<?php the_post_thumbnail('post-image'); ?>
										
						<div class="media-caption-container">
						
							<p class="media-caption"><?php echo get_post_meta( $post->ID, 'wHeaderCaption', 1 );  ?></p>
							
						</div>
						

					
				</div> <!-- /featured-media -->
					
			<?php endif; ?>
				
			<div class="post-header section">
		
				<div class="post-header-inner section-inner medium">
													
					<p class="post-meta-top"><a href="<?php the_permalink(); ?>" title="<?php the_time('h:i'); ?>"><?php the_time(get_option('date_format')); ?></a> <?php if ( comments_open() and truwriter_option('allow_comments') ) { echo '<span class="sep">|</span> '; comments_popup_link( '0 comments', '1 comment', '% comments', 'post-comments' ); } ?> <?php echo '<span class="sep">|</span> Reading Time: ~'; $readtime = do_shortcode( '[est_time]' ); echo $readtime; ?> </p>
														
					<h2 class="post-title"><?php the_title(); ?></h2>
					
					<p class="theauthor"><?php $wAuthor=  get_post_meta( $post->ID, 'wAuthor', 1 ); echo twitternameify( $wAuthor );?></p>
					
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
			    
		    <div class="post-content section-inner thin">
		    
		    	<?php the_content(); ?>
		    	
		    	<hr />
		    	<?php $wFooter = get_post_meta( $post->ID, 'wFooter', 1 ); if ($wFooter) echo '<p><em>' . make_clickable( $wFooter ) . '</em></p>';?>
		    	

		    	<div class="clear"></div>
		    	
		    	<?php wp_link_pages('before=<p class="page-links">' . __('Pages:','radcliffe') . ' &after=</p>&separator=<span class="sep">/</span>'); ?>
		    
		    </div>
		    
			<div class="post-meta section-inner thin">
			
				<div class="meta-block post-author">
				
					<h3 class="meta-title"><?php _e('SPLOT WRITTEN','radcliffe'); ?></h3>
					
					<div class="post-author-container">
				
						<img src="<?php echo get_stylesheet_directory_uri();?>/images/writer.png"  class="avatar avatar-160 photo" height="160" width="160" alt="" />
						
						<div class="post-author-inner">
						 
						 
							
							<p class="author-description"><strong>Author:</strong> <?php echo $wAuthor; ?></p>
							
							<p class="author-description"><strong>Published:</strong> <?php the_time(get_option('date_format')); ?></p>
							<p class="author-description"><strong>Word Count:</strong> <?php  echo str_word_count( get_the_content());?> </p>
							<p class="author-description"><strong>Reading time:</strong> ~<?php echo $readtime?></p>
							
							<?php
							
							// show the request edit link button if they have provided an email and post is published
							if ( get_post_meta( $post->ID, 'wEmail', 1 ) and get_post_status() == 'publish' ) {
								echo '<p class="author-description"><strong>Edit Link:</strong> <em>(emailed to author)</em><br /> <a href="#" id="getEditLink" class="pretty-button pretty-button-blue" data-widurl="' . get_bloginfo('url') . '/get-edit-link/?wid=' .   $post->ID . '">Request Now</a> <span id="getEditLinkResponse" class="writenew"></span></p>';
							}
							?>
							
							
							
							<?php if ( truwriter_option( 'use_cc' ) != 'none' ):?>					
								<!-- creative commons -->
								<p class="author-description"><strong>License: </strong><br />
								<?php 
									// get the license code, either define for site or post meta for user assigned						
									$cc_code = ( truwriter_option( 'use_cc' ) == 'site') ? truwriter_option( 'cc_site' ) : get_post_meta($post->ID, 'wLicense', true);
									echo cc_license_html( $cc_code, $wAuthor, get_the_time( "Y", $post->ID ) );

								?>		
							
								</p> <!-- /creative commons -->
							<?php endif?>

							
							<p class="author-description"><strong>Share: </strong> <a href="https://twitter.com/share" class="twitter-share-button" data-hashtags="splot" 
<a href="https://twitter.com/share" class="twitter-share-button" data-text="Published at TRU Writer: <?php echo addslashes(get_the_title())?> by <?php echo $wAuthor?>" data-hashtags="splot" data-dnt="true">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></p>
							
							
						
						</div>
						
						<div class="clear"></div>
					
					</div>
				
				</div> <!-- /post-author -->
				
				<div class="meta-block post-cat-tags">
				
					<h3 class="meta-title"><?php _e('ABOUT','radcliffe'); ?></h3>
				
					<p class="post-categories">
												
						<?php the_category(', '); ?>
					
					</p>
					
					<?php if ( has_tag() ) : ?>
						
						<p class="post-tags">
																			
							<?php the_tags('', ', '); ?>
						
						</p>
					
					<?php endif; ?>
				
					<div class="post-nav">
		
						<?php
						$next_post = get_next_post();
						if (!empty( $next_post )): ?>
						
							<p class="post-nav-next">
													
								<a title="<?php _e('Next Writing:', 'radcliffe'); echo ' ' . get_the_title($next_post); ?>" href="<?php echo get_permalink( $next_post->ID ); ?>"><?php echo get_the_title($next_post); ?></a>
							
							</p>
					
						<?php endif; ?>
						
						<?php
						$prev_post = get_previous_post();
						if (!empty( $prev_post )): ?>
						
							<p class="post-nav-prev">
					
							<a title="<?php _e('Previous Writing:', 'radcliffe'); echo ' ' . get_the_title($prev_post); ?>" href="<?php echo get_permalink( $prev_post->ID ); ?>"><?php echo get_the_title($prev_post); ?></a>
							
							</p>
					
						<?php endif; ?>
						
						<div class="clear"></div>
					
					</div> <!-- /post-nav -->
				
				</div> <!-- /post-cat-tags -->
				
				<div class="clear"></div>
								
			</div> <!-- /post-meta -->
													                                    	        	        
		</div> <!-- /post -->
				
				<?php if ( truwriter_option('allow_comments') ) comments_template( '', true ); ?>
				
									                        
   	<?php endwhile; else: ?>

		<p><?php _e("We couldn't find any writings that matched your query. Please try again.", "radcliffe"); ?></p>
	
	<?php endif; ?>    

	</div> <!-- /post -->

</div> <!-- /content -->
		
<?php get_footer(); ?>