<?php
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
		truwriter_make_edit_link( $post->ID, $post->post_title );
		$ekey = get_post_meta( $post->ID, 'wEditKey', 1 );
	}

	echo '<label for="writing_edit_link">';
	_e( 'Click to highlight, then copy', 'radcliffe' );
	echo '</label> ';
	echo '<input style="width:100%; type="text" id="writing_edit_link" name="writing_edit_link" value="' . get_bloginfo('url') . '/' . truwriter_get_write_page() . '/?wid=' . $post->ID . '&tk=' . $ekey  . '"  onclick="this.select();" />';

	}


function truwriter_make_edit_link( $post_id, $post_title='') {
	// add a token for editing by using the post title as a trugger
	// ----h/t via http://www.sitepoint.com/generating-one-time-use-urls/

	if ( $post_title == '')   $post_title = get_the_title($post_id );
	update_post_meta( $post_id, 'wEditKey', sha1( uniqid( $post_title, true ) ) );
}

function truwriter_get_edit_link( $post_id ) {
	return ( get_bloginfo('url') . '/' . truwriter_get_write_page() . '/?wid=' . $post_id . '&tk=' . get_post_meta( $post_id, 'wEditKey', 1) );

}

function truwriter_mail_edit_link ( $wid, $mode = 'request' )  {

	// for post id = $wid
	// requested means by click of button vs one sent when published/saved.

	// look up the stored edit key
	$wEditKey = get_post_meta( $wid, 'wEditKey', 1 );

	// While in there get the email address
	$wEmail = get_post_meta( $wid, 'wEmail', 1 );

	// Link for the written thing
	$wLink = get_permalink( $wid );

	// who gets mail? They do.
	$to_recipient = $wEmail;

	// title cleanup
	$wTitle = htmlspecialchars_decode( get_the_title( $wid ) );

	// general how to use this link info
	$edit_instructions = '<p>To be able to edit this work use this special access link <a href="' . get_bloginfo('url') . '/' . truwriter_get_write_page() . '/?wid=' . $wid . '&tk=' . $wEditKey  . '">' . get_bloginfo('url') . '/' . truwriter_get_write_page() . '?wid=' . $wid . '&tk=' . $wEditKey  . '</p>It should open your last edited version so you can make any modifications to it. Save this email as a way to always return to edit your writing or use the Request Edit Link button at the bottom of your published work.</p>';

	if ( $mode == 'request' ) {
		// subject and message for a edit link request from the button press
		$subject ='Edit Link for "' . $wTitle . '"';

		$message = '<p>A request was made to send the link to edit the content of <a href="' . $wLink . '">' . $wTitle . '</a> published on ' . get_bloginfo( 'name')  . ' at <strong>' . $wLink . '</strong>. If this was not requested by you, just ignore this message.</p>' . $edit_instructions;

	} elseif ( $mode == 'draft' ) {
		// message for a draft notification

		$subject = '"' . $wTitle . '" ' . ' saved as draft';
		$message = '<a href="' . $wLink . '">' . $wTitle . '</a> has been saved as a draft on ' . get_bloginfo( 'name')  . '. </p>' . $edit_instructions;

	} else {
		// message for a just been published notification
		$subject = '"' . $wTitle . '" ' . ' is now published';

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
			echo 'Uh oh email not sent';
		}
	}
}

?>
