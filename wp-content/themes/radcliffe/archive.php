<?php get_header(); ?>

<div class="content">
			
		<div class="posts">
		
			<div class="page-title section light-padding">
			
				<div class="section-inner">
	
					<h4><?php if ( is_day() ) : ?>
						<?php printf( __( 'Date: %s', 'radcliffe' ), '' . get_the_date() . '' ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Month: %s', 'radcliffe' ), '' . get_the_date( _x( 'F Y', 'F = Month, Y = Year', 'radcliffe' ) ) ); ?>
					<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Year: %s', 'radcliffe' ), '' . get_the_date( _x( 'Y', 'Y = Year', 'radcliffe' ) ) ); ?>
					<?php elseif ( is_category() ) : ?>
						<?php printf( __( 'Category: %s', 'radcliffe' ), '' . single_cat_title( '', false ) . '' ); ?>
					<?php elseif ( is_tag() ) : ?>
						<?php printf( __( 'Tag: %s', 'radcliffe' ), '' . single_tag_title( '', false ) . '' ); ?>
					<?php elseif ( is_author() ) : ?>
						<?php $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>
						<?php printf( __( 'Author: %s', 'radcliffe' ), $curauth->display_name ); ?>
					<?php else : ?>
						<?php _e( 'Archive', 'radcliffe' ); ?>
					<?php endif; ?>
					
					<?php
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					
					if ( "1" < $wp_query->max_num_pages ) : ?>
					
						<span><?php printf( __('(page %s of %s)', 'radcliffe'), $paged, $wp_query->max_num_pages ); ?></span>
					
					<?php endif; ?></h4>
					
					<?php
						$tag_description = tag_description();
						if ( ! empty( $tag_description ) )
							echo apply_filters( 'tag_archive_meta', '<div class="tag-archive-meta">' . $tag_description . '</div>' );
					?>
				
				</div> <!-- /section-inner -->
				
			</div> <!-- /page-title -->
			
			<div class="clear"></div>
	
			<?php if ( have_posts() ) : ?>
		
				<?php rewind_posts(); ?>
			
				<?php while ( have_posts() ) : the_post(); ?>
				
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
						<?php get_template_part( 'content', get_post_format() ); ?>
												
					</div> <!-- /post -->
					
				<?php endwhile; ?>
							
		</div> <!-- /posts -->
					
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			
			<div class="archive-nav">
			
				<?php echo get_next_posts_link( '&laquo; ' . __('Older posts', 'radcliffe')); ?>
						
				<?php echo get_previous_posts_link( __('Newer posts', 'radcliffe') . ' &raquo;'); ?>
				
				<div class="clear"></div>
				
			</div> <!-- /post-nav archive-nav -->
							
		<?php endif; ?>
				
	<?php endif; ?>

</div> <!-- /content -->

<?php get_footer(); ?>