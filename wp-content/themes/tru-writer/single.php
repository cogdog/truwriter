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
													
					<p class="post-meta-top"><a href="<?php the_permalink(); ?>" title="<?php the_time('h:i'); ?>"><?php the_time(get_option('date_format')); ?></a> <?php echo '<span class="sep">/</span> Reading Time: ~'; $readtime = do_shortcode( '[est_time]' ); echo $readtime; ?> </p>
														
					<h2 class="post-title"><?php the_title(); ?></h2>
					
					<p class="theauthor">written by <?php $wAuthor=  get_post_meta( $post->ID, 'wAuthor', 1 ); echo $wAuthor;?></p>
					
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
			    
		    <div class="post-content section-inner thin">
		    
		    	<?php the_content(); ?>

		    	<div class="clear"></div>
		    	
		    	<?php wp_link_pages('before=<p class="page-links">' . __('Pages:','radcliffe') . ' &after=</p>&separator=<span class="sep">/</span>'); ?>
		    
		    </div>
		    
			<div class="post-meta section-inner thin">
			
				<div class="meta-block post-author">
				
					<h3 class="meta-title"><?php _e('SPLOT WRITTEN','radcliffe'); ?></h3>
					
					<div class="post-author-container">
				
						<?php echo get_avatar( 2, '160', 'http://splot.ca/writer/files/2014/12/splot.png' ); ?>
						
						
						<div class="post-author-inner">
						 
						 
							
							<p class="author-description"><strong>Author:</strong> <?php echo $wAuthor; ?></p>
							
							<p class="author-description"><strong>Published:</strong> <?php the_time(get_option('date_format')); ?></p>
							<p class="author-description"><strong>Word Count:</strong> <?php  echo str_word_count( get_the_content());?> </p>
							<p class="author-description"><strong>Reading time:</strong> ~<?php echo $readtime?></p>
							
							
							<div class="author-links">
								
							</div> <!-- /author-links -->
						
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
									                        
   	<?php endwhile; else: ?>

		<p><?php _e("We couldn't find any writings that matched your query. Please try again.", "radcliffe"); ?></p>
	
	<?php endif; ?>    

	</div> <!-- /post -->

</div> <!-- /content -->
		
<?php get_footer(); ?>