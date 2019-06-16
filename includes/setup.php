<?php

# -----------------------------------------------------------------
# Theme activation: Wonder Theme ACTIVATE
# -----------------------------------------------------------------

// run when this theme is activated
add_action('after_switch_theme', 'truwriter_setup');

function truwriter_setup () { 

    // make sure our categories are present, accounted for, named
	wp_insert_term( 'In Progress', 'category' );
	wp_insert_term( 'Published', 'category' );

	// Look for existence of pages with the appropriate template, if not found
	// make 'em cause it's good to make the pages
	
	if (! page_with_template_exists( 'page-write.php' ) ) {
  
		// create the writing form page if it does not exist
		// backdate creation date 2 days just to make sure they do not end up future dated
		// which causes all kinds of disturbances in the force
		
		$page_data = array(
			'post_title' 	=> 'Write? Write. Right.',
			'post_content'	=> 'Here is the place to compose, preview, and hone your fine words. If you are building this site, maybe edit this page to customize this wee bit of text.',
			'post_name'		=> 'write',
			'post_status'	=> 'publish',
			'post_type'		=> 'page',
			'post_author' 	=> 1,
			'post_date' 	=> date('Y-m-d H:i:s', time() - 172800),
			'page_template'	=> 'page-write.php',
		);
	
		wp_insert_post( $page_data );
	}

	if (! page_with_template_exists( 'page-desk.php' ) ) {
  
		// create the welcome entrance to the tool if it does not exist
		// backdate creation date 2 days just to make sure they do not end up future dated
		
		$page_data = array(
			'post_title' 	=> 'Welcome Desk',
			'post_content'	=> 'You are but one special key word away from being able to write. Hopefully the kind owner of this site has provided you the key phrase. Spelling and capitalization do count. If you are said owner, editing this page will let you personalize this bit. ',
			'post_name'		=> 'desk',
			'post_status'	=> 'publish',
			'post_type'		=> 'page',
			'post_author' 	=> 1,
			'post_date' 	=> date('Y-m-d H:i:s', time() - 172800),
			'page_template'	=> 'page-desk.php',
		);
	
		wp_insert_post( $page_data );
	}
	
	flush_rewrite_rules( );
}

# -----------------------------------------------------------------
# Set up the table and put the napkins out, stuff we do every visit
# -----------------------------------------------------------------

// get theme options early in the flow
add_action( 'after_setup_theme', 'truwriter_load_theme_options', 9 );

// change the name of admin menu items from "New Posts"
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
// and of course the Codex http://codex.wordpress.org/Function_Reference/add_submenu_page


// Here we further move the Wordpress interface from it's Post centric personality, to rename the labels for posts
add_action( 'init', 'truwriter_change_post_object' );

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


/* set up rewrite rules */
add_action('init','truwriter_rewrite_rules');


function truwriter_rewrite_rules() {
	// for sending to random item
   add_rewrite_rule('random/?$', 'index.php?random=1', 'top');

   // for edit link requests
   add_rewrite_rule( '^get-edit-link/([^/]+)/?',  'index.php?elink=1&wid=$matches[1]','top');	 
 }
 
 
 /* handle redirects */
 
add_action( 'template_redirect', 'truwriter_write_director' );

function truwriter_write_director() {

	if ( is_page('write') and !isset( $_POST['truwriter_form_make_submitted'] ) ) {
	
		// check for query vars that indicate this is a edit request/ build qstring
		$wid = get_query_var( 'wid' , 0 );   // id of post
		$tk  = get_query_var( 'tk', 0 );    // magic token to check

		$args = ( $wid and $tk )  ? '?wid=' . $wid . '&tk=' . $tk : '';
		
			// normal entry check for author
		if ( !is_user_logged_in() ) {
			// not already logged in? go to desk.
			wp_redirect ( home_url('/') . 'desk'  . $args );
			exit;
	
		} elseif ( !current_user_can( 'edit_others_posts' ) ) {
			// okay user, who are you? we know you are not an admin or editor
		
			// if the writer user not found, we send you to the desk
			if ( !truwriter_check_user() ) {
				// now go to the desk and check in properly
				wp_redirect ( home_url('/') . 'desk' . $args  );
				exit;
			} 
		}

	}
	
	if ( is_page('desk') ) {
	
	
		// check for query vars that indicate this is a edit request/ build qstring
		$wid = get_query_var( 'wid' , 0 );   // id of post
		$tk  = get_query_var( 'tk', 0 );    // magic token to check

		$args = ( $wid and $tk )  ? '?wid=' . $wid . '&tk=' . $tk : '';

	
		// already logged in? go directly to the tool
		if ( is_user_logged_in() ) {

			if ( current_user_can( 'edit_others_posts' ) ) {
				// If user has edit/admin role, send them to the tool
				wp_redirect( splot_redirect_url() . $args );
				exit;

			} else {

				// if the correct user already logged in, go directly to the tool
				if ( truwriter_check_user() ) {			
					wp_redirect( splot_redirect_url()  . $args );
					exit;
				} 
			}	
	
		} elseif ( truwriter_option('accesscode') == '')  {
			splot_user_login('writer', true, $args );
			exit;
		} elseif ( isset( $_POST['truwriter_form_access_submitted'] ) 
		&& wp_verify_nonce( $_POST['truwriter_form_access_submitted'], 'truwriter_form_access' ) ) {
 
			// access code from the form
			if ( stripslashes( $_POST['wAccess'] ) == truwriter_option('accesscode') ) {
				splot_user_login('writer', true, $args );
				exit;
			}
			
		}
			
	}
	
  if ( get_query_var('random') == 1 ) {
		 // set arguments for WP_Query on published posts to get 1 at random
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'orderby' => 'rand'
		);

		// It's time! Go someplace random
		$my_random_post = new WP_Query ( $args );

		while ( $my_random_post->have_posts () ) {
		  $my_random_post->the_post ();
  
		  // redirect to the random post
		  wp_redirect ( get_permalink () );
		  exit;
		}  
   } elseif ( get_query_var('elink') == 1 and get_query_var('wid')  ) {
   
   		// get the id parameter from URL
		$wid = get_query_var( 'wid' , 0 );   // id of post

		truwriter_mail_edit_link ($wid);
   		exit;
   }


}


// filter content on writing page so we do not submit the page content if form is submitted
add_filter( 'the_content', 'truwriter_firstview' );
 
function truwriter_firstview( $content ) {
 
    // Check if we're inside the main loop on the writing page
    if ( is_page('write') && in_the_loop() && is_main_query() ) {
    
    	if ( isset( $_POST['truwriter_form_make_submitted'] ) ) {
    		return '';
    	} else {
    		 return $content;
    	}
       
    }
 
    return $content;
}


// Some vain attempts to manage the twitter auto logout time, make them much longer for admins
// and much shorther for authors (this does not seem to work, sigh. Help me Obi Wordpress Kenobi)

add_filter( 'auth_cookie_expiration', 'truwriter_cookie_expiration', 99, 3 );
function truwriter_cookie_expiration( $expiration, $user_id, $remember ) {

	if ( current_user_can( 'edit_pages' )  ) {
		// bump up default 14 day logout function 
    	return $remember ? $expiration : 1209600; 
    } else {
    	// shorter auto logout for guests (2 hours)
      	return $remember ? $expiration : 7200; 
    }
}

// Customize the headings for the comment form
add_filter('comment_form_defaults', 'truwriter_comment_mod');

function truwriter_comment_mod( $defaults ) {
	$defaults['title_reply'] = 'Provide Feedback';
	$defaults['logged_in_as'] = '';
	$defaults['title_reply_to'] = 'Provide Feedback for %s';
	return $defaults;
}


// remove  buttons from the visual editor
add_filter('mce_buttons','truwriter_tinymce_buttons');

function truwriter_tinymce_buttons($buttons)
 {
	//Remove the more button
	$remove = 'wp_more';

	//Find the array key and then unset
	if ( ( $key = array_search($remove,$buttons) ) !== false )
		unset($buttons[$key]);

	return $buttons;
 }

// remove  more buttons from the visual editor

add_filter('mce_buttons_2','truwriter_tinymce_2_buttons');

function truwriter_tinymce_2_buttons($buttons)
 {
	//Remove the keybord shortcut and paste text buttons
	$remove = array('wp_help','pastetext');

	return array_diff($buttons,$remove);
 }



// -----  add allowable url parameters so we can do reall cool stuff, wally
add_filter('query_vars', 'truwriter_queryvars' );

function truwriter_queryvars( $qvars ) {
	$qvars[] = 'tk'; // token key for editing previously made stuff
	$qvars[] = 'wid'; // post id for editing
	$qvars[] = 'random'; // random flag
	$qvars[] = 'elink'; // edit link flag
	
	return $qvars;
} 

add_action( 'admin_menu', 'truwriter_change_post_label' );

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

// Add some menu items to the admin menu to porvide easy access to the In Progress category items
// and the pending status ones
add_action('admin_menu', 'truwriter_drafts_menu');

function truwriter_drafts_menu() {
	add_submenu_page('edit.php', 'Writings in Progress (not submitted)', 'In Progress', 'edit_pages', 'edit.php?post_status=draft&post_type=post&cat=' . get_cat_ID( 'In Progress' ) ); 
	
	add_submenu_page('edit.php', 'Writings Submitted for Approval', 'Pending Approval', 'edit_pages', 'edit.php?post_status=pending&post_type=post' ); 
}



# -----------------------------------------------------------------
# For the Writing Form
# -----------------------------------------------------------------

add_action('wp_enqueue_scripts', 'add_truwriter_scripts');

function add_truwriter_scripts() {	

	// set up main styles
	$parent_style = 'radcliffe_style'; 

	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

	wp_enqueue_style( 'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get('Version')
	);
	

 	if ( is_page('write') ) { // use on just our form page
    
		 // add media scripts if we are on our maker page and not an admin
		 // after http://wordpress.stackexchange.com/a/116489/14945
    	 
		if (! is_admin() ) wp_enqueue_media();
		
		// Build in tag auto complete script
   		wp_enqueue_script( 'suggest' );
   		
   		// Autoembed functionality in rich text editor
   		// needs dependency on tiny_mce
   		// h/t https://wordpress.stackexchange.com/a/287623
   		
   		wp_enqueue_script( 'mce-view', '', array('tiny_mce') );		
   		
   		
		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.writer' , get_stylesheet_directory_uri() . '/js/jquery.writer.js', 'suggest' , '1.23', TRUE );
		wp_enqueue_script( 'jquery.writer' );
		
		// add scripts for fancybox (used for help) 
		//-- h/t http://code.tutsplus.com/tutorials/add-a-responsive-lightbox-to-your-wordpress-theme--wp-28100
		wp_enqueue_script( 'fancybox', get_stylesheet_directory_uri() . '/includes/lightbox/js/jquery.fancybox.pack.js', array( 'jquery' ), false, true );
    	wp_enqueue_script( 'lightbox', get_stylesheet_directory_uri() . '/includes/lightbox/js/lightbox.js', array( 'fancybox' ), '1.1',
    null , '1.0', TRUE );
    
    	wp_enqueue_style( 'lightbox-style', get_stylesheet_directory_uri() . '/includes/lightbox/css/jquery.fancybox.css' );

	} elseif ( is_single() ) {
		// single writings, give is the jQuery for edit link stuff
		
		wp_register_script( 'jquery.editlink' , get_stylesheet_directory_uri() . '/js/jquery.editlink.js', null , '0.1', TRUE );
		wp_enqueue_script( 'jquery.editlink' );
	}
}

// set the default upload image size to "large' cause medium is puny
// ----- h/t http://stackoverflow.com/a/20019915/2418186

add_filter( 'pre_option_image_default_size', 'my_default_image_size' );

function my_default_image_size () {
    return 'large'; 
}

# -----------------------------------------------------------------
# Menu Setup
# -----------------------------------------------------------------

// checks to see if a menu location is used.
function splot_is_menu_location_used( $location = 'primary' ) {	

	// get locations of all menus
	$menulocations = get_nav_menu_locations();
	
	// get all nav menus
	$navmenus = wp_get_nav_menus();
	
	
	// if either is empty we have no menus to use
	if ( empty( $menulocations ) OR empty( $navmenus ) ) return false;
	
	// othewise look for the menu location in the list
	return in_array( $location , $menulocations);
}



// create a basic menu if one has not been define for primary
function splot_default_menu() {

	// site home with trailing slash
	$splot_home = home_url('/');
  
 	return ( '<li><a href="' . $splot_home . '">Home</a></li><li><a href="' . $splot_home . 'write' . '">Write</a></li><li><a href="' . $splot_home . 'random' . '">Random</a></li>' );
}
?>