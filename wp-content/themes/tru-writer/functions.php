<?php

# -----------------------------------------------------------------
# Theme activation
# -----------------------------------------------------------------

// run when this theme is activated
add_action('after_switch_theme', 'truwriter_setup');

function truwriter_setup () {
  // make sure our categories are present
  
  wp_insert_term( 'In Progress', 'category' );
  wp_insert_term( 'Published', 'category' );

  // create pages if they do not exist
  
  if (! get_page_by_path( 'write' ) ) {
  
  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_content'	=> 'Use the tools below to compose, repview, and publish your writing.',
  		'post_name'		=> 'write',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s'),
  		'page_template'	=> 'page-write.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'desk' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_content'	=> 'Welcome to the place to add your writing to this collection.',
  		'post_name'		=> 'desk',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s'),
  		'page_template'	=> 'page-desk.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'random' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_content'	=> '(Place holder for random page)',
  		'post_name'		=> 'random',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s'),
  		'page_template'	=> 'page-random.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }
   
}


# -----------------------------------------------------------------
# Set up the table and put the napkins out
# -----------------------------------------------------------------


add_action( 'init', 'truwriter_load_theme_options' );

// change the name of admin menu items from "New Posts"
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
// and of course the Codex http://codex.wordpress.org/Function_Reference/add_submenu_page

add_action( 'admin_menu', 'truwriter_change_post_label' );
add_action( 'init', 'truwriter_change_post_object' );
add_action('admin_menu', 'truwriter_drafts_menu');


function truwriter_change_post_label() {
    global $menu;
    global $submenu;
    
    $thing_name = 'Writing';
    
    $menu[5][0] = $thing_name . 's';
    $submenu['edit.php'][5][0] = 'All ' . $thing_name . 's';
    $submenu['edit.php'][10][0] = 'Add ' . $thing_name;
    $submenu['edit.php'][15][0] = $thing_name .' Categories';
    $submenu['edit.php'][16][0] = $thing_name .' Tags';
    echo '';
}
function truwriter_change_post_object() {

    $thing_name = 'Writing';

    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name =  $thing_name . 's';;
    $labels->singular_name =  $thing_name;
    $labels->add_new = 'Add ' . $thing_name;
    $labels->add_new_item = 'Add ' . $thing_name;
    $labels->edit_item = 'Edit ' . $thing_name;
    $labels->new_item =  $thing_name;
    $labels->view_item = 'View ' . $thing_name;
    $labels->search_items = 'Search ' . $thing_name;
    $labels->not_found = 'No ' . $thing_name . ' found';
    $labels->not_found_in_trash = 'No ' .  $thing_name . ' found in Trash';
    $labels->all_items = 'All ' . $thing_name;
    $labels->menu_name =  $thing_name;
    $labels->name_admin_bar =  $thing_name;
}
 

function truwriter_drafts_menu() {
	add_submenu_page('edit.php', 'Writings in Progress (not submitted)', 'In Progress', 'manage_options', 'edit.php?post_status=draft&post_type=post&cat=' . get_cat_ID( 'In Progress' ) ); 
	
	add_submenu_page('edit.php', 'Writings Submitted for Approval', 'Pending Approval', 'manage_options', 'edit.php?post_status=pending&post_type=post' ); 
}

function truwriter_cookie_expiration( $expiration, $user_id, $remember ) {

	if ( current_user_can( 'edit_pages' )  ) {
		// default 14 day logout function 
    	return $remember ? $expiration : 1209600; 
    } else {
    	// shorter auto logout for guests (2 hours)
      	return $remember ? $expiration : 7200; 
    }
}

add_filter( 'auth_cookie_expiration', 'truwriter_cookie_expiration', 99, 3 );


add_filter('comment_form_defaults', 'truwriter_comment_mod');

function truwriter_comment_mod( $defaults ) {
	$defaults['title_reply'] = 'Provide Feedback';
	$defaults['logged_in_as'] = '';
	$defaults['title_reply_to'] = 'Provide Feedback for %s';
	return $defaults;
}



function truwriter_tinymce_buttons($buttons)
 {
	//Remove the more button
	$remove = 'wp_more';

	//Find the array key and then unset
	if ( ( $key = array_search($remove,$buttons) ) !== false )
		unset($buttons[$key]);

	return $buttons;
 }
add_filter('mce_buttons','truwriter_tinymce_buttons');



function truwriter_tinymce_2_buttons($buttons)
 {
	//Remove the keybord shortcut and paste text buttons
	$remove = array('wp_help','pastetext');

	return array_diff($buttons,$remove);
 }
add_filter('mce_buttons_2','truwriter_tinymce_2_buttons');

# -----------------------------------------------------------------
# Options Panel for Admin
# -----------------------------------------------------------------

// -----  Add admin menu link for Theme Options
add_action( 'wp_before_admin_bar_render', 'truwriter_options_to_admin' );

function truwriter_options_to_admin() {
    global $wp_admin_bar;
    
    // we can add a submenu item too
    $wp_admin_bar->add_menu( array(
        'parent' => '',
        'id' => 'truwriter-options',
        'title' => __('TRU Writer Options'),
        'href' => admin_url( 'themes.php?page=truwriter-options')
    ) );
}


function truwriter_enqueue_options_scripts() {
	// Set up javascript for the theme options interface
	
	// media scripts needed for wordpress media uploaders
	wp_enqueue_media();
	
	// custom jquery for the options admin screen
	wp_register_script( 'truwriter_options_js' , get_stylesheet_directory_uri() . '/js/jquery.truwriter-options.js', null , '1.0', TRUE );
	wp_enqueue_script( 'truwriter_options_js' );
}

function truwriter_load_theme_options() {
	// load theme options Settings

	if ( file_exists( get_stylesheet_directory()  . '/class.truwriter-theme-options.php' ) ) {
		include_once( get_stylesheet_directory()  . '/class.truwriter-theme-options.php' );
	}
}


# -----------------------------------------------------------------
# login stuff
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
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
 
 
// Auto Login
// create a link that can automatically log in as a specific user, bypass login screen
// -- h/t  http://www.wpexplorer.com/automatic-wordpress-login-php/

add_action( 'after_setup_theme', 'truwriter_autologin' );

function truwriter_autologin() {
	// URL Paramter to check for to trigger login
	if ($_GET['autologin'] == 'writer') {
		
		// ACCOUNT USERNAME TO LOGIN TO
		$creds['user_login'] = 'writer';
		
		// ACCOUNT PASSWORD TO USE- lame hard coded....
		$creds['user_password'] = 'Ry2fraD5wY7';
		
		$creds['remember'] = true;
		$autologin_user = wp_signon( $creds, false );
		
		if ( !is_wp_error($autologin_user) ) 
			wp_redirect ( site_url() . '/write' );
	}
}

// remove admin tool bar for non-admins, remove access to dashboard
// -- h/t http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if ( !current_user_can('edit_others_posts')  ) {
	  show_admin_bar(false);
	}

}

# -----------------------------------------------------------------
# For the Writing Form
# -----------------------------------------------------------------

add_action('wp_enqueue_scripts', 'add_truwriter_scripts');

function add_truwriter_scripts() {	 
 
 	if ( is_page('write') ) { // use on just our form page
    
		 // add media scripts if we are on our maker page and not an admin
		 // after http://wordpress.stackexchange.com/a/116489/14945
    	 
		if (! is_admin() ) wp_enqueue_media();
		
		// Build in tag auto complete script
   		wp_enqueue_script( 'suggest' );

		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.writer' , get_stylesheet_directory_uri() . '/js/jquery.writer.js', null , '1.0', TRUE );
		wp_enqueue_script( 'jquery.writer' );
		
		// add scripts for fancybox (used for help) 
		//-- h/t http://code.tutsplus.com/tutorials/add-a-responsive-lightbox-to-your-wordpress-theme--wp-28100
		wp_enqueue_script( 'fancybox', get_stylesheet_directory_uri() . '/includes/lightbox/js/jquery.fancybox.pack.js', array( 'jquery' ), false, true );
    	wp_enqueue_script( 'lightbox', get_stylesheet_directory_uri() . '/includes/lightbox/js/lightbox.js', array( 'fancybox' ), '1.1',
    null , '1.0', TRUE );
    
    	wp_enqueue_style( 'lightbox-style', get_stylesheet_directory_uri() . '/includes/lightbox/css/jquery.fancybox.css' );

	}

}

# -----------------------------------------------------------------
# Useful spanners and wrenches
# -----------------------------------------------------------------


// function to get the caption for an attachment (stored as post_excerpt)
// -- h/t http://wordpress.stackexchange.com/a/73894/14945
function get_attachment_caption_by_id( $post_id ) {
    $the_attachment = get_post( $post_id );
    return ( $the_attachment->post_excerpt ); 
}

function reading_time_check() {
// checks for installation of Estimated Reading Time plugin

	if ( shortcode_exists( 'est_time' ) ) {
		// yep, golden
		return ('The Estimated Post Reading Time plugin is installed. No further action necessary.');
	} else {
		// nope, send them off to set it up
		return ('The <a href="http://wordpress.org/extend/plugins/estimated-post-reading-time/" target="_blank">Estimated Post Reading Time plugin</a> is NOT installed.  <a href="' . admin_url( 'plugins.php') . '">Do it now!</a>');
	}
}

function truwriter_author_user_check() {
// checks for an authoring account set up

	$auser = get_user_by( 'login', 'writer' );
	
	if ( !$auser) {
		return ('Authoring account not set up. You need to <a href="' . admin_url( 'user-new.php') . '">create a user account</a> with login name <strong>writer</strong> with a role of <strong>Author</strong>. Make a killer strong password; no one uses it.');
	} elseif ( $auser->roles[0] != 'author') {
		return ('The user account <strong>writer</strong> is set up but needs to have it\'s role set to <strong>Author</strong>. You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
	} else {
		return ('The authoring account <strong>writer</strong> is correctly set up.');
	}
}


function set_html_content_type() {
	// from http://codex.wordpress.org/Function_Reference/wp_mail
	return 'text/html';
}

function br2nl ( $string )
// from http://php.net/manual/en/function.nl2br.php#115182
{
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

?>