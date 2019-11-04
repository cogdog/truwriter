  <style>
      code{white-space: pre-wrap;}
      span.smallcaps{font-variant: small-caps;}
      span.underline{text-decoration: underline;}
      div.column{display: inline-block; vertical-align: top; width: 50%;}
      img {max-width:90%; }
  </style>



<p><em>For complete setup documentation that includes suggestions for setup, plugins, <a href="https://github.com/cogdog//truwriter"  target="_blank">see the theme repository on GitHub</a>. That is also <a href="https://github.com/cogdog//truwriter/issues"  target="_blank">a good place to ask question or toss accolades</a>.</em></p>

 


  <style>
      code{white-space: pre-wrap;}
      span.smallcaps{font-variant: small-caps;}
      span.underline{text-decoration: underline;}
      div.column{display: inline-block; vertical-align: top; width: 50%;}
  </style>

<h2 id="setting-up-tru-writer">Setting Up TRU Writer</h2>
<p>Now that you’ve installed TRU Writer and can see the barebones theme staring back at you. It is not very interesting. Yet.</p>
<h3 id="recommended-plugins-for-tru-writer">Recommended Plugins for TRU Writer</h3>
<p>While your doing all that uploading, you should know that this theme uses the <a href="https://wordpress.org/plugins/reading-time-wp/">Reading Time WP plugin</a> to insert those commonplace estimates of reading time – blame <a href="http://www.medium.com">Medium</a> if you hate knowing how long a post might take to read.</p>
<h2 id="demo-content">Demo Content</h2>
<p>If you want a site that is not completely empty, you can get one with the content set up on the <a href="https://lab.cogdogblog.com/writer">public demo site</a>.</p>
<p>Install all content by <a href="https://github.com/cogdog/truwriter/blob/master/data/truwriter.xml">downloading the WordPress export for that site</a>. Running the WordPress Importer (under <strong>Tools</strong> – <strong>Import</strong>) and upload that file when prompted.</p>
<p>You can also get a copy of the Widgets used on that site too. First intall/activate the <a href="https://wordpress.org/plugins/widget-importer-exporter/">Widget Importer &amp; Exporter plugin</a>. Download the <a href="https://github.com/cogdog/truwriter/blob/master/data/writer-widgets.wie">Writer Widgets data file</a>. Look under the <strong>Tools</strong> menu for <strong>[Widget Importer &amp; Exporter</strong> and use the Import Widgets section to upload the data file. Boom! You got Widgets.</p>
<h3 id="page-setup">Page Setup</h3>
<p>This theme has one special page for your writing form that must be created; associated with a specific template that provides it’s functionality. Activating the theme <em>should</em> create the page for you when the theme is activated, but if not, create them as described below. You can edit the content of the <strong>Write</strong> page to customize the welcome seen by writers on your site.</p>
<p>If the theme does not do so automatically (and it should) create this Wordpress <strong>Page</strong>. As of version 1.8, you can edit the url short name as well.</p>
<ul>
<li><strong>Write</strong> – The page that provides the writing form, see <a href="http://splot.ca/writer/write">http://splot.ca/writer/write</a>. Whatever you include in the body (not required) is added to the top of the form, maybe for extra instructions.e.g. for a site at <code>http://coolest.site.org/</code> the page can be published at <code>http://coolest.site.org/writing</code> When you create a Writing Form page, under <strong>Page Atributes</strong>, select the Template named <code>Writing Pad</code></li>
</ul>
<h3 id="customize-your-menus">Customize Your Menus</h3>
<p>The default menus are not what you want Wordpress will generate one based on all Pages set up.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/default-menus.jpg" /></p>
<p>In Wordpress Dashboard go to Appearance -&gt; Menus. Create a new menu, and check the location box for “Primary”. A typical TRu Writer menu might have an “About Page”, the Write page (the form for writing), maybe a Random link (your site URL followed by <code>/random</code>), and a set of dropdowns to see pages by category. Here is one example for the site http://splot.ca/Writer</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/writer-menus.jpg" /></p>
<p>You can of course, create any menu structure that works for your site- but don’t use the default! And hey, if you’re being all fancy and don’t check the “Primary” box above, remember to go into the “Manage Menus” tab and change it from the default menu to the new awesome menu you just created or all you’ll see is the default menu popping up again and again and that will be frustrating.</p>
<h3 id="theme-options-and-settings">Theme Options and Settings</h3>
<p>Upon activation the theme will set up a <strong>TRU Writer Options</strong> link that appears in the black admin bar at the top of your Wordpress Dashboard interface (when logged in), and in the “Appearance” tab on your Wordpress Dashboard.</p>
<p>Click <strong>TRU Writer Options</strong> to see or change the settings outlined below.</p>
<p>In the TRU Writer Theme, traditional “posts” in Wordpress are renamed “writings,” but they still have all the attributes of garden variety Wordpress blog posts. Yum.</p>
<h4 id="access-options">Access Options</h4>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/access-options.jpg" /></p>
<p>Leave this field blank if you want any visitor to be able to access the writing form on your TRU Writer site. If that’s too open for you, add a code and give it a hint. If you want a quick workaround here, make the site less accessible by not having any links in the homepage menu bar that go to the editor page–no link to the “writer” page, the harder it is to find the editor and post something.</p>
<p>If you want to require users to enter an access code (a very weak password), just enter it in the space provided. Any requests to access the editor and write a post will be sent to a front-page where a writer must enter the passcode. As an example, we have “Lassie” and an equally difficult hint. If you enter in “Lassie,” you can see the editor and begin the process of writing a post–but don’t actually do that; we haven’t set everything up yet! This is a hypothetical situation. For now, decide if you want a passcode. If you do, put it in, with a hint. If you don’t, moving on!</p>
<p>The TRU Writer is a moderated publishing theme. And “moderated” means that when a writer submits something, it is not immediately visible to the world. Instead, unless you set it otherwise (more on that below), when a writer submits a post (or “writing” as above) they are automatically set as drafts with a <em>Pending Approval</em> status. You’ll get notifications that a submissions has been made to the Email address you enter in the “Notifications Email” option.</p>
<p>In order to make a post (or “writing” as above) visible, a user with the permission status of “Editor” or “Administrator” needs to change the status of the “pending approval” writing to “published (or ignore an”Editor" or “Administrator” could just ignore it forever, such power is theirs).</p>
<h4 id="special-page-setup">Special Page Setup</h4>
<p>Use this area to assign the WordPress Page to be used for the writing form. With version 1.8 of TRU Writer, you can now use any name you want for the URL (previously these were forced to be <code>write</code> – this is no longer required.</p>
<p>If no Page is found with the correct template, you will see a prompt to create one. If the Page is found (and there might even be more than one, you can select the one you want used for each special page.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/special-page.jpg" /></p>
<h4 id="publish-settings">Publish Settings</h4>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/publish.jpg" /></p>
<p>You can also choose the “status for new writings” as “publish immediately” and there are no checks and balances–the writing goes right up and is publicly visible immediately. Immediate gratification goes a long way. Note though, that because of the hollow tunnel this might create, this option works best with an access code as described above in place–right Lassie!?</p>
<p>Check the <strong>Allow Comments</strong> box to add a standard blog comment field at the bottom of all published pieces.</p>
<h4 id="writing-form-settings">Writing Form Settings</h4>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/writing-form.jpg" /></p>
<p>Enter the <strong>Default Writing Prompt</strong> field to pre-populate the field that visitors write use to compose their work. You can provide a prompt or set up a structure. The editor is now full rich text enabled (including images).</p>
<p>You can also set a minimum number of words required for publishing.</p>
<p>Under <strong>Default Header Image</strong> click <strong>Set/Change Image</strong> to open the Wordpress media editor to choose a default media header image. Drop an image that you wish to be used as a default one if a writer does not include one of their own. The <code>640 x 300</code> dimensions are a minimum size, and represent a reasonable aspect ratio for a header image. Larger is better; and the image will be cropped along the middle of the image.</p>
<p>You can now also set a limit for the file size upload.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/header-image-caption.jpg" /></p>
<p>Before choosing the image, be sure to add a caption (we suggest a creative commons license!) as this is what is used to display atop the image when published. Attribute and model attribution! FTW!</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/header-image-selected.jpg" /></p>
<p>Once selected you will see a preview of your default image. Isn’t Felix a nice looking dog?</p>
<p>Note that you can set a maximum size for uploaded images.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/category-tags.jpg" /></p>
<p>Disable <strong>Show the categories menu on writing form and display</strong> if you do not want writers or readers to use/see categories.</p>
<p>If you have not set up any categories, the <strong>Default Category for New Writing</strong> menu will not do much. You might want to save your options and go edit your <strong>Writing Categories</strong>. On activation the TRU Writer will pre-create two categories it uses to organize what is submitted and published.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/categories.jpg" /></p>
<p>The <code>In Progress</code> category is where all submitted writings go if they need approval. Final Published items have their own … <code>Published</code> category (big surprise there, eh?). If you want to give your writers a choice of categories to place their work, make sure any categories you create have <code>Published</code> as a parent.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/categories-published.jpg" /></p>
<p>So we suggest making a few pre-set categories for a new site. As an Administrator you can always add new ones at any time. Any text you add as a description will be included on the writing form.</p>
<p>Disable <strong>Show the tags entry on writing form and single items displays?</strong> if you do not want writers or readers to use/see tags. Likewise use <strong>Show the footer entry field on the writing form?</strong> to use/disable the footer field writers can append to their works.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/email.jpg" /></p>
<p>Enabling the email fields creates the option for users of your site to provide an address if they wish to have a special link sent to them that allows for post-publishing edits. (or be able to request one when published).</p>
<p>As an option you can enter a comma-separated list of domains to restruct the email addresses entered (e.g. if you wish students to use an school provided email address).</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/extra-fields.jpg" /></p>
<p>The <strong>Extra Information Field</strong> represents a place for them to add a message that is not part of the final published item, but that the administrator or editor might need to know, or might have requested. Perhaps if you are using this theme to host multiple sections of a class or course of study, you might want to populate the prompt for this field with something like “Include your name and course section” or, “name the dog that best represents you”.</p>
<p>These end up in a <strong>Custom Field</strong> named “wEditorNotes,” which you can view when editing the post in order to publish it (not the “quick edit”; the full monty “edit” please). You will need to open the Screen Options toggle at the top of the Wordpress Dashboard interface and click the box that makes the Custom Fields available. The information is also included in the notification emails announcing a new writing in need of approval.</p>
<h4 id="admin-settings">Admin Settings</h4>
<p>Enter in <strong>Notification Emails</strong> any email addresses who should be notified if new submissions; you have multiple ones if you separate them by a comma. Or leave blank if you never want to know what’s going on in your site.</p>
<h4 id="twitter-settings">Twitter Settings</h4>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/twitter-settings.jpg" /></p>
<p>You can now choose to disable the Tweet This button on published items In enabled, you can enter one or more hashtags to be used when a published item is shared via the <strong>Tweet This</strong> button. Do not include “#” and separate multiple ones with commas</p>
<h4 id="estimated-reading-time-plugin">Estimated Reading Time Plugin</h4>
<p>This plugin is optional, if installed and activated, it will add to all published works an estimate of the reading time based on a crude formula. If it is not installed, you will see the notes shown above. This plugin is available in the Wordpress repository, so it is an easy install.</p>
<h4 id="creative-commons-rights-settings">Creative Commons / Rights Settings</h4>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/creative-commons.jpg" /></p>
<p>Creative Commons licenses or other usage rights (including copyright) can be attached to all works published on your site. Choose <strong>Apply one license to all challenges</strong> to place the same license on all works (a notice will be displayed on the writing form).</p>
<p>Or you can set the option to <strong>Enable users to choose license</strong> which places the same menu on the writing form so users can choose a license (or set to All Rights Reserved).</p>
<h2 id="customize-the-write-form">Customize the Write Form</h2>
<p>You can customize the field labels and the descriptions of the form where people submit new pieces of writing to a TRU Writer site. On your site navigate to the write form, and activate the Wordpress Customizer from the admin bar.</p>
<p>Look for a special section just below <strong>Site Identity</strong> to open:</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/customizer-writer-tab2.jpg" /></p>
<p>Then from this pane, open “Write Form” tab</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/customizer-writer-form-tab2.jpg" /></p>
<p>And then you will see a series of fields to edit for all form field elements.</p>
<p>For each, you can edit the title/label of the field and the prompt that appears below. As you type in the customizer fields on the left, you will see a live preview on the right.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/customizer-form-edit.jpg" /></p>
<h2 id="customize-the-content-layout">Customize the Content Layout</h2>
<p>You can also customize the content layout. On your site navigate to any content post or page, and activate the Wordpress Customizer from the admin bar. From the <strong>TRU Writer</strong> pane, open <strong>Writer Layout</strong>. Here you can choose from the Thin or Medium layout widths (Wide will only affect very large screens).</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/customizer-layout.jpg" /></p>
<h2 id="customize-heading-for-single-page-metadata">Customize Heading for Single Page Metadata</h2>
<p>A small thing, but a corny heading of “SO IT WAS WRITTEN” was hardwired into the single post template; this is now something that can be changed in the Customizer. From the <strong>TRU Writer</strong> pane, open <strong>Writer Layout</strong>.</p>
<p><img src="<?php echo get_stylesheet_directory_uri()?>/images/meta-heading.jpg" /></p>
<h2 id="some-complexish-stuff-for-nerds-who-are-awesome">Some Complexish Stuff for Nerds, Who Are Awesome</h2>
<p>If you want to customize/re-arrange the buttons and controls on the rich text editor used by writers on your site, install <a href="https://wordpress.org/plugins/tinymce-advanced/">TinyMCE Advanced</a>. There is <a href="includes/tinymce-advanced-settings.txt">a file in the theme</a> with the typical settings for this plugin I use when I set up these sites.</p>
<p>You can copy and paste from that .txt file into the import field of the settings for TinyMCE Advanced (it’s a small chunk of json).</p>

