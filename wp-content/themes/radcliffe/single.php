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
					
					<?php if ( !empty(get_post(get_post_thumbnail_id())->post_excerpt) ) : ?>
											
						<div class="media-caption-container">
						
							<p class="media-caption"><?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?></p>
							
						</div>
						
					<?php endif; ?>
					
				</div> <!-- /featured-media -->
					
			<?php endif; ?>
				
			<div class="post-header section">
		
				<div class="post-header-inner section-inner medium">
													
					<p class="post-meta-top"><a href="<?php the_permalink(); ?>" title="<?php the_time('h:i'); ?>"><?php the_time(get_option('date_format')); ?></a> <?php if ( comments_open() ) { echo '<span class="sep">/</span> '; comments_popup_link( '0 comments', '1 comment', '% comments', 'post-comments' ); } ?> <?php edit_post_link( 'Edit', '<span class="sep">/</span> ' ); ?></p>
											
					<h1 class="post-title"><?php the_title(); ?></h1>
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
			    
		    <div class="post-content section-inner thin">
		    
		    	<?php the_content(); ?>

		    	<div class="clear"></div>
		    	
		    	<?php wp_link_pages('before=<p class="page-links">' . __('Pages:','radcliffe') . ' &after=</p>&separator=<span class="sep">/</span>'); ?>
		    
		    </div>
		    
			<div class="post-meta section-inner thin">
			
				<div class="meta-block post-author">
				
					<h3 class="meta-title"><?php _e('About the author','radcliffe'); ?></h3>
					
					<div class="post-author-container">
				
						<?php echo get_avatar( get_the_author_meta('email'), '160' ); ?>
						
						<div class="post-author-inner">
					
							<h3><?php the_author_posts_link(); ?></h3>
							
							<p class="author-description"><?php the_author_meta('description'); ?></p>
							
							<div class="author-links">
								
								<a class="author-link-posts" title="<?php _e('Author archive','radcliffe'); ?>" href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php _e('Author archive','radcliffe'); ?></a>
								
								<?php $author_url = get_the_author_meta('user_url'); 
							
								$author_url = preg_replace('#^https?://#', '', rtrim($author_url,'/'));
																
								if (!empty($author_url)) : ?>
								
									<a class="author-link-website" title="<?php _e('Author website','radcliffe'); ?>" href="<?php the_author_meta('user_url'); ?>"><?php _e('Author website','radcliffe'); ?></a>
									
								<?php endif; ?>
								
							</div> <!-- /author-links -->
						
						</div>
						
						<div class="clear"></div>
					
					</div>
				
				</div> <!-- /post-author -->
				
				<div class="meta-block post-cat-tags">
				
					<h3 class="meta-title"><?php _e('About the post','radcliffe'); ?></h3>
				
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
													
								<a title="<?php _e('Next post:', 'radcliffe'); echo ' ' . get_the_title($next_post); ?>" href="<?php echo get_permalink( $next_post->ID ); ?>"><?php echo get_the_title($next_post); ?></a>
							
							</p>
					
						<?php endif; ?>
						
						<?php
						$prev_post = get_previous_post();
						if (!empty( $prev_post )): ?>
						
							<p class="post-nav-prev">
					
							<a title="<?php _e('Previous post:', 'radcliffe'); echo ' ' . get_the_title($prev_post); ?>" href="<?php echo get_permalink( $prev_post->ID ); ?>"><?php echo get_the_title($prev_post); ?></a>
							
							</p>
					
						<?php endif; ?>
						
						<div class="clear"></div>
					
					</div> <!-- /post-nav -->
				
				</div> <!-- /post-cat-tags -->
				
				<div class="clear"></div>
								
			</div> <!-- /post-meta -->
													                                    	        	        
		</div> <!-- /post -->
						
		<?php comments_template( '', true ); ?>
									                        
   	<?php endwhile; else: ?>

		<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "radcliffe"); ?></p>
	
	<?php endif; ?>    

	</div> <!-- /post -->

</div> <!-- /content -->
		
<?php get_footer(); ?>