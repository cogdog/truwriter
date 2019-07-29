<?php
# -----------------------------------------------------------------
# login stuff
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the "back to blog" and retrieve password links

// You know like my logo? Whatsamatta you? Then change the image in the theme folder images/site-login-logo.png
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            padding-bottom: 30px;
        }    
	#backtoblog {display:none;}
	#nav {display:none;}
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'login_link' );

function login_link( $url ) {
	return get_bloginfo( 'url' );
}

function splot_user_login( $user_login = 'writer', $redirect = true, $query_str = '' ) {
	/* login the special user account to allow authoring
	   Somestimes we want to do it without redirection
	   other times we have to pass a query string
	*/
	
	// check for the correct user
	$autologin_user = get_user_by( 'login', $user_login ); 
	
	// is this user logged in?
	if ( $autologin_user ) {
	
		// just in case we have old cookies
		wp_clear_auth_cookie(); 
		
		// set the user directly
		wp_set_current_user( $autologin_user->ID, $autologin_user->user_login );
		
		// new cookie
		wp_set_auth_cookie( $autologin_user->ID);
		
		// do the login
		do_action( 'wp_login', $autologin_user->user_login );
		
		// send 'em on their way
		if ($redirect) wp_redirect( splot_redirect_url() . $query_str  );
		
		
	} else {
		// uh on, problem
		die ('Required account missing. Looks like there is not an account set up for "' . $user_login . '". See the theme options to set up.');
	
	}
}

function splot_redirect_url() {
	// where to send visitors after login ok
	return ( home_url('/') . truwriter_get_write_page() );
}

function splot_is_admin() {
	// test current author as above basic level of splot user
	return ( current_user_can( 'edit_others_posts' )  );
}


// remove admin tool bar for non-admins, remove access to dashboard
// -- h/t http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/

add_action('after_setup_theme', 'splot_remove_admin_bar');

function splot_remove_admin_bar() {
	if ( !splot_is_admin()  ) show_admin_bar(false);
}
?>