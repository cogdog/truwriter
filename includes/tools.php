<?php
# -----------------------------------------------------------------
# Page and Template Checks
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

function get_pages_with_template ( $template ) {
	// returns array of pages with a given template

	// look for pages that use the given template
	$seekpages = get_posts (array (
				'post_type' => 'page',
				'meta_key' => '_wp_page_template',
				'meta_value' => $template,
				'posts_per_page' => -1
	));

	// holder for results
	$tpages = array(0 => 'Select Page');


	// Walk those results, store ID of pages found
	foreach ( $seekpages as $p ) {
		$tpages[$p->ID] = $p->post_title;
	}

	return $tpages;
}

function truwriter_get_write_page() {

	// return slud for page set in theme options for writing page (newer versions of SPLOT)
	if ( truwriter_option( 'write_page' ) )  {
		return ( get_post_field( 'post_name', get_post( truwriter_option( 'write_page' ) ) ) );
	} else {
		// older versions of SPLOT use the slug
		return ('write');
	}
}

function splot_redirect_url() {
	// where to send visitors after login ok
	return ( home_url('/') . truwriter_get_write_page() );
}

# -----------------------------------------------------------------
# Shortcodes
# -----------------------------------------------------------------


// ----- short code for number of assignments in the bank
add_shortcode('splotcount', 'splot_count_splots');

function splot_count_splots() {
	return wp_count_posts()->publish;
}


# -----------------------------------------------------------------
# Media
# -----------------------------------------------------------------

// for uploading images
function truwriter_insert_attachment( $file_handler, $post_id ) {

	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) return (false);

	require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
	require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
	require_once( ABSPATH . "wp-admin" . '/includes/media.php' );

	$attach_id = media_handle_upload( $file_handler, $post_id );

	return ($attach_id);

}

// function to get the caption for an attachment (stored as post_excerpt)
// -- h/t http://wordpress.stackexchange.com/a/73894/14945
function get_attachment_caption_by_id( $post_id ) {
    $the_attachment = get_post( $post_id );
    return ( $the_attachment->post_excerpt );
}


# -----------------------------------------------------------------
# Override the Radcliffe Comment Function
# -----------------------------------------------------------------


if ( ! function_exists( 'truwriter_comment' ) ) {

	function truwriter_comment( $comment, $args, $depth ) {
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>

		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

			<?php __( 'Pingback:', 'radcliffe' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'radcliffe' ), '<span class="edit-link">', '</span>' ); ?>

		</li>
		<?php
				break;
			default :
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

			<div id="comment-<?php comment_ID(); ?>" class="comment">

				<?php
				echo get_avatar( $comment, 150 );
				// we have removed the Radcliffe code to display post author


				?>

				<div class="comment-inner">

					<div class="comment-header">

						<cite><?php echo get_comment_author_link(); ?></cite>

						<span><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php echo get_comment_date() . ' &mdash; ' . get_comment_time(); ?></a></span>

					</div>

					<div class="comment-content">

						<?php if ( '0' == $comment->comment_approved ) : ?>

							<p class="comment-awaiting-moderation"><?php __( 'Your comment is awaiting moderation.', 'radcliffe' ); ?></p>

						<?php endif; ?>

						<?php comment_text(); ?>

					</div><!-- .comment-content -->

					<div class="comment-actions">

						<?php edit_comment_link( __( 'Edit', 'radcliffe' ), '', '' ); ?>

						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'radcliffe' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

					</div><!-- .comment-actions -->

				</div><!-- .comment-inner -->

			</div><!-- .comment-## -->

		<?php
			break;
		endswitch;
	}

}


# -----------------------------------------------------------------
# Grab bag
# -----------------------------------------------------------------

function truwriter_word_count( $content ) {
   return str_word_count( strip_tags( $content ) );
}

function truwriter_preview_notice() {
	return ('<div class="notify"><span class="symbol icon-info"></span>
This is a preview of your entry that shows how it will look when published. <a href="#" onclick="self.close();return false;">Close this window/tab</a> when done to return to the writing form. Make any changes and click "Update and Save Draft" again or if it is ready, click "Publish Now".
				</div>');
}


/**
 * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
 * placed under a 'children' member of their parent term.
 * @param Array   $cats     taxonomy term objects to sort
 * @param Array   $into     result array to put them in
 * @param integer $parentId the current parent ID to put them in
   h/t http://wordpress.stackexchange.com/a/99516/14945
 */
function truwriter_sort_terms_hierarchicaly( Array &$cats, Array &$into, $parentId = 0 )
{
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parentId) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $topCat) {
        $topCat->children = array();
        truwriter_sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id);
    }
}


# -----------------------------------------------------------------
# Plugin Checks
# -----------------------------------------------------------------


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


# -----------------------------------------------------------------
# Twittering
# -----------------------------------------------------------------

function splot_get_twitter_name( $str ) {
	// takes an author string and extracts a twitter handle if there is one

	$found = preg_match('/@(\\w+)\\b/i', '$str', $matches);

	if ($found) {
		return $matches[0];
	} else {
		return false;
	}
}

function twitternameify( $str ) {
	// convert any "@" in astring to a linked twitter name
	// ----- h/t http://snipe.net/2009/09/php-twitter-clickable-links/
	$str = preg_replace( "/@(\w+)/", "<a href=\"https://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $str );

	return $str;
}

# -----------------------------------------------------------------
# Email
# -----------------------------------------------------------------

function truwriter_allowed_email_domain( $email ) {
	// checks if an email address is within a list of allowed domains

	// allow for empty entries
	if ( empty($email) ) return true;

	// extract domain h/t https://www.fraudlabspro.com/resources/tutorials/how-to-extract-domain-name-from-email-address/
	$domain = substr($email, strpos($email, '@') + 1);

	$allowables = explode(",", truwriter_option('email_domains'));

	foreach ( $allowables as $item) {
		if ( $domain == trim($item)) return true;
	}

	return false;
}

function set_html_content_type() {
	// from http://codex.wordpress.org/Function_Reference/wp_mail
	return 'text/html';
}


# -----------------------------------------------------------------
# API
# -----------------------------------------------------------------


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
