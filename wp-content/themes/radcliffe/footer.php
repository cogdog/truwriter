<?php if ( is_active_sidebar( 'footer-a' ) || is_active_sidebar( 'footer-b') || is_active_sidebar( 'footer-c' ) ) : ?>

	<div class="footer section medium-padding bg-graphite">
			
		<div class="section-inner row">
		
			<?php if ( is_active_sidebar( 'footer-a' ) ) : ?>
			
				<div class="column column-1 one-third">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-a' ); ?>
											
					</div>
					
				</div>
				
			<?php endif; ?> <!-- /footer-a -->
				
			<?php if ( is_active_sidebar( 'footer-b' ) ) : ?>
			
				<div class="column column-2 one-third">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-b' ); ?>
											
					</div> <!-- /widgets -->
					
				</div>
				
			<?php endif; ?> <!-- /footer-b -->
								
			<?php if ( is_active_sidebar( 'footer-c' ) ) : ?>
			
				<div class="column column-3 one-third">
			
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-c' ); ?>
											
					</div> <!-- /widgets -->
					
				</div>
				
			<?php endif; ?> <!-- /footer-c -->
			
			<div class="clear"></div>
		
		</div> <!-- /footer-inner -->
	
	</div> <!-- /footer -->

<?php endif; ?>

<div class="credits section light-padding">

	<div class="credits-inner section-inner">
	
		<p class="fleft">
		
			&copy; <?php echo date("Y") ?> <a href="<?php echo home_url(); ?>" title="<?php esc_attr( bloginfo('name') ); ?>"><?php bloginfo('name'); ?></a>
		
		</p>
		
		<p class="fright">
			
			<span><?php _e( 'Theme by', 'radcliffe' ); ?> <a href="http://www.andersnoren.se">Anders Nor&eacute;n</a> &mdash; </span><a title="<?php _e('To the top', 'radcliffe'); ?>" href="#" class="tothetop"><?php _e('Up', 'radcliffe' ); ?> &uarr;</a>
			
		</p>
		
		<div class="clear"></div>
		
	</div> <!-- /credits-inner -->

</div> <!-- /credits -->

<?php wp_footer(); ?>

</body>
</html>