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
					
				</div> <!-- /featured-media -->
					
			<?php endif; ?>
											
			<div class="post-header section">
		
				<div class="post-header-inner section-inner">
																									
					<h1 class="post-title"><?php the_title(); ?></h1>
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
			    
		    <div class="post-content section-inner thin">
		    
		    	<?php the_content(); ?>
		    	
				<div class="clear"></div>
				
				
		    	<?php wp_link_pages('before=<p class="page-links">' . __('Pages:','radcliffe') . ' &after=</p>&seperator= <span class="sep">/</span> '); ?>
		    
		    </div>

		</div> <!-- /post -->
		
		<?php comments_template( '', true ); ?>
		
	<?php endwhile; else: ?>
	
		<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "radcliffe"); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
	
</div> <!-- /content -->
								
<?php get_footer(); ?>