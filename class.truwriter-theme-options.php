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
		$this->settings = array();
		
		$this->get_settings();
		
		$this->sections['general'] = __( 'General Settings' );

		// create a colllection of callbacks for each section heading
		foreach ( $this->sections as $slug => $title ) {
			$this->section_callbacks[$slug] = 'display_' . $slug;
		}

		// enqueue scripts for media uploader
        add_action( 'admin_enqueue_scripts', 'truwriter_enqueue_options_scripts' );
		
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'truwriter_options' ) )
			$this->initialize_settings();
	}

	/* Add page(s) to the admin menu */
	public function add_pages() {
		$admin_page = add_theme_page( 'TRU Writer Options', 'TRU Writer Options', 'manage_options', 'truwriter-options', array( &$this, 'display_page' ) );
		
		// documents page, but don't add to menu		
		$docs_page = add_theme_page( 'TRU Writer Documentation', '', 'manage_options', 'truwriter-docs', array( &$this, 'display_docs' ) );
		
	}

	/* HTML to display the theme options page */
	public function display_page() {
		echo '<div class="wrap">
		<h1>TRU Writer Options</h1>';
		
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';
				
		echo '<form action="options.php" method="post" enctype="multipart/form-data">';

		settings_fields( 'truwriter_options' );
			
		echo  '<h2 class="nav-tab-wrapper"><a class="nav-tab nav-tab-active" href="?page=truwriter-options">Settings</a>
		<a class="nav-tab" href="?page=truwriter-docs">Documentation</a></h2>';

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
			
	/*  display documentation in a tab */
	public function display_docs() {	
		// This displays on the "Documentation" tab. 
		
	 	echo '<div class="wrap">
		<h1>TRU Writer Documentation</h1>
		<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="?page=truwriter-options">Settings</a>
		<a class="nav-tab nav-tab-active" href="?page=truwriter-docs">Documentation</a></h2>';
		
		include( get_stylesheet_directory() . '/includes/truwriter-theme-options-docs.php');
		
		echo '</div>';		
	}


	/* Define all settings and their defaults */
	public function get_settings() {
	
		/* General Settings
		===========================================*/


		$this->settings['access_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Access to Writing Tool',
			'std'    => '',
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
			'desc'    => __( 'Suggestion if someone cannot guess the code. Not super secure, but hey.' ),
			'std'     => 'Name of this site (lower the case, Ace!)',
			'type'    => 'text',
			'section' => 'general'
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
				'pending' => 'Moderated: new submissions will not appear until an admin edits status in Wordpress',
				'publish' => 'Published immediately to site',
			)
		);		

		$this->settings['def_text'] = array(
			'title'   => __( 'Default Writing Prompt' ),
			'desc'    => __( 'Contents that will appear in a new blank writing spot; it can include HTML (sorry not rich text editor here yet)' ),
			'std'     => '<h2>Introduction</h2><p>This is what I have to say, which of course is something important. As you can see we can use headings and other <strong>formatting</strong> in our work.</p>',
			'type'    => 'textarea',
			'section' => 'general'
		);
		
		$this->settings['allow_comments'] = array(
			'section' => 'general',
			'title'   => __( 'Allow Comments?' ),
			'desc'    => __( 'Enable comments on published writings.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		$this->settings['require_extra_info'] = array(
			'section' => 'general',
			'title'   => __( 'Require Extra Information Field Filled In?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes',
							'-1' => 'Hide this field',
					)
		);
			
		$this->settings['defheaderimg'] = array(
			'title'   => __( 'Default Header Image' ),
			'desc'    => __( 'Used on articles as a default. Be sure to enter a default caption in the upload.' ),
			'std'     => '0',
			'type'    => 'medialoader',
			'section' => 'general'
		);
		
		
		$this->settings['show_cats'] = array(
			'section' => 'general',
			'title'   => __( 'Show the categories menu on writing form and display'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
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
			'title'   => __( 'Default Category for New Writing'),
			'desc'    => '',
			'type'    => 'select',
			'std'     => get_option('default_category'),
			'choices' => $cat_options
		);
		
		
		$this->settings['show_tags'] = array(
			'section' => 'general',
			'title'   => __( 'Show the tags entry on writing form and single items displays?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);
			
		$this->settings['show_footer'] = array(
			'section' => 'general',
			'title'   => __( 'Show the footer entry field on the writing form?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);

			
		$this->settings['notify'] = array(
			'title'   => __( 'Notification Emails' ),
			'desc'    => __( 'Send notifications to these addresses (separate multiple wth commas). They must have an Editor Role on this site to be able to moderate' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
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
		
		$this->settings['readingtimecheck'] = array(
		'section' => 'general',
		'title' 	=> '' ,// Not used for headings.
		'desc'   => 'Estimated Reading Time Plugin', 
		'std'    =>  reading_time_check(),
		'type'    => 'heading'
		);		

		$this->settings['authorcheck'] = array(
		'section' => 'general',
		'title' 	=> '' ,// Not used for headings.
		'desc'   => 'Author Account Setup', 
		'std'    =>  truwriter_author_user_check( 'writer' ),
		'type'    => 'heading'
		);		
		
		// ------- creative commons options		
		$this->settings['cc_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Creative Commons',
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
			'title'   => __( 'License for All Writings'),
			'desc'    => __( 'Choose a license that will appear sitewide or used as default if user selects.' ),
			'type'    => 'select',
			'std'     => 'by',
			'choices' => array(
				'0' =>'CC0 Public Domain',
				'by' => 'CC BY Attribution',
				'by-sa' => 'CC Attribution-ShareAlike',
				'by-nd' => 'CC BY-ND Attribution-NoDerivs',
				'by-nc' => 'CC BY-NC Attribution-NonCommercial',
				'by-nc-sa' => 'CC BY-NC-SA	Attribution-NonCommercial-ShareAlike',
				'by-nc-nd' => 'CC BY-NC-ND Attribution-NonCommercial-NoDerivs',
			)
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
	
		echo '<p>These settings manaage the behavior and appearance of your TRU Writer site. There are quite a few of them!</p>';		
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
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span><br /><br />';
					
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="truwriter_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				break;

			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="truwriter_options[' . $id . ']" placeholder="' . $std . '" rows="10" cols="80">' . format_for_editor( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;
				
			case 'medialoader':
				echo '<div id="uploader_' . $id . '">';

				if ( $options[$id] )  {
					$front_img = wp_get_attachment_image_src( $options[$id], 'radcliffe' );
					echo '<img id="previewimage_' . $id . '" src="' . $front_img[0] . '" width="640" height="300" alt="default thumbnail" />';
				} else {
					echo '<img id="previewimage_' . $id . '" src="https://placehold.it/640x300" alt="default header image" />';
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