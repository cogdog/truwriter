<?php
// manages all of the theme options
// heavy lifting via http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
// Revision Aug 22, 2016 as jQuery update killed TAB UI

class truwriter_Theme_Options {

	/* Array of sections for the theme options page */
	private $sections;
	private $checkboxes;
	private $settings;

	/* Initialize */
	function __construct() {

		// This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();

		// set up the settings!
		$this->settings = array();
		$this->get_settings();

		// One General Section to rule them all
		$this->sections['general'] = __( 'General Settings' );

		// create a colllection of callbacks for each section heading
		foreach ( $this->sections as $slug => $title ) {
			$this->section_callbacks[$slug] = 'display_' . $slug;
		}

		// enqueue scripts for media uploader
        add_action( 'admin_enqueue_scripts', 'truwriter_enqueue_options_scripts' );

		// Options actions
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_init', array( &$this, 'export_settings' ) );
		add_action( 'admin_init', array( &$this, 'import_settings' ) );

		if ( ! get_option( 'truwriter_options' ) )
			$this->initialize_settings();
	}

	/* Add page(s) to be available as tabs */
	public function add_pages() {
		add_theme_page( 'TRU Writer Options', 'TRU Writer Options', 'manage_options', 'truwriter-options', array( &$this, 'display_options_page' ) );

		// import/export settings page
		add_theme_page( 'Import/Export', 'Import/Export TRU Writer Options', 'manage_options', 'splot-settings', array( &$this, 'display_import_export_settings' ) );
	}

	/* Display theme options page */
	public function display_options_page() {
		echo '<div class="wrap">
		<h1>TRU Writer Options</h1>';

		// check for notices
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {
			// for settings updates
			echo '<div class="updated fade"><p>' . __( 'TRU Writer settings updated.' ) . '</p></div>';

		} elseif ( isset( $_GET['settings-imported'] ) && $_GET['settings-imported'] == true ) {
			// notice for successful import of settings
			echo '<div class="updated fade"><p>' . __( 'TRU Writer settings successfully imported. Double check the Writing Form page (contnt will not be imported) and the default category.' ) . '</p></div>';
		}

		echo '<form action="options.php" method="post" enctype="multipart/form-data">';

		settings_fields( 'truwriter_options' );

		echo  '<h2 class="nav-tab-wrapper"><a class="nav-tab nav-tab-active" href="?page=truwriter-options">Settings</a><a class="nav-tab" href="?page=splot-settings">Import/Export</a></h2>';

		do_settings_sections( $_GET['page'] );

		echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>
		</form>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($) {

			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});

			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});

			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});

		});
		</script>';
	}

	/*  display import/export options in a tab */
	public function display_import_export_settings() {
		// ------h/t https://pippinsplugins.com/building-settings-import-export-feature/

	 	echo '<div class="wrap">
		<h1>Import and Export TRU Settings</h1>';

		// first the export button
		echo '<form method="post">

		<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="?page=truwriter-options">Settings</a>
		<a class="nav-tab nav-tab-active" href="?page=splot-settings">Import/Export</a></h2><h3>Export Settings</h3><p>Export the TRU Writer settings for this site as a .json file. This allows you to easily import the configuration into another site. </p><p><input type="hidden" name="splot_action" value="export_settings" /></p>
			<p>';
			wp_nonce_field( 'splot_settings_export_nonce', 'splot_settings_export_nonce' );
			submit_button( __( 'Export Settings' ), 'secondary', 'do_export_settings', false );

		// next the import interface
		echo '</form><h3>Import Settings</h3><p>Import the TRU Writer settings from a .json file. This file can be obtained by exporting the settings on another site using the export method above. <strong>This will override the current settings except for the page for the Writing Form and the default category..</strong></p>
		<form method="post" enctype="multipart/form-data">
		<input type="file" name="import_settings"/>
		<input type="hidden" name="splot_action" value="import_settings" />';

		wp_nonce_field( 'splot_settings_import_nonce', 'splot_settings_import_nonce' );

		submit_button( __( 'Import Settings' ), 'secondary', 'do_export_settings', false );

		echo '</form></div>';

	}

	/* Define all settings and their defaults */
	public function get_settings() {

		// for file upload checks
		$max_upload_size = round(wp_max_upload_size() / 1000000);


		/* General Settings
		===========================================*/


		$this->settings['access_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Access to Writing Tool',
			'std'    => 'Use to require a access code to use the writing form.',
			'type'    => 'heading'
		);

		$this->settings['accesscode'] = array(
			'title'   => __( 'Access Code' ),
			'desc'    => __( 'Set necessary code to access the writing tool; leave blank to make wide open' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);

		$this->settings['accesshint'] = array(
			'title'   => __( 'Access Hint' ),
			'desc'    => __( 'Suggestion if someone cannot guess the code. Not super secure' ),
			'std'     => 'Enter a good suggestion here. ',
			'type'    => 'text',
			'section' => 'general'
		);

		$this->settings['pages_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Special Pages Setup',
			'std'    => 'Choose the pages for the two ones that create access to the writingform',
			'type'    => 'heading'
		);

		// get all pages on site with template for the Writing Form
		$found_pages = get_pages_with_template('page-write.php');
		$page_desc = 'Set the Page that should be used for the Writing form.';

		// the function returns an array of id => page title, first item is the menu selection item
		if ( count( $found_pages ) > 1 ) {
			$page_std =  array_keys( $found_pages)[1];
		} else {

			$trypage = get_page_by_path('write');

			if ( $trypage ) {
				$page_std = $trypage->ID;
				$found_pages = array( 0 => 'Select Page', $page_std => $trypage->post_title );

			} else {
				$page_desc = 'No pages have been created with the Writing Pad template. This is required to enable access to the writing form. <a href="' . admin_url( 'post-new.php?post_type=page') . '">Create a new Page</a> and under <strong>Page Attributes</strong> select <code>Writing Pad</code> for the Template.';
				$page_std = '';
			}

		}

		$this->settings['write_page'] = array(
			'section' => 'general',
			'title'   => __( 'Page For Writing Form (Writing Pad)'),
			'desc'    => $page_desc,
			'type'    => 'select',
			'std'     =>  $page_std,
			'choices' => $found_pages
		);


		$this->settings['publish_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Publish Settings',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['pub_status'] = array(
			'section' => 'general',
			'title'   => __( 'Status For New Writings' ),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'pending',
			'choices' => array(
				'pending' => 'Moderated. New submissions are set with a <strong>Pending</strong> status and will not appear until an admin updates the status in Wordpress',
				'publish' => 'Published immediately to site',
			)
		);

		$this->settings['allow_comments'] = array(
			'section' => 'general',
			'title'   => __( 'Allow Comments?' ),
			'desc'    => __( 'Enable comments on published writings.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);



		// ------- sort options
		$this->settings['sort_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Sorting',
			'std'    => 'Set the order of published items on home page and archives.',
			'type'    => 'heading'
		);


		$this->settings['sort_by'] = array(
			'section' => 'general',
			'title'   => __( 'Sort by'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'date',
			'choices' => array (
							'date' => 'Date Published (default)',
							'title' => 'Title',
					)
		);

		$this->settings['sort_direction'] = array(
			'section' => 'general',
			'title'   => __( 'Sort Order'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'DESC',
			'choices' => array (
							'DESC' => 'Descending  (default)',
							'ASC' => 'Ascending',
					)
		);

		$this->settings['sort_applies'] = array(
			'section' => 'general',
			'title'   => __( 'Sort Applied To'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'all',
			'choices' => array (
							'all' => 'All Items',
							'front' => 'Front Page Only',
							'cat' => 'Categories Only',
							'tag' => 'Tags Only',
							'tagcat' => 'Categories and Tags'
					)
		);

		$this->settings['form_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Writer Form Settings',
			'std'    => 'Options for the form where visitors compose their writing.',
			'type'    => 'heading'
		);



		$this->settings['def_text'] = array(
			'title'   => __( 'Default Writing Prompt' ),
			'desc'    => __( 'The default content that will appear in a new blank editor.' ),
			'std'     => '<h1>Introduction</h1>

This is what I have to say, which of course is something <em>really</em> important.

Edit this to be more appropriate for your onw site as sample starting content.',
			'type'    => 'richtextarea',
			'section' => 'general'
		);

		$this->settings['min_words'] = array(
			'title'   => __( 'Mininum Number of Words' ),
			'desc'    => __( 'Require this number of words written in an item' ),
			'std'     => 10,
			'type'    => 'text',
			'section' => 'general'
		);

		$this->settings['use_header_image'] = array(
			'section' => 'general',
			'title'   => __( 'Allow uploads of header images'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '2',
			'choices' => array (
							'0' => 'No, do not use uploaded images',
							'1' => 'Yes, but make it optional',
							'2' => 'Yes, and make it required'
					)
		);


		$this->settings['defheaderimg'] = array(
			'title'   => __( 'Default Header Image' ),
			'desc'    => __( 'Used on articles as a default. Be sure to enter a default caption in the upload.' ),
			'std'     => '0',
			'type'    => 'medialoader',
			'section' => 'general'
		);

		$this->settings['upload_max'] = array(
			'title'   => __( 'Maximum Upload File Size' ),
			'desc'    => __( 'Set limit for file uploads in Mb (maximum possible for this site is ' . $max_upload_size . ' Mb).' ),
			'std'     => $max_upload_size,
			'type'    => 'text',
			'section' => 'general'
		);


		$this->settings['use_header_image_caption'] = array(
			'section' => 'general',
			'title'   => __( 'Use caption field for uploaded header images'),
			'desc'    => 'Mostly used for providing attribution for images',
			'type'    => 'radio',
			'std'     => '2',
			'choices' => array (
							'0' => 'No, do not use image captions',
							'1' => 'Yes, but make it optional',
							'2' => 'Yes, and make it required'
					)
		);

		$this->settings['show_cats'] = array(
			'section' => 'general',
			'title'   => __( 'Use categories as options for submission or only for admin use'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No, do not use categories',
							'1' => 'Yes, options on share form and display on single item',
							'2' => 'Yes, but used only by admin to organize (not on writing form)'
					)
		);


		// ---- Build array to hold options for select, an array of post categories that are children of "Published"
		$all_cats = get_categories('hide_empty=0&parent=' . get_cat_ID( 'Published' ) );

		$cat_options = array();

		// Walk those cats, store as array index=ID
		foreach ( $all_cats as $item ) {
			$cat_options[$item->term_id] =  $item->name;
		}

		$this->settings['def_cat'] = array(
			'section' => 'general',
			'title'   => __( 'Set the default category for new items (choose from child categories of "Published").'),
			'desc'    => '<a href="' . admin_url( 'edit-tags.php?taxonomy=category') . '">Edit categories now</a>.',
			'type'    => 'select',
			'std'     => get_option('default_category'),
			'choices' => $cat_options
		);


		$this->settings['show_tags'] = array(
			'section' => 'general',
			'title'   => __( 'Show/use tags?'),
			'desc'    => 'Use tags as options for submission or only for admin use',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No, do not use tags',
							'1' => 'Yes, options on share form and display on single item',
							'2' => 'Yes, but used only by admin to organize (not on writing form)'

					)
		);

		$this->settings['show_email'] = array(
			'section' => 'general',
			'title'   => __( 'Enable email address field.'),
			'desc'    => ' Setting to <strong>No</strong> will remove this feature from being available on published items and remove option for selecting notification of comments.',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes, but make it optional',
							'2' => 'Yes, and make it required'
					)
		);

		$this->settings['email_domains'] = array(
			'title'   => __( 'Limit email addresses to domain(s).' ),
			'desc'    => __( 'Seperate multiple domains by commas' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);

		$this->settings['comment_notification'] = array(
			'section' => 'general',
			'title'   => __( 'Show option for comment notification.'),
			'desc'    => ' Setting to <strong>Yes</strong> will provide a check box option for authors to receive notification of comments on their writing (only effective if comments enabled in the <strong>Published</strong> section).',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);


		$this->settings['require_extra_info'] = array(
			'section' => 'general',
			'title'   => __( 'Require extra information field filled in?'),
			'desc'    => 'Use this to enable and/or require additional information entered in the web form that only site admins can view.',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes',
							'-1' => 'Hide this field',
					)
		);


		$this->settings['show_footer'] = array(
			'section' => 'general',
			'title'   => __( 'Show the footer entry field on the writing form?'),
			'desc'    => 'A field to add extra information like an original source or credit; this gets published at the bottom of the published item.',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);

		$this->settings['twitter_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Twitter Settings',
			'std'    => '',
			'type'    => 'heading'
		);

		$this->settings['show_tweet_button'] = array(
			'section' => 'general',
			'title'   => __( 'Show a Tweet This button on published items?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);


		$this->settings['hashtags'] = array(
			'title'   => __( 'Twitter Button Hashtag(s)' ),
			'desc'    => __( 'When a writing is tweeted add these hashtags. Do not include # and separate multiple hashtags with commas.' ),
			'std'     => 'splotwriter',
			'type'    => 'text',
			'section' => 'general'
		);

		$this->settings['admin_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Admin Settings',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['notify'] = array(
			'title'   => __( 'Notification Emails' ),
			'desc'    => __( 'Send notifications to these addresses (separate multiple wth commas). They must have an Editor Role on this site to be able to moderate' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);



		$this->settings['readingtimecheck'] = array(
		'section' => 'general',
		'title' 	=> '' ,// Not used for headings.
		'desc'   => 'Estimated Reading Time Plugin',
		'std'    =>  reading_time_check(),
		'type'    => 'heading'
		);

		// ------- creative commons options
		$this->settings['cc_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Creative Commons / Rights',
			'std'    => '',
			'type'    => 'heading'
		);

		$this->settings['use_cc'] = array(
			'section' => 'general',
			'title'   => __( 'Usage Mode' ),
			'desc'    => __( 'How licenses are applied' ),
			'type'    => 'radio',
			'std'     => 'none',
			'choices' => array(
				'none' => 'No Creative Commons',
				'site' => 'Apply the same license to all writings',
				'user' => 'Enable authors to choose a license'
			)
		);

		$this->settings['cc_site'] = array(
			'section' => 'general',
			'title'   => __( 'Rights for All Writings'),
			'desc'    => __( 'Choose an option that will appear sitewide or used as default if user selects.' ),
			'type'    => 'select',
			'std'     => 'by',
			'choices' => truwriter_get_licences()
		);

		/* Reset
		===========================================*/

		$this->settings['reset_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'With Great Power Comes...',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['reset_theme'] = array(
			'section' => 'general',
			'title'   => __( 'Reset All Options' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);
	}

	public function display_general() {
		// section heading for general setttings

		echo '<p>These settings manage the behavior and appearance of your TRU Writer site. See the <a href="https://github.com/cogdog/truwriter" target="_blank">the documentation at the theme source on GitHub</a> (a new SPLOT documentation site is in development) .</p><p>If this kind of stuff has any value to you, please consider supporting me so I can do more!</p><p style="text-align:center"><a href="https://patreon.com/cogdog" target="_blank"><img src="https://cogdog.github.io/images/badge-patreon.png" alt="donate on patreon"></a> &nbsp; <a href="https://paypal.me/cogdog" target="_blank"><img src="https://cogdog.github.io/images/badge-paypal.png" alt="donate on paypal"></a></p> ';
	}


	public function display_reset() {
		// section heading for reset section setttings
	}
	/* HTML output for individual settings */
	public function display_setting( $args = array() ) {

		extract( $args );

		$options = get_option( 'truwriter_options' );

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$options['new_types'] = 'New Type Name'; // always reset

		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;


		switch ( $type ) {

			case 'heading':
				echo '<tr><td colspan="2" class="alternate"><h3>' . $desc . '</h3><p>' . $std . '</p></td></tr>';
				break;

			case 'checkbox':

				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="truwriter_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';

				break;

			case 'select':
				echo '<select class="select' . $field_class . '" name="truwriter_options[' . $id . ']">';

				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';

				echo '</select>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="truwriter_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
								if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';

				break;

			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="truwriter_options[' . $id . ']" placeholder="' . $std . '" rows="10" cols="80">' . format_for_editor( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'richtextarea':


				// set up for inserting the WP post editor
				$rich_settings = array( 'textarea_name' => 'truwriter_options[' . $id . ']' , 'editor_height' => '200',  'tabindex'  => "3", 'editor_class' => $field_class );

				$textdefault = (isset( $options[$id] ) ) ? $options[$id] : $std;

				wp_editor(   $textdefault , $id , $rich_settings );

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'medialoader':
				echo '<div id="uploader_' . $id . '">';

				if ( $options[$id] )  {
					$front_img = wp_get_attachment_image_src( $options[$id], 'large' );
					echo '<img id="previewimage_' . $id . '" src="' . $front_img[0] . '" alt="default thumbnail" />';
				} else {
					echo '<img id="previewimage_' . $id . '" src="' . get_stylesheet_directory_uri() . '/images/default-header-640.jpg" alt="default header image" />';
				}

				echo '<input type="hidden" name="truwriter_options[' . $id . ']" id="' . $id . '" value="' . $options[$id]  . '" />
  <br /><input type="button" class="upload_image_button button-primary" name="_truwriter_button' . $id .'" id="_truwriter_button' . $id .'" data-options_id="' . $id  . '" data-uploader_title="Set Default Header Image" data-uploader_button_text="Select Image" value="Set/Change Image" />
</div><!-- uploader -->';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="truwriter_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" /> <input type="button" id="showHide" value="Show" /> ';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'text':
			default:
				echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="truwriter_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

				if ( $desc != '' ) {

					if ($id == 'def_thumb') $desc .= '<br /><a href="' . $options[$id] . '" target="_blank"><img src="' . $options[$id] . '" style="overflow: hidden;" width="' . $options["index_thumb_w"] . '"></a>';
					echo '<br /><span class="description">' . $desc . '</span>';
				}

				break;
		}
	}

	/* export settings as json */
	// ------h/t https://pippinsplugins.com/building-settings-import-export-feature/
	public function export_settings() {

		if ( empty( $_POST['splot_action'] ) || 'export_settings' != $_POST['splot_action'] )
			return;

		if ( ! wp_verify_nonce( $_POST['splot_settings_export_nonce'], 'splot_settings_export_nonce' ) )
			return;

		// grab options
		$options = get_option( 'truwriter_options' );

		// if the default header image is set, replace it with it's URL
		if ( $options["defheaderimg"] ) $options["defheaderimg"] = wp_get_attachment_url($options["defheaderimg"]);

		// add a key name to validate on import
		$options["splotname"] = "truwriter";

		// start json output to download
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=truwriter-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );
		echo json_encode( $options );
		exit;

	}

	/* import settings as json */
	public function import_settings() {

		if ( empty( $_POST['splot_action'] ) || 'import_settings' != $_POST['splot_action'] )
			return;

		if ( ! wp_verify_nonce( $_POST['splot_settings_import_nonce'], 'splot_settings_import_nonce' ) )
			return;

		// check uploaded file name for .json
		$extension = end( explode( '.', $_FILES['import_settings']['name'] ) );

		if( $extension != 'json' ) {
			wp_die( __( 'Please upload a valid .json file' ) );
		}

		$import_file = $_FILES['import_settings']['tmp_name'];

		if( empty( $import_file ) ) {
			wp_die( __( 'Please upload a file to import' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ) );

		if (!$settings["splotname"] or $settings["splotname"] != "truwriter") {
			wp_die( __( 'This does not appear to be a TRU Writer export file. Missing splotname.' ) );
		}

		//  discard the validation key
		unset($settings["splotname"]);

		// for header image, import to media library and replace with id
		if ( $settings["defheaderimg"] ) {
			$settings["defheaderimg"] = media_sideload_image( $settings["defheaderimg"], null, null, 'id');
		}

		// grab existing options
		$curr_options = get_option( 'truwriter_options' );

		// page ids will not match, so use the one already set
		$settings["write_page"] = $curr_options["write_page"];

		// if we have a default category, keep it
		if ($curr_options["def_cat"]) $settings["def_cat"] = $curr_options["def_cat"];


		// update the options with new settings
		update_option( 'truwriter_options', $settings );

		// return to options
		wp_safe_redirect( admin_url( 'https://splot.test/wp-admin/themes.php?page=truwriter-options&settings-imported=1' ) ); exit;

}


	/* Initialize settings to their default values */
	public function initialize_settings() {

		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}

		update_option( 'truwriter_options', $default_settings );

	}


	/* Register settings via the WP Settings API */
	public function register_settings() {

		register_setting( 'truwriter_options', 'truwriter_options', array ( &$this, 'validate_settings' ) );

		foreach ( $this->sections as $slug => $title ) {
			add_settings_section( $slug, $title, array( &$this, $this->section_callbacks[$slug] ), 'truwriter-options' );
		}

		$this->get_settings();

		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}

	}


	/* tool to create settings fields */
	public function create_setting( $args = array() ) {

		$defaults = array(
			'id'      => 'default_field',
			'title'   => 'Default Field',
			'desc'    => 'This is a default description.',
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);

		extract( wp_parse_args( $args, $defaults ) );

		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);

		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;


		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'truwriter-options', $section, $field_args );

	}


	public function validate_settings( $input ) {
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'truwriter_options' );

			if ( $input['notify'] != $options['notify'] ) {
				$input['notify'] = str_replace(' ', '', $input['notify']);
			}

			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}

			// make sure the max file upload is integer and less than max possible
			$max_upload_size = round(wp_max_upload_size() / 1000000);
			$input['upload_max'] = min( intval( $input['upload_max'] ), $max_upload_size  );


			return $input;
		}

		return false;


	}
 }

$theme_options = new truwriter_Theme_Options();

function truwriter_option( $option ) {
	$options = get_option( 'truwriter_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}
?>
