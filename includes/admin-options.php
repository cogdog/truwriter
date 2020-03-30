<?php
# -----------------------------------------------------------------
# Options Panel for Admin
# -----------------------------------------------------------------

// -----  Add admin menu link for Theme Options
add_action( 'wp_before_admin_bar_render', 'truwriter_options_to_admin' );

// put the options on the menu and top stage, for admins only
function truwriter_options_to_admin() {

	if ( current_user_can( 'manage_options' ) ) {
		global $wp_admin_bar;

		// we can add a submenu item too
		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'truwriter-options',
			'title' => __('TRU Writer Options'),
			'href' => admin_url( 'themes.php?page=truwriter-options')
		) );

		// add a customizer link that opens the sharing form
		$wp_admin_bar->add_menu( array(
			'parent' => 'customize',
			'id' => 'truwriter-customize',
			'title' => __('Writing Form'),
			'href' => admin_url( 'customize.php?url='. splot_redirect_url())
		) );

	}
}

// Set up javascript for the theme options interface
function truwriter_enqueue_options_scripts() {

	// media scripts needed for wordpress media uploaders
	wp_enqueue_media();

	// custom jquery for the options admin screen
	wp_register_script( 'truwriter_options_js' , get_stylesheet_directory_uri() . '/js/jquery.truwriter-options.js', null , '1.0', TRUE );
	wp_enqueue_script( 'truwriter_options_js' );
}

// load theme options Settings
function truwriter_load_theme_options() {

	if ( file_exists( get_stylesheet_directory()  . '/class.truwriter-theme-options.php' ) ) {
		include_once( get_stylesheet_directory()  . '/class.truwriter-theme-options.php' );
	}

	// set up child theme localization
	load_child_theme_textdomain( 'radcliffe', get_stylesheet_directory() . '/languages' );
}
?>
