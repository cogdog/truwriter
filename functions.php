<?php
/*  tru-writer theme functions
	
	Lives and forks at http://github.com/cogdog/truwriter
	
	Much of the magic happens here. Edit your own discretion, peril, unless you
	find a coding error, and by all means please fork this to the github repo 
	thus you are deemed an honorary SPLOT knight. 
	
	We suggest putting your own extra groovy code in incldes/custom-functions.php 
	
*/

# -----------------------------------------------------------------
# Theme activation: Wonder Theme ACTIVATE
# -----------------------------------------------------------------

// run when this theme is activated
add_action('after_switch_theme', 'truwriter_setup');

function truwriter_setup () { 
	// Let's get this show on the road! 


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

	
	if (! page_with_template_exists( 'page-random.php' ) ) {
  
		// create the writing form page if it does not exist
		// backdate creation date 2 days just to make sure they do not end up future dated
		
		$page_data = array(
			'post_title' 	=> 'Random',
			'post_content'	=> 'You should never see this page, it is for random redirects. What are you doing looking at this page? Get back to writing, willya?',
			'post_name'		=> 'random',
			'post_status'	=> 'publish',
			'post_type'		=> 'page',
			'post_author' 	=> 1,
			'post_date' 	=> date('Y-m-d H:i:s', time() - 172800),
			'page_template'	=> 'page-random.php',
		);
	
		wp_insert_post( $page_data );
	}

	if (! page_with_template_exists( 'page-get-edit-link.php' ) ) {
  
		// create the writing form page if it does not exist
		// backdate creation date 2 days just to make sure they do not end up future dated
		
		$page_data = array(
			'post_title' 	=> 'Get Edit Link',
			'post_content'	=> 'You should never see this page, it is for doing a few chores. What did your mom tell you about peeking?',
			'post_name'		=> 'get-edit-link',
			'post_status'	=> 'publish',
			'post_type'		=> 'page',
			'post_author' 	=> 1,
			'post_date' 	=> date('Y-m-d H:i:s', time() - 172800),
			'page_template'	=> 'page-get-edit-link.php',
		);
	
		wp_insert_post( $page_data );
	}
	

}


# -----------------------------------------------------------------
# Set up the table and put the napkins out, stuff we do every visit
# -----------------------------------------------------------------

// we need to load the options this before the auto login so we can use the pass
add_action( 'after_setup_theme', 'truwriter_load_theme_options', 9 );

// change the name of admin menu items from "New Posts"
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
// and of course the Codex http://codex.wordpress.org/Function_Reference/add_submenu_page

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


// Add some menu items to the admin menu to porvide easy access to the In Progress category items
// and the pending status ones
add_action('admin_menu', 'truwriter_drafts_menu');

function truwriter_drafts_menu() {
	add_submenu_page('edit.php', 'Writings in Progress (not submitted)', 'In Progress', 'edit_pages', 'edit.php?post_status=draft&post_type=post&cat=' . get_cat_ID( 'In Progress' ) ); 
	
	add_submenu_page('edit.php', 'Writings Submitted for Approval', 'Pending Approval', 'edit_pages', 'edit.php?post_status=pending&post_type=post' ); 
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
add_filter('query_vars', 'truwriter_tqueryvars' );

function truwriter_tqueryvars( $qvars ) {
	$qvars[] = 'tk'; // token key for editing previously made stuff
	$qvars[] = 'wid'; // post id for editing
	
	return $qvars;
}   



# -----------------------------------------------------------------
# Options Panel for Admin
# -----------------------------------------------------------------

// -----  Add admin menu link for Theme Options
add_action( 'wp_before_admin_bar_render', 'truwriter_options_to_admin' );

// put the options on the menu and top stage
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

# -----------------------------------------------------------------
# login stuff
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links

// You know like my logo? Whatsamatta you? Then chenage the image in the theme folder images/site-login-logo.png
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
 
 
// Auto Login for the Author account
// create a link that can automatically log in as a specific user, bypass login screen
// -- h/t  http://www.wpexplorer.com/automatic-wordpress-login-php/

add_action( 'after_setup_theme', 'truwriter_autologin' );

function truwriter_autologin() {
	
	if (! isset ( $_GET['autologin'] ) ) return;
	
	if ($_GET['autologin'] == 'writer') {
		
		// ACCOUNT USERNAME TO LOGIN TO
		$creds['user_login'] = 'writer';
		
		// ACCOUNT PASSWORD TO USE- stored as option
		$creds['user_password'] = truwriter_option('pkey');

		// we don't need long cookie times
		$creds['remember'] = false;
		
		$autologin_user = wp_signon( $creds );

		
		
		if ( !is_wp_error($autologin_user) ) {
				wp_redirect ( site_url() . '/write' );
		} else {
				die ('Bad news! login error: ' . $autologin_user->get_error_message() );
		}
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

function oembed_filter( $str ) {
	// filters text for URLs WP can autoembed, and returns with proper embed code
	// lifted somewhat from oembed-in-comments plugin
	global $wp_embed;

	// Automatic discovery would be a security risk, safety first
	add_filter( 'embed_oembed_discover', '__return_false', 999 );
	$str = $wp_embed->autoembed( $str );

	// ...but don't break your posts if you use it
	remove_filter( 'embed_oembed_discover', '__return_false', 999 );

	return $str;
}

// set the default upload image size to "large' cause medium is puny
// ----- h/t http://stackoverflow.com/a/20019915/2418186

add_filter( 'pre_option_image_default_size', 'my_default_image_size' );

function my_default_image_size () {
    return 'large'; 
}

# -----------------------------------------------------------------
# Customizer Stuff
# -----------------------------------------------------------------

add_action( 'customize_register', 'truwriter_register_theme_customizer' );


function truwriter_register_theme_customizer( $wp_customize ) {
	// Create custom panel.
	$wp_customize->add_panel( 'customize_writer', array(
		'priority'       => 500,
		'theme_supports' => '',
		'title'          => __( 'TRU Writer', 'radcliffe'),
		'description'    => __( 'Customizer Stuff', 'radcliffe'),
	) );

	// Add section for the collect form
	$wp_customize->add_section( 'write_form' , array(
		'title'    => __('Writing Form','radcliffe'),
		'panel'    => 'customize_writer',
		'priority' => 10
	) );
	
	// Add setting for default prompt
	$wp_customize->add_setting( 'default_prompt', array(
		 'default'           => __( 'Enter the content for your writing below. You must save first and preview once before it goes into the system as a draft. After that, continue to edit, save, and preview as much as needed. Remember to click  "Publish Final" when you are done. If you include your email address, we can send you a link that will allow you to make changes later.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Add control for default prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'default_prompt',
		    array(
		        'label'    => __( 'Default Prompt', 'radcliffe'),
		        'priority' => 10,
		        'description' => __( 'The opening message greeting above the form.' ),
		        'section'  => 'write_form',
		        'settings' => 'default_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
	
	
	// Add setting for re-edit prompt
	$wp_customize->add_setting( 're_edit_prompt', array(
		 'default'           => __( 'You can now re-edit any part of this previously published writing. If you do not save any final changes, it will be left as it was before.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Add control for re-edit prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		're_edit_prompt',
		    array(
		        'label'    => __( 'Return Edit Prompt', 'radcliffe'),
		        'priority' => 12,
		        'description' => __( 'The opening message greeting above the form for a request to edit a previously published item.' ),
		        'section'  => 'write_form',
		        'settings' => 're_edit_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
	
	
	
	
	// setting for title label
	$wp_customize->add_setting( 'item_title', array(
		 'default'           => __( 'The Title', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control fortitle label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_title',
		    array(
		        'label'    => __( 'Title Label', 'radcliffe'),
		        'priority' => 16,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_title',
		        'type'     => 'text'
		    )
	    )
	);
	
	// setting for title description
	$wp_customize->add_setting( 'item_title_prompt', array(
		 'default'           => __( 'A good title is important! Create an eye-catching title for your story, one that would make a person who sees it want to stop whatever they are doing and read it.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for title description
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_title_prompt',
		    array(
		        'label'    => __( 'Title Prompt', 'radcliffe'),
		        'priority' => 17,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_title_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for byline label
	$wp_customize->add_setting( 'item_byline', array(
		 'default'           => __( 'How to List Author', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for byline label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_byline',
		    array(
		        'label'    => __( 'Author Byline Label', 'radcliffe'),
		        'priority' => 18,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_byline',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for byline  prompt
	$wp_customize->add_setting( 'item_byline_prompt', array(
		 'default'           => __( 'Publish under your name, twitter handle, secret agent name, or remain "Anonymous". If you include a twitter handle such as @billyshakespeare, when someone tweets your work you will get a lovely notification.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for byline  prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_byline_prompt',
		    array(
		        'label'    => __( 'Author Byline Prompt', 'radcliffe'),
		        'priority' => 19,
		        'description' => __( 'Directions for the author entry field' ),
		        'section'  => 'write_form',
		        'settings' => 'item_byline_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);


	// setting for writing field  label
	$wp_customize->add_setting( 'item_writing_area', array(
		 'default'           => __( 'Writing Area', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for description  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_writing_area',
		    array(
		        'label'    => __( 'Writing Area Label', 'radcliffe'),
		        'priority' => 20,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_writing_area',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for description  label prompt
	$wp_customize->add_setting( 'item_writing_area_prompt', array(
		 'default'           => __( 'Use the editing area below the toolbar to write and format your writing. You can also paste formatted content here (e.g. from MS Word or Google Docs). The editing tool will do its best to preserve standard formatting--headings, bold, italic, lists, footnotes, and hypertext links. Click "Add Media" to upload images to include in your writing or choose from the media already in the media library (click on the tab labelled "media library"). You can also embed audio and video from many social sites simply by putting the URL of the media on a separate line (you will see a place holder in the editor, but the media will only show in preview and when published).  Click and drag the icon in the lower right to resize the editing space.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for description  label prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_writing_area_prompt',
		    array(
		        'label'    => __( 'Writing Area Prompt', 'radcliffe'),
		        'priority' => 22,
		        'description' => __( 'Directions for the main writing entry field' ),
		        'section'  => 'write_form',
		        'settings' => 'item_writing_area_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for footer  label
	$wp_customize->add_setting( 'item_footer', array(
		 'default'           => __( 'Additional Information for Footer', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for description  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_footer',
		    array(
		        'label'    => __( 'Footer Entry Label', 'radcliffe'),
		        'priority' => 24,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_footer',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for description  label prompt
	$wp_customize->add_setting( 'item_footer_prompt', array(
		 'default'           => __( 'Add any endnote / credits information you wish to append to the end of your writing, such as a citation to where it was previously published or any other meta information. URLs will be automatically hyperlinked when published.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for description  label prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_footer_prompt',
		    array(
		        'label'    => __( 'Footer Prompt', 'radcliffe'),
		        'priority' => 26,
		        'description' => __( 'Directions for the footer entry field' ),
		        'section'  => 'write_form',
		        'settings' => 'item_footer_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for header image upload label
	$wp_customize->add_setting( 'item_header_image', array(
		 'default'           => __( 'Header Image', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for header image upload  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_header_image',
		    array(
		        'label'    => __( 'Header Image Upload Label', 'radcliffe'),
		        'priority' => 30,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_header_image',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for header image upload prompt
	$wp_customize->add_setting( 'item_header_image_prompt', array(
		 'default'           => __( 'You can upload any image file to be used in the header or choose from ones that have already been added to the site. Ideally this image should be at least 1440px wide for photos. Any uploaded image should either be your own or one licensed for re-use; provide an attribution credit for the image in the caption field below.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for image upload prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_header_image_prompt',
		    array(
		        'label'    => __( 'Header Image Upload Prompt', 'radcliffe'),
		        'priority' => 32,
		        'description' => __( 'Directions for image uploads' ),
		        'section'  => 'write_form',
		        'settings' => 'item_header_image_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
	
	
	// setting for header image caption label
	$wp_customize->add_setting( 'item_header_caption', array(
		 'default'           => __( 'Caption/credits for header image', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for header image caption   label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_header_caption',
		    array(
		        'label'    => __( 'Header Image Caption Label', 'radcliffe'),
		        'priority' => 34,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_header_caption',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for header image caption   label prompt
	$wp_customize->add_setting( 'item_header_caption_prompt', array(
		 'default'           => __( 'Provide full credit / attribution for the header image.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for header image caption   label prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_header_caption_prompt',
		    array(
		        'label'    => __( 'Header Image Caption Prompt', 'radcliffe'),
		        'priority' => 36,
		        'description' => __( 'Directions for the header caption field' ),
		        'section'  => 'write_form',
		        'settings' => 'item_header_caption_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);	
	

	// setting for categories  label
	$wp_customize->add_setting( 'item_categories', array(
		 'default'           => __( 'Kind of Writing', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for categories  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_categories',
		    array(
		        'label'    => __( 'Categories Label', 'radcliffe'),
		        'priority' => 40,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_categories',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for categories  prompt
	$wp_customize->add_setting( 'item_categories_prompt', array(
		 'default'           => __( 'Check as many that apply.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for categories prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_categories_prompt',
		    array(
		        'label'    => __( 'Categories Prompt', 'radcliffe'),
		        'priority' => 42,
		        'description' => __( 'Directions for the categories selection' ),
		        'section'  => 'write_form',
		        'settings' => 'item_categories_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
		
	// setting for tags  label
	$wp_customize->add_setting( 'item_tags', array(
		 'default'           => __( 'Tags', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for tags  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_tags',
		    array(
		        'label'    => __( 'Tags Label', 'radcliffe'),
		        'priority' => 44,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_tags',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for tags  prompt
	$wp_customize->add_setting( 'item_tags_prompt', array(
		 'default'           => __( 'Add any descriptive tags for your writing. Separate multiple ones with commas.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for tags prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_tags_prompt',
		    array(
		        'label'    => __( 'Tags Prompt', 'radcliffe'),
		        'priority' => 46,
		        'description' => __( 'Directions for tags entry' ),
		        'section'  => 'write_form',
		        'settings' => 'item_tags_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);	
	
	// setting for email address  label
	$wp_customize->add_setting( 'item_email', array(
		 'default'           => __( 'Your Email Address', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for email address  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_email',
		    array(
		        'label'    => __( 'Email Address Label', 'radcliffe'),
		        'priority' => 50,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_email',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for email address  prompt
	$wp_customize->add_setting( 'item_email_prompt', array(
		 'default'           => __( 'If you provide an email address when your writing is published, you can request a special link that will allow you to edit it again in the future.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for email address prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_email_prompt',
		    array(
		        'label'    => __( 'Email Address Prompt', 'radcliffe'),
		        'priority' => 52,
		        'description' => __( 'Directions for email address entry' ),
		        'section'  => 'write_form',
		        'settings' => 'item_email_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);		
	
	// setting for editor notes  label
	$wp_customize->add_setting( 'item_editor_notes', array(
		 'default'           => __( 'Extra Information for Editors', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for editor notes  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_editor_notes',
		    array(
		        'label'    => __( 'Editor Notes Label', 'radcliffe'),
		        'priority' => 54,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_editor_notes',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for editor notes  prompt
	$wp_customize->add_setting( 'item_editor_notes_prompt', array(
		 'default'           => __( 'This information will *not* be published with your work, it is informational for the editor use only.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for editor notes prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_editor_notes_prompt',
		    array(
		        'label'    => __( 'Editor Notes Prompt', 'radcliffe'),
		        'priority' => 56,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_editor_notes_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);	
	

	// setting for license  label
	$wp_customize->add_setting( 'item_license', array(
		 'default'           => __( 'Creative Commons License', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for license  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_license',
		    array(
		        'label'    => __( 'License Label', 'radcliffe'),
		        'priority' => 27,
		        'description' => __( '' ),
		        'section'  => 'write_form',
		        'settings' => 'item_license',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for license  prompt
	$wp_customize->add_setting( 'item_license_prompt', array(
		 'default'           => __( 'Choose your preferred license.', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for license prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_license_prompt',
		    array(
		        'label'    => __( 'Image Source Prompt', 'radcliffe'),
		        'priority' => 28,
		        'description' => __( 'Directions for the license selection' ),
		        'section'  => 'write_form',
		        'settings' => 'item_license_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

			
 	// Sanitize text
	function sanitize_text( $text ) {
	    return sanitize_text_field( $text );
	}
}


function truwriter_form_default_prompt() {
	 if ( get_theme_mod( 'default_prompt') != "" ) {
	 	return get_theme_mod( 'default_prompt');
	 }	else {
	 	return 'Enter the content for your writing below. You must save first and preview once before it goes into the system as a draft. After that, continue to edit, save, and preview as much as needed. Remember to click  "Publish Final" when you are done. If you include your email address, we can send you a link that will allow you to make changes later.';
	 }
}


function truwriter_form_re_edit_prompt() {
	 if ( get_theme_mod( 're_edit_prompt') != "" ) {
	 	return get_theme_mod( 're_edit_prompt');
	 }	else {
	 	return 'You can now re-edit any part of this previously published writing. If you do not save any final changes, it will be left as it was before.';
	 }
}

function truwriter_form_item_title() {
	 if ( get_theme_mod( 'item_title') != "" ) {
	 	echo get_theme_mod( 'item_title');
	 }	else {
	 	echo 'The Title';
	 }
}

function truwriter_form_item_title_prompt() {
	 if ( get_theme_mod( 'item_title_prompt') != "" ) {
	 	echo get_theme_mod( 'item_title_prompt');
	 }	else {
	 	echo 'A good title is important! Create an eye-catching title for your story, one that would make a person who sees it want to stop whatever they are doing and read it..';
	 }
}

function truwriter_form_item_byline() {
	 if ( get_theme_mod( 'item_byline') != "" ) {
	 	echo get_theme_mod( 'item_byline');
	 }	else {
	 	echo 'How to List Author';
	 }
}

function truwriter_form_item_byline_prompt() {
	 if ( get_theme_mod( 'item_byline_prompt') != "" ) {
	 	echo get_theme_mod( 'item_byline_prompt');
	 }	else {
	 	echo 'Publish under your name, twitter handle, secret agent name, or remain "Anonymous". If you include a twitter handle such as @billyshakespeare, when someone tweets your work you will get a lovely notification.';
	 }
}

function truwriter_form_item_header_image() {
	 if ( get_theme_mod( 'item_header_image') != "" ) {
	 	echo get_theme_mod( 'item_header_image');
	 }	else {
	 	echo 'Header Image';
	 }
}

function truwriter_form_item_header_image_prompt() {
	 if ( get_theme_mod( 'item_header_image_prompt') != "" ) {
	 	echo get_theme_mod( 'item_header_image_prompt');
	 }	else {
	 	echo 'You can upload any image file to be used in the header or choose from ones that have already been added to the site. Ideally this image should be at least 1440px wide for photos. Any uploaded image should either be your own or one licensed for re-use; provide an attribution credit for the image in the caption field below.';
	 }
}

function truwriter_form_item_header_caption() {
	 if ( get_theme_mod( 'item_header_caption') != "" ) {
	 	echo get_theme_mod( 'item_header_caption');
	 }	else {
	 	echo 'Caption/credits for header image';
	 }
}

function truwriter_form_item_header_caption_prompt() {
	 if ( get_theme_mod( 'item_header_caption_prompt') != "" ) {
	 	echo get_theme_mod( 'item_header_caption_prompt');
	 }	else {
	 	echo 'Provide full credit / attribution for the header image.';
	 }
}


function truwriter_form_item_writing_area() {
	 if ( get_theme_mod( 'item_writing_area') != "" ) {
	 	echo get_theme_mod( 'item_writing_area');
	 }	else {
	 	echo 'Writing Area';
	 }
}

function truwriter_form_item_writing_area_prompt() {
	 if ( get_theme_mod( 'item_writing_area_prompt') != "" ) {
	 	echo get_theme_mod( 'item_writing_area_prompt');
	 }	else {
	 	echo 'Use the editing area below the toolbar to write and format your writing. You can also paste formatted content here (e.g. from MS Word or Google Docs). The editing tool will do its best to preserve standard formatting--headings, bold, italic, lists, footnotes, and hypertext links. Click "Add Media" to upload images to include in your writing or choose from the media already in the media library (click on the tab labelled "media library"). You can also embed audio and video from many social sites simply by putting the URL of the media on a separate line (you will see a place holder in the editor, but the media will only show in preview and when published).  Click and drag the icon in the lower right to resize the editing space.';
	 }
}

function truwriter_form_item_footer() {
	 if ( get_theme_mod( 'item_footer') != "" ) {
	 	echo get_theme_mod( 'item_footer');
	 }	else {
	 	echo 'Additional Information for Footer';
	 }
}

function truwriter_form_item_footer_prompt() {
	 if ( get_theme_mod( 'item_footer_prompt') != "" ) {
	 	echo get_theme_mod( 'item_footer_prompt');
	 }	else {
	 	echo 'Add any endnote / credits information you wish to append to the end of your writing, such as a citation to where it was previously published or any other meta information. URLs will be automatically hyperlinked when published.';
	 }
}

function truwriter_form_item_license() {
	 if ( get_theme_mod( 'item_license') != "" ) {
	 	echo get_theme_mod( 'item_license');
	 }	else {
	 	echo 'Creative Commons License';
	 }
}

function truwriter_form_item_license_prompt() {
	 if ( get_theme_mod( 'item_license_prompt') != "" ) {
	 	echo get_theme_mod( 'item_license_prompt');
	 }	else {
	 	echo 'Choose your preferred license.';
	 }
}

function truwriter_form_item_categories() {
	 if ( get_theme_mod( 'item_categories') != "" ) {
	 	echo get_theme_mod( 'item_categories');
	 }	else {
	 	echo 'Kind of Writing';
	 }
}

function truwriter_form_item_categories_prompt() {
	 if ( get_theme_mod( 'item_categories_prompt') != "" ) {
	 	echo get_theme_mod( 'item_categories_prompt');
	 }	else {
	 	echo 'Check as many that apply.';
	 }
}

function truwriter_form_item_tags() {
	 if ( get_theme_mod( 'item_tags') != "" ) {
	 	echo get_theme_mod( 'item_tags');
	 }	else {
	 	echo 'Tags';
	 }
}

function truwriter_form_item_tags_prompt() {
	 if ( get_theme_mod( 'item_tags_prompt') != "" ) {
	 	echo get_theme_mod( 'item_tags_prompt');
	 }	else {
	 	echo 'Add any descriptive tags for your writing. Separate multiple ones with commas.';
	 }
}

function truwriter_form_item_email() {
	 if ( get_theme_mod( 'item_email') != "" ) {
	 	echo get_theme_mod( 'item_email');
	 }	else {
	 	echo 'Your Email Address';
	 }
}

function truwriter_form_item_email_prompt() {
	 if ( get_theme_mod( 'item_email_prompt') != "" ) {
	 	echo get_theme_mod( 'item_email_prompt');
	 }	else {
	 	echo 'If you provide an email address when your writing is published, you can request a special link that will allow you to edit it again in the future.';
	 }
}


function truwriter_form_item_editor_notes() {
	 if ( get_theme_mod( 'item_editor_notes') != "" ) {
	 	echo get_theme_mod( 'item_editor_notes');
	 }	else {
	 	echo 'Extra Information for Editors';
	 }
}

function truwriter_form_item_editor_notes_prompt() {
	 if ( get_theme_mod( 'item_editor_notes_prompt') != "" ) {
	 	echo get_theme_mod( 'item_editor_notes_prompt');
	 }	else {
	 	echo 'This information will *not* be published with your work, it is informational for the editor use only.';
	 }
}




# -----------------------------------------------------------------
# Author Edit Link - cause we want people to come back and get to their stuff
# -----------------------------------------------------------------

// add meta box to show edit link on posts in dashboard
function truwriter_editlink_meta_box() {

	add_meta_box(
		're_editlink',
		'Author Re-Edit Link',
		'truwriter_editlink_meta_box_callback',
		'post',
		'side'
	);
}
add_action( 'add_meta_boxes', 'truwriter_editlink_meta_box' );

// content for edit link meta box
function truwriter_editlink_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'truwriter_editlink_meta_box_data', 'truwriter_editlink_meta_box_nonce' );

	// get edit key, it's in the meta, baby!
	$ekey = get_post_meta( $post->ID, 'wEditKey', 1 );
	
	// Create an edit link if it does not exist
	if ( !$ekey ) {
		truwriter_make_edit_link( $post->ID );
		$ekey = get_post_meta( $post->ID, 'wEditKey', 1 );
	}

	echo '<label for="writing_edit_link">';
	_e( 'Click to highlight, then copy', 'radcliffe' );
	echo '</label> ';
	echo '<input style="width:100%; type="text" id="writing_edit_link" name="writing_edit_link" value="' . get_bloginfo('url') . '/write/?wid=' . $post->ID . '&tk=' . $ekey  . '"  onclick="this.select();" />';
	
	}


function truwriter_make_edit_link( $post_id, $post_title='') {
	// add a token for editing by using the post title as a trugger
	// ----h/t via http://www.sitepoint.com/generating-one-time-use-urls/
	
	if ( $post_title == '')   $post_title = get_the_title($post_id );
	update_post_meta( $post_id, 'wEditKey', sha1( uniqid( $post_title, true ) ) );
}

function truwriter_mail_edit_link ( $wid, $mode = 'request' )  {

	// for post id = $wid
	// requested means by click of button vs one sent when published.
	
	// look up the stored edit key 
	$wEditKey = get_post_meta( $wid, 'wEditKey', 1 );

	// While in there get the email address
	$wEmail = get_post_meta( $wid, 'wEmail', 1 );

	// Link for the written thing
	$wLink = get_permalink( $wid);
	
	// who gets mail? They do.
	$to_recipient = $wEmail;

	$wTitle = htmlspecialchars_decode( get_the_title( $wid ) );
	
	$edit_instructions = '<p>To be able to edit this work, just follow these steps</p> <ol><li><a href="' . get_bloginfo('url') . '/desk">Activate the writer</a>. This will send you to the blank writing form. Ignore it. </li><li>Now, use this link to access your work<br />&nbsp; &nbsp; <a href="' . get_bloginfo('url') . '/write/?wid=' . $wid . '&tk=' . $wEditKey  . '">' . get_bloginfo('url') . '/write/?wid=' . $wid . '&tk=' . $wEditKey  . '</a></li></ol><p>That link should open your last edited version in the writer so you can make any modifications to it. Save this email as a way to always return to edit your writing.</p>';
	
	if ( $mode == 'request' ) {
		// subject and message for a edut link request
		$subject ='Edit Link for "' . $wTitle . '"';
		
		$message = '<p>A request was made hopefully by you for the link to edit the content of <a href="' . $wLink . '">' . $wTitle . '</a> published on ' . get_bloginfo( 'name')  . ' at <strong>' . $wLink . '</strong>. (If this was not done by you, just ignore this message)</p>' . $edit_instructions;
		
	} else {
		// message for a just been published notification
		$subject = '"' . $wTitle . '" ' . 'is now published';
		
		$message = 'Your writing <a href="' . $wLink . '">' . $wTitle . '</a> has been published on ' . get_bloginfo( 'name')  . ' and is now available at <strong><a href="' . $wLink . '">' . $wLink . '</a></strong>.</p>' . $edit_instructions;
	}

	// turn on HTML mail
	add_filter( 'wp_mail_content_type', 'set_html_content_type' );

	// mail it!
	$mail_sent = wp_mail( $to_recipient, $subject, $message );

	// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	

	if ($mode == 'request') {
		if 	($mail_sent) {
			echo 'Instructions sent via email';
		} else {
			echo 'Uh op mail not sent';
		}
	}
}

function truwriter_publish ( $post ) {
	 truwriter_mail_edit_link ( $post->ID, 'published' );
    // Send edit link when published  
}

add_action(  'publish_post',  'truwriter_publish', 10, 2 );

# -----------------------------------------------------------------
# Creative Commons Licensing
# -----------------------------------------------------------------


function cc_license_html ($license, $author='', $yr='') {
	// outputs the proper license
	// $license is abbeviation. author is from post metadata, yr is from post date
	
	if ( !isset( $license ) or $license == '' ) return '';
	
	if ($license == 'copyright') {
		// boo copyrighted! sigh, slap on the copyright text. Blarg.
		return 'This work by ' . $author . ' is &copy;' . $yr . ' All Rights Reserved';
	} 
	
	// names of creative commons licenses
	$commons = array (
		'by' => 'Attribution',
		'by-sa' => 'Attribution-ShareAlike',
		'by-nd' => 'Attribution-NoDerivs',
		'by-nc' => 'Attribution-NonCommercial',
		'by-nc-sa' => 'Attribution-NonCommercial-ShareAlike',
		'by-nc-nd' => 'Attribution-NonCommercial-NoDerivs',
	);
	
	// do we have an author?
	$credit = ($author == '') ? '' : ' by ' . $author;
	
	return '<a rel="license" href="http://creativecommons.org/licenses/' . $license . '/4.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/' . $license . '/4.0/88x31.png" /></a><br />This work' . $credit . ' is licensed under a <a rel="license" href="http://creativecommons.org/licenses/' . $license . '/4.0/">Creative Commons ' . $commons[$license] . ' 4.0 International License</a>.';            
}


function cc_license_select_options ($curr) {
	// output for select form options for use in forms

	$str = '';
	
	// to restrict the list of options, comment out lines you do not want
	// to make available (HACK HACK HACK)
	$licenses = array (
		'by' => 'Creative Commons Attribution',
		'by-sa' => 'Creative Commons Attribution-ShareAlike',
		'by-nd' => 'Creative Commons Attribution-NoDerivs',
		'by-nc' => 'Creative Commons Attribution-NonCommercial',
		'by-nc-sa' => 'Creative Commons Attribution-NonCommercial-ShareAlike',
		'by-nc-nd' => 'Creative Commons Attribution-NonCommercial-NoDerivs',
		'copyright' => 'Copyrighted All Rights Reserved',
	);
	
	foreach ($licenses as $key => $value) {
		// build the striing of select options
		$selected = ( $key == $curr ) ? ' selected' : '';
		$str .= '<option value="' . $key . '"' . $selected  . '>' . $value . '</option>';
	}
	
	return ($str);
}

# -----------------------------------------------------------------
# Useful spanners and wrenches
# -----------------------------------------------------------------

function page_with_template_exists ( $template ) {
	// returns true if at least one Page exists that uses given template

	// look for pages that use the given template
	$seekpages = get_posts (array (
				'post_type' => 'page',
				'meta_key' => '_wp_page_template',
				'meta_value' => $template
			));
	 
	// did we find any?
	$pages_found = ( count ($seekpages) ) ? true : false ;
	
	// report to base
	return ($pages_found);
}


// function to get the caption for an attachment (stored as post_excerpt)
// -- h/t http://wordpress.stackexchange.com/a/73894/14945
function get_attachment_caption_by_id( $post_id ) {
    $the_attachment = get_post( $post_id );
    return ( $the_attachment->post_excerpt ); 
}

function reading_time_check() {
// checks for installation of Reading Time WP plugin https://wordpress.org/plugins/reading-time-wp/

	if ( shortcode_exists( 'rt_reading_time' ) ) {
		// yep, golden
		return ('The Reading Time WP plugin is installed. No further action necessary.');
	} else {
		// nope, send them off to set it up
		return ('The <a href="https://wordpress.org/plugins/reading-time-wp/" target="_blank">The Reading Time WP plugin</a> is NOT installed. You might want it-- it\'s not needed, but it\'s nifty.  <a href="' . admin_url( 'plugins.php') . '">Do it now!</a>');
	}
}


function truwriter_get_reading_time( $prefix_string, $suffix_string ) {
	// return the estimated reading time only if the short code (aka plugin) exists. 
	// Start with the prefix string add an approximation symbol and append suffix

	if ( shortcode_exists( 'rt_reading_time' ) ) {		
		return ( $prefix_string . ' ~' . do_shortcode( '[rt_reading_time postfix="minutes" postfix_singular="minute"]' ) . $suffix_string );
	}
}

function splot_get_twitter_name( $str ) {
	// takes an author string and extracts a twitter handle if there is one 
	
	$found = preg_match('/@(\\w+)\\b/i', '$str', $matches);
	
	if ($found) {
		return $matches[0];
	} else {
		return false;
	}
}

function truwriter_author_user_check( $expected_user = 'writer' ) {
// checks for the proper authoring account set up

	$auser = get_user_by( 'login', $expected_user );
		
	if ( !$auser) {
		return ('The Authoring account not set up. You need to <a href="' . admin_url( 'user-new.php') . '">create a user account</a> with login name <strong>' . $expected_user . '</strong> with a role of <strong>Author</strong>. Make a killer strong password; no one uses it. Not even you.');
	} elseif ( $auser->roles[0] != 'author') {
	
		// for multisite let's check if user is not member of blog
		if ( is_multisite() AND !is_user_member_of_blog( $auser->ID, get_current_blog_id() ) )  {
			return ('The user account <strong>' . $expected_user . '</strong> is set up but it has not been added as a user to this site (and needs to have a role of <strong>Author</strong>). You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit the account now</a>'); 
			
		} else {
		
			return ('The user account <strong>' . $expected_user . '</strong> is set up but needs to have it\'s role set to <strong>Author</strong>. You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
		}
		
		
		
	} else {
		return ('The authoring account <strong>' . $expected_user . '</strong> is correctly set up. You are ready to Write and Roll. Or your site users are.');
	}
}


function truwriter_check_user( $allowed='writer' ) {
	// checks if the current logged in user is who we expect
   $current_user = wp_get_current_user();
	
	// return check of match
	return ( $current_user->user_login == $allowed );
}


function twitternameify( $str ) {
	// convert any "@" in astring to a linked twitter name
	// ----- h/t http://snipe.net/2009/09/php-twitter-clickable-links/
	$str = preg_replace( "/@(\w+)/", "<a href=\"https://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $str );

	return $str;
}

function splot_the_author() {
	// utility to put in template to show status of special logins
	// nothing is printed if there is not current user, 
	//   echoes (1) if logged in user is the special account
	//   echoes (0) if logged in user is the another account
	//   in both cases the code is linked to a logout script

	
	if ( is_user_logged_in() and !current_user_can( 'edit_others_posts' ) )  {
	
		$user_code = ( truwriter_check_user() ) ? 1 : 0;
		echo '<a href="' . wp_logout_url( site_url() ). '">(' . $user_code  .')</a>';
	}
}

function truwriter_publink ( $redirect ) {
	// for feedback after publishing, for guest users we want to return
	// a logout link, if we are an editor or admin, we just want the regular link
	if ( is_user_logged_in() and !current_user_can( 'edit_others_posts' ) )  {
		return ( wp_logout_url( $redirect ) );
	} else {
		return ( $redirect  );
	}
}

function get_page_id_by_slug( $page_slug ) {
	// pass the slug and get it's id, so we can use most basic permalink structure
	// ----- h/t https://gist.github.com/davidpaulsson/9224518
	
	// get page as object
	$page = get_page_by_path( $page_slug );
	
	if ( $page ) {
		return $page->ID;
	} else {
		return null;
	}
}



function set_html_content_type() {
	// from http://codex.wordpress.org/Function_Reference/wp_mail
	return 'text/html';
}

function br2nl ( $string )
// convert HTML <br> tags to new lines
// from http://php.net/manual/en/function.nl2br.php#115182
{
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

?>