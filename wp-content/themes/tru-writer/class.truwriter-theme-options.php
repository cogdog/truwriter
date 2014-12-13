<?php
// manages all of the theme options
// heavy lifting via http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/

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
		
		//$this->bank106_init();
		$this->get_settings();
		
		$this->sections['general'] = __( 'General Settings' );
		$this->sections['docs']        = __( 'Documentation' );
		$this->sections['reset']   = __( 'Reset to Defaults' );

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
		
		// give us javascript for this page
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		
		// and some pretty styling
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );
	}

	/* HTML to display the theme options page */
	public function display_page() {
		echo '<div class="wrap">
		<div class="icon32" id="icon-options-general"></div>
		<h2>TRU Writer Options</h2>';
		
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';
				
		echo '<form action="options.php" method="post" enctype="multipart/form-data">';

			settings_fields( 'truwriter_options' );
			echo '<div class="ui-tabs">
				<ul class="ui-tabs-nav">';

			foreach ( $this->sections as $section_slug => $section )
				echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';

			echo '</ul>';
			do_settings_sections( $_GET['page'] );

			echo '</div>
			<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>

		</form>';
		echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
			var sections = [];';
			
			foreach ( $this->sections as $section_slug => $section )
				echo "sections['$section'] = '$section_slug';";
			
			echo 'var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
			wrapped.each(function() {
				$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
			});
			$(".ui-tabs-panel").each(function(index) {
				$(this).attr("id", sections[$(this).children("h3").text()]);
				if (index > 0)
					$(this).addClass("ui-tabs-hide");
			});
			$(".ui-tabs").tabs({
				fx: { opacity: "toggle", duration: "fast" }
			});
			
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
			
			$(".wrap h3, .wrap table").show();
			
			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
			
			// Browser compatibility
			if ($.browser.mozilla) 
			         $("form").attr("autocomplete", "off");
			         
		
				//  via http://stackoverflow.com/a/14467706/2418186
	
				//  jQueryUI 1.10 and HTML5 ready
				//      http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option 
				//  Documentation
				//      http://api.jqueryui.com/tabs/#option-active
				//      http://api.jqueryui.com/tabs/#event-activate
				//      http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
				//
				//  Define friendly index name
				var index = "key";
				//  Define friendly data store name
				var dataStore = window.sessionStorage;
				//  Start magic!
				try {
					// getter: Fetch previous value
					var oldIndex = dataStore.getItem(index);
				} catch(e) {
					// getter: Always default to first tab in error state
					var oldIndex = 0;
				}
				$(".ui-tabs").tabs({
					// The zero-based index of the panel that is active (open)
					active : oldIndex,
					// Triggered after a tab has been activated
					activate : function( event, ui ){
						//  Get future value
						var newIndex = ui.newTab.parent().children().index(ui.newTab);
						//  Set future value
						dataStore.setItem( index, newIndex ) 
					}
				}); 
					 
			});
	</script>
</div>';	
	}
			
		/* Insert custom CSS */
		public function styles() {

			wp_register_style( 'truwriter-admin', get_stylesheet_directory_uri() . '/truwriter-options.css' );
			wp_enqueue_style( 'truwriter-admin' );

		}

	/* Define all settings and their defaults */
	public function get_settings() {
	
		/* General Settings
		===========================================*/


		$this->settings['accesscode'] = array(
			'title'   => __( 'Access Code' ),
			'desc'    => __( 'Set necessary code to access the writing tool; leave blank to make wide open' ),
			'std'     => 'tru writer',
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
		
		$this->settings['defheaderimg'] = array(
			'title'   => __( 'Default Header Image' ),
			'desc'    => __( 'Used on articles as a default. Be sure to enter a default caption in the upload.' ),
			'std'     => '0',
			'type'    => 'medialoader',
			'section' => 'general'
		);
			
		$this->settings['notify'] = array(
			'title'   => __( 'Notification Emails' ),
			'desc'    => __( 'Send notifications to these addresses (separate multiple wth commas). They must have an Editor Role on this site to be able to moderate' ),
			'std'     => get_option( 'admin_email' ),
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


		$this->settings['captcha_heading'] = array(
		'section' => 'general',
		'title' 	=> '' ,// Not used for headings.
		'desc'   => 'Captcha Settings', 
		'std'    => 'Not current used, but may set up in future.',
		'type'    => 'heading'
		);		

				
		$this->settings['use_captcha'] = array(
			'section' => 'general',
			'title'   => __( 'Use reCaptcha' ),
			'desc'    => __( 'Activate a google captcha for all submission forms; <a href="https://www.google.com/recaptcha/admin/create" target="_blank">get your access keys</a>' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		
		$this->settings['captcha_style'] = array(
		'section' => 'general',
		'title'   => __( 'Captcha Style' ),
		'desc'    => __( 'Visual style for captchas, see <a href="https://developers.google.com/recaptcha/docs/customization?csw=1" target="_blank">examples of styles</a>.' ),
		'type'    => 'select',
		'std'     => 'red',
		'choices' => array(
			'red' => 'Red',
			'white' => 'White',
			'blackglass' => 'Black',
			'clean' => 'Clean',
		)
	);
	
		
		$this->settings['captcha_pub'] = array(
			'title'   => __( 'reCaptcha Public Key' ),
			'desc'    => __( '' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);
		
		$this->settings['captcha_pri'] = array(
			'title'   => __( 'reCaptcha Private Key' ),
			'desc'    => __( '' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);



				

			
		/* Reset
		===========================================*/
		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset Options' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset bank options to their defaults.' )
		);

		
	}
	
	/* Description for section */
	public function display_section() {
		// code
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
				echo '</td></tr><tr valign="top"><td colspan="2"><h4 style="margin-bottom:0;">' . $desc . '</h4><p style="margin-top:0">' . $std . '</p>';
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
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="truwriter_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;
				
			case 'medialoader':
			
			
				echo '<div id="uploader_' . $id . '">';
				
				
				
				if ( $options[$id] )  {
					$front_img = wp_get_attachment_image_src( $options[$id], 'radcliffe' );
					echo '<img id="previewimage_' . $id . '" src="' . $front_img[0] . '" width="640" height="300" alt="default thumbnail" />';
				} else {
					echo '<img id="previewimage_' . $id . '" src="http://placehold.it/640x300" alt="default header image" />';
				}

				echo '<input type="hidden" name="truwriter_options[' . $id . ']" id="' . $id . '" value="' . $options[$id]  . '" />
  <br /><input type="button" class="upload_image_button button-primary" name="_truwriter_button' . $id .'" id="_truwriter_button' . $id .'" data-options_id="' . $id  . '" data-uploader_title="Set Default Header Image" data-uploader_button_text="Select Image" value="Set/Change Image" />
</div><!-- uploader -->';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="truwriter_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';

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
			


	/**
	 * Description for Docs section
	 *
	 * @since 1.0
	 */
	public function display_docs_section() {
		
		// This displays on the "Documentation" tab. 
		
		include( get_stylesheet_directory() . '/includes/truwriter-theme-options-docs.php');
		
		
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
		//register_setting( 'truwriter_options', 'truwriter_options' );

		foreach ( $this->sections as $slug => $title )
		
			if ( $slug == 'docs' ) {
				add_settings_section( $slug, $title, array( &$this, 'display_docs_section' ), 'truwriter-options' );
			} else {
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'truwriter-options' );
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
	
	
	/* jQuery Tabs */
	public function scripts() {
		wp_print_scripts( 'jquery-ui-tabs' );
	}
	
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'truwriter_options' );
					
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