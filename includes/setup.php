<?php

# -----------------------------------------------------------------
# Theme activation: Wonder Theme ACTIVATE
# -----------------------------------------------------------------

// run when this theme is activated
add_action( 'after_switch_theme', 'truwriter_setup' );

function truwriter_setup() {

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
			'post_content'	=> 'Here is the place to compose,  , and hone your fine words. If you are building this site, maybe edit this page to customize this wee bit of text.',
			'post_name'		=> 'write',
			'post_status'	=> 'publish',
			'post_type'		=> 'page',
			'post_author' 	=> 1,
			'post_date' 	=> date('Y-m-d H:i:s', time() - 172800),
			'page_template'	=> 'page-write.php',
		);

		wp_insert_post( $page_data );
	}

	// add rewrite rules, then flush to make sure they stick.
	truwriter_rewrite_rules();
	flush_rewrite_rules();
}

# -----------------------------------------------------------------
# Set up the table and put the napkins out, stuff we do every visit
# -----------------------------------------------------------------

// get theme options early in the flow
add_action( 'after_setup_theme', 'truwriter_load_theme_options', 9 );

# -----------------------------------------------------------------
# Dashboard menus
# -----------------------------------------------------------------

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

add_action( 'admin_menu', 'truwriter_change_post_label' );

function truwriter_change_post_label() {
    global $menu;
    global $submenu;

    $thing_name = 'Writing';

    $menu[5][0] = $thing_name . 's';
    $submenu['edit.php'][5][0] = 'All ' . $thing_name . 's';
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
# Admin has style!
# -----------------------------------------------------------------

add_action('admin_enqueue_scripts', 'truwriter_custom_admin_styles');

function truwriter_custom_admin_styles(){
    wp_enqueue_style( 'admin_css',  get_stylesheet_directory_uri() . '/includes/admin.css');
}

# -----------------------------------------------------------------
# Query vars and Redirects
# -----------------------------------------------------------------

// -----  add allowable url parameters so we can do reall cool stuff, wally
add_filter('query_vars', 'truwriter_queryvars' );

function truwriter_queryvars( $qvars ) {
	$qvars[] = 'tk'; // token key for editing previously made stuff
	$qvars[] = 'wid'; // post id for editing
	$qvars[] = 'random'; // random flag
	$qvars[] = 'elink'; // edit link flag
	$qvars[] =  'ispre'; // another preview flag

	return $qvars;
}


/* set up rewrite rules */
function truwriter_rewrite_rules() {
	// for sending to random item
   add_rewrite_rule('random/?$', 'index.php?random=1','top');

   // for edit link requests
   add_rewrite_rule( '^get-edit-link/([^/]+)/?',  'index.php?elink=1&wid=$matches[1]','top');

}

// redirections for rewrites on the /random and /get-edit-link
add_action( 'template_redirect', 'truwriter_write_director' );

function truwriter_write_director() {

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

// prevent posts from being saved to /random (reserved for random post generator

add_action( 'save_post', 'splot_save_post_random_check' );

function splot_save_post_random_check( $post_id ) {
    // verify post is not a revision and that the post slug is "random"

    $new_post = get_post( $post_id );
    if ( ! wp_is_post_revision( $post_id ) and  $new_post->post_name == 'random' ) {
        // unhook this function to prevent infinite looping
        remove_action( 'save_post', 'splot_save_post_random_check' );

        // update the post slug
        wp_update_post( array(
            'ID' => $post_id,
            'post_name' => 'randomly' // do your thing here
        ));

        // re-hook this function
        add_action( 'save_post', 'splot_save_post_random_check' );

    }
}


# -----------------------------------------------------------------
# Sorting posts
# -----------------------------------------------------------------

add_action( 'pre_get_posts', 'truwriter_order_items' );

function truwriter_order_items( $query ) {


	// check for not being an admin screem and a main query
	if ( !is_admin() && $query->is_main_query()  ) {

		// test on the settings
		switch (truwriter_option('sort_applies')) {

			case 'all':
				// use sorting on home, all archives, and search results
				if (  $query->is_home() OR $query->is_archive() OR $query->is_search() ) {
					splotbox_query_set($query);
				}
				break;

			case 'front':
				if (  $query->is_home() ) {
					// use sorting on home only
					splotbox_query_set($query);
				}
				break;
			case 'tag':
				if (  $query->is_tag() ) {
					// tag archive
					splotbox_query_set($query);
				}
				break;
			case 'cat':
				if (  $query->is_category() ) {
					// category archive
					splotbox_query_set($query);
				}
				break;
			case 'tagcat':
				if (  $query->is_tag() OR $query->is_category() ) {
					// any archive that made it this far
					splotbox_query_set($query);
				}
				break;

		} // switch

	} // if  main query

}

function splotbox_query_set ($the_query) {
	//utility to set the query as per the theme settings
	$the_query->set( 'orderby', truwriter_option('sort_by')  );
	$the_query->set( 'order', truwriter_option('sort_direction') );

}


# -----------------------------------------------------------------
# Comments
# -----------------------------------------------------------------

// Customize the headings for the comment form
add_filter('comment_form_defaults', 'truwriter_comment_mod');

function truwriter_comment_mod( $defaults ) {
	$defaults['title_reply'] = get_truwriter_comment_title();
	$defaults['title_reply_after'] = '</h3>' . get_truwriter_comment_extra_intro();
	$defaults['logged_in_as'] = '';
	$defaults['title_reply_to'] = 'Provide Feedback for %s';
	$defaults['label_submit'] = get_truwriter_comment_button_label();
	return $defaults;
}


// possibly add writer email to comment notifications
// add_filter( 'comment_moderation_recipients', 'truwriter_comment_notification_recipients', 15, 2 );
add_filter( 'comment_notification_recipients', 'truwriter_comment_notification_recipients', 15, 2 );

function truwriter_comment_notification_recipients( $emails, $comment_id ) {

	 $comment = get_comment( $comment_id );

	 // check if we should send notifications
	 if ( truwriter_ok_to_notify( $comment ) ) {
	 	// find post id from comment ID and fetch the email address to append to notifications
		$emails[] = get_post_meta(  $comment->comment_post_ID, 'wEmail', 1 );
	}
 	return ( $emails );
}

// modify the comment notification for content creators, non users dont need the wordpress comment mod stuff
// h/t https://wordpress.stackexchange.com/a/170151/14945

add_filter( 'comment_notification_text', 'truwriter_comment_notification_text', 20, 2 );

function truwriter_comment_notification_text( $notify_message, $comment_id ){
    // get the current comment
    $comment = get_comment( $comment_id );

    // change notification only for recipient who is the author of this an item (e.g. skip for admins)
    if ( truwriter_ok_to_notify( $comment ) ) {
    	// get post data
    	$post = get_post( $comment->comment_post_ID );

		// don't modify trackbacks or pingbacks
		if ( '' == $comment->comment_type ){
			// build the new message text
			$notify_message  = sprintf( __( 'New comment on  "%s" published at "%s"' ), $post->post_title, get_bloginfo( 'name' ) ) . "\r\n\r\n----------------------------------------\r\n";
			$notify_message .= sprintf( __('Author : %1$s'), $comment->comment_author ) . "\r\n";
			$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
			$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
			$notify_message .= sprintf( __('Comment Link: %s'), get_comment_link( $comment_id ) ) . "\r\n\r\n----------------------------------------\r\n";
			$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n----------------------------------------\r\n\r\n";

			$notify_message .= __('See all comments: ') . "\r\n";
			$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";

		}
	}

	// return the notification text
    return $notify_message;
}

function truwriter_ok_to_notify( $comment ) {
	// check if theme options are set to use comments and that the post associated with comment has the notify flag activated
	return ( truwriter_option('allow_comments') and get_post_meta( $comment->comment_post_ID, 'wCommentNotify', 1 ) );
}

# -----------------------------------------------------------------
# Tiny-MCE mods
# -----------------------------------------------------------------

add_filter( 'tiny_mce_before_init', 'truwriter_tinymce_settings' );

function truwriter_tinymce_settings( $settings ) {

	$settings['images_upload_handler'] = 'function (blobInfo, success, failure) {
    var xhr, formData;

    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open(\'POST\', \'' . admin_url('admin-ajax.php') . '\');

    xhr.onload = function() {
      var json;

      if (xhr.status != 200) {
        failure(\'HTTP Error: \' + xhr.status);
        return;
      }

      json = JSON.parse(xhr.responseText);

      if (!json || typeof json.location != \'string\') {
        failure(\'Invalid JSON: \' + xhr.responseText);
        return;
      }

      success(json.location);
    };

    formData = new FormData();
    formData.append(\'file\', blobInfo.blob(), blobInfo.filename());
	formData.append(\'action\', \'truwriter_upload_action\');
    xhr.send(formData);
  }';



	return $settings;
}



function truwriter_register_buttons( $plugin_array ) {
	$plugin_array['imgbutton'] = get_stylesheet_directory_uri() . '/js/image-button.js';
	return $plugin_array;
}

// remove  buttons from the visual editor

function truwriter_tinymce_buttons($buttons) {
	//Remove the more button
	$remove = 'wp_more';

	// Find the array key and then unset
	if ( ( $key = array_search($remove,$buttons) ) !== false )
		unset($buttons[$key]);

	// now add the image button in,
	$buttons[] = 'image';

	return $buttons;
 }

// remove  more buttons from the visual editor


function truwriter_tinymce_2_buttons( $buttons)  {
	//Remove the keybord shortcut and paste text buttons
	$remove = array('wp_help','pastetext');

	return array_diff($buttons,$remove);
 }


// this is the handler used in the tiny_mce editor to manage image upload
add_action( 'wp_ajax_nopriv_truwriter_upload_action', 'truwriter_upload_action' ); //allow on front-end
add_action( 'wp_ajax_truwriter_upload_action', 'truwriter_upload_action' );

function truwriter_upload_action() {

    $newupload = 0;

    if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
            );

            $_FILES = array('upload'=>$newfile);
            foreach($_FILES as $file => $array) {
                $newupload = media_handle_upload( $file, 0);
            }
        }
    }
    echo json_encode( array('id'=> $newupload, 'location' => wp_get_attachment_image_src( $newupload, 'large' )[0], 'caption' => get_attachment_caption_by_id( $newupload ) ) );
    die();
}

# -----------------------------------------------------------------
# For the Writing Form
# -----------------------------------------------------------------

add_action('wp_head', 'truwriter_no_featured_image');

function truwriter_no_featured_image() {
	if ( is_page( truwriter_get_write_page() ) and isset( $_POST['truwriter_form_make_submitted'] ) ) {
    ?>
        <style>
            .featured-media {
                display:none;
            }
        </style>
    <?php
    }
}


// filter content on writing page so we do not submit the page content if form is submitted
add_filter( 'the_content', 'truwriter_firstview' );

function truwriter_firstview( $content ) {
    // Check if we're inside the main loop on the writing page
    if ( is_page( truwriter_get_write_page() ) && in_the_loop() && is_main_query() ) {

    	if ( isset( $_POST['truwriter_form_make_submitted'] ) ) {
    		return '';
    	} else {
    		 return $content;
    	}

    }

    return $content;
}


# -----------------------------------------------------------------
# Enqueue scripts and styles
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

 	if ( is_page( truwriter_get_write_page() ) ) { // use on just our form page

		if (! is_admin() ) {
			// add media scripts if we are on our collect page and not an admin
		 	// after http://wordpress.stackexchange.com/a/116489/14945
			wp_enqueue_media();

			// Build in tag auto complete script
			wp_enqueue_script( 'suggest' );
		}

   		// Autoembed functionality in rich text editor
   		// needs dependency on tiny_mce
   		// h/t https://wordpress.stackexchange.com/a/287623

   		wp_enqueue_script( 'mce-view', '', array('tiny_mce'), '', true );


		// tinymce mods
		add_filter("mce_external_plugins", "truwriter_register_buttons");
		add_filter('mce_buttons','truwriter_tinymce_buttons');
		add_filter('mce_buttons_2','truwriter_tinymce_2_buttons');

		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.writer' , get_stylesheet_directory_uri() . '/js/jquery.writer.js', array( 'suggest') , false, true );

		// add a local variable for the site's home url
		wp_localize_script(
		  'jquery.writer',
		  'writerObject',
		  array(
		  	'ajaxUrl' => admin_url('admin-ajax.php'),
			'siteUrl' => esc_url(home_url()),
			'uploadMax' => truwriter_option('upload_max' )
		  )
		);

		wp_enqueue_script( 'jquery.writer' );

		// use built in Thickbox for writing help pop over
		add_thickbox();


	} elseif ( is_single() ) {
		// single writings, give is the jQuery for edit link stuff

		wp_register_script( 'jquery.editlink' , get_stylesheet_directory_uri() . '/js/jquery.editlink.js', null , '0.2', TRUE );
		wp_enqueue_script( 'jquery.editlink' );
	}
}


# -----------------------------------------------------------------
# Tag Search
# -----------------------------------------------------------------


add_filter( 'wp_headers', 'splot_send_cors_headers', 11, 1 );

function splot_send_cors_headers( $headers ) {
	if ( is_page( truwriter_get_write_page() ) ) {
    	$headers['Access-Control-Allow-Origin'] = '*';
    }
    return $headers;
}

// this is the handler used in the tiny_mce editor to manage image upload
add_action( 'wp_ajax_nopriv_splot_ajax_tag_search', 'splot_ajax_tag_search' ); //allow on front-end
add_action( 'wp_ajax_splot_ajax_tag_search', 'splot_ajax_tag_search' );


/* local version of wp_ajax_ajax_tag_search without exit for user capabilties
   (this requires a logged in user which we do not always have

   modified from
   https://developer.wordpress.org/reference/functions/wp_ajax_ajax_tag_search
*/

function splot_ajax_tag_search() {
    if ( ! isset( $_GET['tax'] ) ) {
        wp_die( 0 );
    }

    $taxonomy = sanitize_key( $_GET['tax'] );
    $tax      = get_taxonomy( $taxonomy );

    if ( ! $tax ) {
        wp_die( 0 );
    }

    $s = wp_unslash( $_GET['q'] );

    $comma = _x( ',', 'tag delimiter' );
    if ( ',' !== $comma ) {
        $s = str_replace( $comma, ',', $s );
    }

    if ( false !== strpos( $s, ',' ) ) {
        $s = explode( ',', $s );
        $s = $s[ count( $s ) - 1 ];
    }

    $s = trim( $s );

    /**
     * Filters the minimum number of characters required to fire a tag search via Ajax.
     *
     * @since 4.0.0
     *
     * @param int         $characters The minimum number of characters required. Default 2.
     * @param WP_Taxonomy $tax        The taxonomy object.
     * @param string      $s          The search term.
     */
    $term_search_min_chars = (int) apply_filters( 'term_search_min_chars', 2, $tax, $s );

    /*
     * Require $term_search_min_chars chars for matching (default: 2)
     * ensure it's a non-negative, non-zero integer.
     */
    if ( ( 0 == $term_search_min_chars ) || ( strlen( $s ) < $term_search_min_chars ) ) {
        wp_die();
    }

    $results = get_terms(
        array(
            'taxonomy'   => $taxonomy,
            'name__like' => $s,
            'fields'     => 'names',
            'hide_empty' => false,
        )
    );

    echo implode( "\n", $results );
    wp_die();
}


# -----------------------------------------------------------------
# Grab Bag
# -----------------------------------------------------------------


// set the default upload image size to "large' cause medium is puny
// ----- h/t http://stackoverflow.com/a/20019915/2418186

add_filter( 'pre_option_image_default_size', 'my_default_image_size' );

function my_default_image_size () {
    return 'large';
}

function  truwriter_show_drafts( $query ) {
// show drafts only for single previews
    if ( is_user_logged_in() || is_feed() || !is_single() )
        return;

    $query->set( 'post_status', array( 'publish', 'draft' ) );
}

add_action( 'pre_get_posts', 'truwriter_show_drafts' );

// enable previews of posts for non-logged in users
// ----- h/t https://wordpress.stackexchange.com/a/164088/14945

add_filter( 'the_posts', 'truwriter_reveal_previews', 10, 2 );

function truwriter_reveal_previews( $posts, $wp_query ) {

    //making sure the post is a preview to avoid showing published private posts
    if ( !is_preview() )
        return $posts;

    if ( is_user_logged_in() )
    	 return $posts;

    if ( count( $posts ) )
        return $posts;

    if ( !empty( $wp_query->query['p'] ) ) {
        return array ( get_post( $wp_query->query['p'] ) );
    }
}

function truwriter_is_preview() {
	return ( get_query_var( 'ispre', 0 ) == 1);
}


# -----------------------------------------------------------------
# login stuff - things to set up special user, prevent access to WP
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
add_action( 'login_enqueue_scripts', 'splot_login_logo' );

function splot_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            height:90px;
			width:320px;
			background-size: 320px 90px;
			background-repeat: no-repeat;
			padding-bottom: 0px;
        }
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'splot_login_link' );

function splot_login_link( $url ) {
	return 'https://splot.ca/';
}

/* Customize message above registration form */

add_filter('login_message', 'splot_add_login_message');

function splot_add_login_message() {
	return '<p class="message">To do all that is SPLOT!</p>';
}

// login page title
add_filter( 'login_headertext', 'splot_login_logo_url_title' );

function splot_login_logo_url_title() {
	return 'The grand mystery of all things SPLOT';
}


# -----------------------------------------------------------------
# Menu Setup
# -----------------------------------------------------------------

// create a basic menu if one has not been define for primary
function splot_default_menu() {

	// site home with trailing slash
	$splot_home = home_url('/');

 	return ( '<li><a href="' . $splot_home . '">Home</a></li><li><a href="' . $splot_home . truwriter_get_write_page() . '">Write</a></li><li><a href="' . $splot_home . 'random' . '">Random</a></li>' );
}
?>
