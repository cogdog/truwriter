<?php get_header(); ?>

<div class="content">
																	                    
	<?php if (have_posts()) : ?>
	
		<div class="posts">
	
			<?php
			$total_post_count = wp_count_posts();
			$published_post_count = $total_post_count->publish;
			$total_pages = ceil( $published_post_count / $posts_per_page );

			$paging_header = '';
			$writer_header = ( get_query_var('writer')) ? 'Written By: ' . urldecode(get_query_var('writer')) :  '';
			
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			
			if ( $paged > "1" ) {
				$paging_header = '(Page '. $paged .  ' of ' . $wp_query->max_num_pages .  ')';
			}
			?>

			<?php if ( $writer_header OR $paging_header ) : ?>
				<div class="page-title section light-padding show-writer">

					<div class="section-inner archive-header">
						<h1 class="archive-title">
							<?php echo $writer_header . ' ' . $paging_header;?>
						</h1>
					</div>
				</div>

			<?php endif?>
				
		    	<?php while (have_posts()) : the_post(); ?>
		    	
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		    	
			    		<?php get_template_part( 'content', get_post_format() ); ?>
			    				    		
		    		</div> <!-- /post -->
		    			        		            
		        <?php endwhile; ?>
	        	        		
			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			
				<div class="archive-nav">
				
					<?php echo get_next_posts_link( '&laquo; ' . __('Older Writings', 'radcliffe')); ?>
						
					<?php echo get_previous_posts_link( __('Newer Writings', 'radcliffe') . ' &raquo;'); ?>
					
					<div class="clear"></div>
					
				</div> <!-- /post-nav archive-nav -->
								
			<?php endif; ?>
        	                    
		<?php endif; ?>
		
	</div> <!-- /posts -->
		
</div> <!-- /content section-inner -->
	              	        
<?php get_footer(); ?>