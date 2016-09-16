<?php
/*
Template Name: Archive Template
*/
?>

<?php get_header(); ?>

<div class="content">						

	<div <?php post_class('post single'); ?>>
	
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
			<div class="post-header section">
	
				<div class="post-header-inner section-inner">
																									
					<h1 class="post-title"><?php the_title(); ?></h1>
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
		   				        			        		                
			<div class="post-content section-inner thin">
										                                        
				<?php the_content(); ?>
				
				<div class="clear"></div>
									
			</div>
									
			<div class="archive-container section-inner thin">
									            
	            <ul>
		            <?php $posts_archive = get_posts('numberposts=-1');
		            foreach($posts_archive as $post) : ?>
		                <li>
		                	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		                		<?php the_title();?> 
		                		<span><?php the_time(get_option('date_format')); ?></span>
		                	</a>
		                </li>
		            <?php endforeach; ?>
	            </ul>
	            
	            <?php wp_reset_query(); ?>
        
            </div> <!-- /archive-container -->
		            							
			<?php comments_template( '', true ); ?>
		
		<?php endwhile; else: ?>

			<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "radcliffe"); ?></p>
	
		<?php endif; ?>

	</div> <!-- /post -->

	<div class="clear"></div>
	
</div> <!-- /content -->
								
<?php get_footer(); ?>