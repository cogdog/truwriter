<?php
# -----------------------------------------------------------------
# Customizer Stuff
# -----------------------------------------------------------------

add_action( 'customize_register', 'truwriter_register_theme_customizer' );


function truwriter_register_theme_customizer( $wp_customize ) {
	// Create custom panel.
	$wp_customize->add_panel( 'customize_writer', array(
		'priority'       => 25,
		'theme_supports' => '',
		'title'          => __( 'TRU Writer', 'radcliffe'),
		'description'    => __( 'Customizer Stuff', 'radcliffe'),
	) );
	

	// Add section for the collect form
	$wp_customize->add_section( 'write_form' , array(
		'title'    => __('Writing Form Prompts','radcliffe'),
		'panel'    => 'customize_writer',
		'priority' => 12
	) );
	
	// Add section for display settings
	$wp_customize->add_section( 'write_colors' , array(
		'title'    => __('Form and Button Colors','radcliffe'),
		'panel'    => 'customize_writer',
		'priority' => 18
	) );



	// Add section for display settings
	$wp_customize->add_section( 'write_display' , array(
		'title'    => __('Published Layout','radcliffe'),
		'panel'    => 'customize_writer',
		'priority' => 20
	) );

	
	$wp_customize->add_setting( 'layout_width',
	   array(
		  'default' => 'thin',
		  'type' => 'theme_mod',
	   )
	);
 
	$wp_customize->add_control( 'layout_width',
	   array(
		  'label' => __( 'Display Width' ),
		  'description' => esc_html__( 'Main content width on larger screens (will not effect mobile or table screens). Thin setting has most margin.' ),
		  'section' => 'write_display',
		  'priority' => 10, // Optional. Order priority to load the control. Default: 10
		  'type' => 'radio',
		  'choices' => array( // Optional.
			 'thin' => __( 'Thin (740px maximum)' ),
			 'medium' => __( 'Medium (1040px maximum)' ),
			 'wide' => __( 'Wide (1300px maximum)' )
		  )
	   )
	);
	
	// setting for header image upload label
	$wp_customize->add_setting( 'meta_heading', array(
		 'default'           => __( 'And So It Was Written', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for header image upload  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'meta_heading',
		    array(
		        'label'    => __( 'Metadata Heading', 'radcliffe'),
		        'priority' => 30,
		        'description' => __( 'On single page views, the heading above the left side post meta data below the content' ),
		        'section'  => 'write_display',
		        'settings' => 'meta_heading',
		        'type'     => 'text'
		    )
	    )
	); 
	

// ------ color controls for form and buttons

/*
.pretty-button-final, #writerform input.pretty-button-final { background: #0971B2; }
.pretty-button-update, #writerform input.pretty-button-update { background: #00B233; }

.writenew { background-color: #8ed9f6; }
.writedraft { background-color: #D1FAB6; }
.writeoops { background-color: #fad9d7; }
*/

	// Final button  color 
	
	$btnformcolors[] = array(
		'slug'=>'button_draft', 
		'default' => '#00B233',
		'label' => 'Draft Submit Button'
	);
	
	$btnformcolors[] = array(
		'slug'=>'button_final', 
		'default' => '#0971B2',
		'label' => 'Submit Final Submit Button'
	);

	$btnformcolors[] = array(
		'slug'=>'form_bg_new', 
		'default' => '#8ed9f6',
		'label' => 'Form Background for New Entry'
	);
		
	$btnformcolors[] = array(
		'slug'=>'form_bg_draft', 
		'default' => '#D1FAB6',
		'label' => 'Form Background for Draft'
	);

	$btnformcolors[] = array(
		'slug'=>'form_bg_error', 
		'default' => '#fad9d7',
		'label' => 'Form Background for Error'
	);
	

	// add the settings and controls for each color
	foreach( $btnformcolors as $thecolor ) {
 
		// SETTINGS
		$wp_customize->add_setting(
			$thecolor['slug'], array(
				'default' => $thecolor['default'],
				'type' => 'option', 
				'capability' =>  'edit_theme_options'
			)
		);
		
		// CONTROLS
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$thecolor['slug'], 
				array('label' => $thecolor['label'], 
				'section' => 'write_colors',
				'settings' => $thecolor['slug'])
			)
		);

     } // foreach


	// Add setting for footer
	$wp_customize->add_setting( 'button_roundness', array(
		 'default'           => __( 0, 'radcliffe' ),
		 'type' => 'option', 
		  'capability' =>  'edit_theme_options'
	) );     
     
     $wp_customize->add_control( 'button_roundness', array(
		  'type' => 'range',
		  'section' => 'write_colors',
		  'label' => __( 'Button Corner Roundness' ),
		  'description' => __( 'From square to curved' ),
		  'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 5,
		  ),
		) );
		     	
	
	// Add setting for default prompt
	$wp_customize->add_setting( 'default_prompt', array(
		 'default'           => __( 'Enter the content for your writing below. You must save first and preview once before it goes into the system as a draft. After that, continue to edit, save, and preview as much as needed. Remember to click  "Publish Now" when you are ready for your work to be published. If you include your email address, we can send you a link that will allow you to make changes later. Otherwise you should finish your writing and publish it in this session.', 'radcliffe'),
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
		 'default'           => __( 'You can now edit this previously saved writing, save the changes as a draft, or if done, submit for final publishing.', 'radcliffe'),
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
		 'default'           => __( 'Use the editing area below the toolbar to write and format your writing. You can also paste formatted content here (e.g. from MS Word or Google Docs). The editing tool will do its best to preserve standard formatting--headings, bold, italic, lists, footnotes, and hypertext links.  You can also embed audio and video from many social sites simply by putting the URL of the media on a blank line (you will see them embedded in previews and when saved).', 'radcliffe'),
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
		 'default'           => __( 'Upload an image file to be used in the header for your writing. Ideally the image should be at least 1440px wide and &lt; ' . truwriter_option('upload_max' ) . ' Mb in size. Images should either be your own or one licensed for re-use; provide an attribution credit for the image in the caption field below.', 'radcliffe'),
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
		 'default'           => __( 'Caption/Credits for Header Image', 'radcliffe'),
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
		 'default'           => __( 'Rights / Resuse License', 'radcliffe'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for license  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_license',
		    array(
		        'label'    => __( 'Rights Label', 'radcliffe'),
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
		 'default'           => __( 'Choose your preferred reuse option.', 'radcliffe'),
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
		        'description' => __( 'Directions for the rights selection' ),
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


// layout settings
function truwriter_layout_width() {
	 if ( get_theme_mod( 'layout_width') != "" ) {
	 	$thewidth = ( get_theme_mod( 'layout_width') == 'wide' ) ? '' : get_theme_mod( 'layout_width'); 	
	 	echo $thewidth;
	 }	else {
	 	echo 'thin';
	 }
}



function truwriter_form_default_prompt() {
	 if ( get_theme_mod( 'default_prompt') != "" ) {
	 	return get_theme_mod( 'default_prompt');
	 }	else {
	 	return 'Enter the content for your writing below. You must save first and preview once before it goes into the system as a draft. After that, continue to edit, save, and preview as much as needed. Remember to click  "Publish Now" when you are ready for your work to be published. If you include your email address, we can send you a link that will allow you to make changes later. Otherwise you should finish your writing and publish it in this session.';
	 }
}


function truwriter_form_re_edit_prompt() {
	 if ( get_theme_mod( 're_edit_prompt') != "" ) {
	 	return get_theme_mod( 're_edit_prompt');
	 }	else {
	 	return 'You can now edit this previously saved writing, save the changes as a draft, or if done, submit for final publishing.';
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
	 	echo 'A good title is important! Create an eye-catching title for your story, one that would make a person who sees it want to stop whatever they are doing and read it.';
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
	 	echo 'Upload an image file to be used in the header for your writing. Ideally the image should be at least 1440px wide and &lt; ' . truwriter_option('upload_max' ) . ' Mb in size. Images should either be your own or one licensed for re-use; provide an attribution credit for the image in the caption field below.';
	 }
}

function truwriter_form_item_header_caption() {
	 if ( get_theme_mod( 'item_header_caption') != "" ) {
	 	echo get_theme_mod( 'item_header_caption');
	 }	else {
	 	echo 'Caption/Credits for Header Image';
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
	 	echo 'Use the editing area below the toolbar to write and format your writing. You can also paste formatted content here (e.g. from MS Word or Google Docs). The editing tool will do its best to preserve standard formatting--headings, bold, italic, lists, footnotes, and hypertext links.  You can also embed audio and video from many social sites simply by putting the URL of the media on a blank line (you will see them embedded in previews and when saved).';
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
	 	echo 'Rights / Resuse License';
	 }
}

function truwriter_form_item_license_prompt() {
	 if ( get_theme_mod( 'item_license_prompt') != "" ) {
	 	echo get_theme_mod( 'item_license_prompt');
	 }	else {
	 	echo 'Choose your preferred reuse option.';
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
	 	echo 'This information will *not* be published with your work, it is only to sent to the editor of ' . get_bloginfo('name') . '.';
	 }
}

function truwriter_metadate_heading() {
	 if ( get_theme_mod( 'meta_heading') != "" ) {
	 	echo get_theme_mod( 'meta_heading');
	 }	else {
	 	echo 'And So It Was Written';
	 }
}

// Add the Customizer set CSS
add_action( 'wp_head', 'truwriter_customize_prettify' );

function truwriter_customize_prettify() {

	// draft button color
	$button_draft_color= get_option( 'button_draft' );
	
	// final button color
	$button_final_color= get_option( 'button_final' );

	// form backgrund new entry
	$form_bg_new = get_option( 'form_bg_new' );
 
	// form backgrund new entry
	$form_bg_draft = get_option( 'form_bg_draft' );

	// form backgrund new entry
	$form_bg_error = get_option( 'form_bg_error' );
	
	// button roundness
	$button_roundness = get_option( 'button_roundness' ); 
	
	// styling! 
	?>
<style>
/* customizer set styles */
	.pretty-button-final, #writerform input.pretty-button-final { background: <?php echo $button_final_color?>; border-radius: <?php echo $button_roundness; ?>px; ?>; }
	.pretty-button-update, #writerform input.pretty-button-update { background: <?php echo $button_draft_color?>; border-radius: <?php echo $button_roundness; ?>px; }
	.writenew { background-color: <?php echo $form_bg_new; ?> }
	.writedraft { background-color: <?php echo $form_bg_draft; ?> }
	.writeoops { background-color: <?php echo $form_bg_error; ?> }
</style>	
	<?php
}

?>