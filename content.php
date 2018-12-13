<?php

$style_attr = '';

if ( has_post_thumbnail() ) {
	$style_attr = 'style="background-image: url( ' . get_the_post_thumbnail_url( $post->ID, 'post-image' ) . ' );"';
}

?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?><?php echo $style_attr; ?>>	


	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="post-header section medium-padding">
		
		<div class="post-meta-top">
		
			<?php 
			
			the_time( get_option( 'date_format' ) );

		
		
			if ( truwriter_option('allow_comments') and comments_open() ) {
				echo '<span class="sep">/</span> '; 
				if ( is_single() ) {
					comments_popup_link( __( '0 comments', 'radcliffe' ), __( '1 comment', 'radcliffe' ), __( '% comments', 'radcliffe' ), 'post-comments' ); 
				} else {
					comments_number( __( '0 comments', 'radcliffe' ), __( '1 comment', 'radcliffe' ), __( '% comments', 'radcliffe' ) ); 
				}
			}
			
			echo truwriter_get_reading_time('<span class="sep">/</span> Reading Time:', '');
			
			if ( is_sticky() ) { echo '<span class="sep">/</span> '; _e('Sticky','radcliffe'); } 
			 
			 ?>
		
		</div>
	
    <h2 class="post-title"><?php the_title(); ?></h2>
    <p class="theauthor"><?php $wAuthor=  get_post_meta( $post->ID, 'wAuthor', 1 ); echo $wAuthor;?></p>
    	    
	</a><!-- .post-header -->

</div><!-- .post -->