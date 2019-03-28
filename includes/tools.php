<?php
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
		return ('The authoring account <strong>' . $expected_user . '</strong> is correctly set up.');
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

// -----  expose post meta date to API
add_action( 'rest_api_init', 'truwriter_create_api_posts_meta_field' );
 
function truwriter_create_api_posts_meta_field() {
 
	register_rest_field( 'post', 'splot_meta', array(
								 'get_callback' => 'truwriter_get_splot_meta_for_api',
 								 'schema' => null,)
 	);
}
 
function truwriter_get_splot_meta_for_api( $object ) {
	//get the id of the post object array
	$post_id = $object['id'];

	// meta data fields we wish to make available
	$splot_meta_fields = ['author' => 'wAuthor', 'license' => 'wLicense', 'footer' => 'wFooter'];
	
	// array to hold stuff
	$splot_meta = [];
 
 	foreach ($splot_meta_fields as $meta_key =>  $meta_value) {
	 	//return the post meta for each field
	 	$splot_meta[$meta_key] =  get_post_meta( $post_id, $meta_value, true );
	 }
	 
	 return ($splot_meta);
 
} 
?>