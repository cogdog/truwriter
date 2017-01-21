<p>These instructions are a reference for the settings within the TRU Writer; if you are reading this, you got as far as installing and activating the theme. For extra fun "writer" here in lower case refers to the person using your site.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/writings-menu.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p> In this theme Wordpress <code>Posts</code> are renamed <code>Writings</code> but have all the attributes of garden variety blog posts.</p>


<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/options-1.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />


<h2>Access Code and Hint</h2>
<p>Leave this field blank if you want any visitor to be able to access the <a href="<?php echo site_url(); ?>/write">writing form on this site</a> (you can always make it less accessible by not having any links are menus for the form page. </p>

<p>If you want to provide an access code (a very weak password), just enter it. Any requests to access to form will be sent to the <a href="<?php echo site_url(); ?>/desk">front desk</a> form which a writer must enter in this case <code>Lassie</code> to see the form.</p>

<p>Enter an <strong>Access Hint</strong> that will be displayed if someone does not enter the correct code.</p>

<h2>Status for New Writings</h2>
<p>Moderated means what it sounds like, when a writer submits something, it is not publicly visible; these are set as drafts with a  <a href="<?php echo admin_url( 'edit.php?post_status=pending&post_type=post')?>">Pending Approval</a> status. Notifications of these submissions are sent to the email addresses entered below. An Editor or Administrator must edit the draft to have a Published status to make it publicly available.</p>

<p>Other sites may wish to give visitors the ability to publish directly.</p>

<h2>Default Writing Prompt</h2>
<p>Enter this field to pre-populate the textarea that visitors write in. You can provide a prompt or set up a structure. HYTML is acceptable, but for now, there is no rich text editor here. Learn some web code!</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/options-2.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Allow Comments</h2>
<p>Check this box to add a standard blog comment field at the bottom of all published pieces.</p>

<h2>Extra Information Field</h2>
<p>On the writing form this is a place for someone to add some information that is not part of the final published item. If you wish to be specific, enter the prompt for this field like <em>Include your name and course section.</em></p>

<p>These end up in a <strong>Custom Field</strong> named <code>wEditorNotes</code> you can view when editing the post for the item (you have to to open the Screen Options toggle at the top of the Wordpress interface and click the box to make the Custom Fields available. The information is also included in the admin notification emails.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/options-3.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Default Header Image</h2>
<p>Click <strong>Set/Change Image</strong> to open the Wordpress media editor. Drop an image that you wish to be used as a default if a writer does not include one of their own. The <code>640 x 300</code> dimensions are a minimum size, and represent a reasonable aspect ratio for a header image. Larger is better; and the image will be cropped along the middle of the image.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/header-image-caption.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>Before choosing the image, be sire to add a caption (we suggest a creative commons license!) as this is what is used to display atop the image when published. Attribute and model attribution!</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/header-image-selected.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>Once selected you will see a preview of your default image. Isn't Cadu a nice looking dog?</p>


<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/options-4.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Default Category for New Writing</h2>
<p>If you have not set up any categories, this menu will not do much. You might want to save your options, and edit your <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=category')?>">Writing Categories</a>. On activation, the TRU Writer will pre-create two categories for you.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/categories.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>The <code>In Progress</code> category is where all submitted writings go if they need approval. Final Published items have their own ... <code>Published</code> category (big surprise there, eh?). If you want to give your writers a choice of categories to place their work, make sure any categories you create have <code>Published</code> as a parent.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/categories-published.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>So we suggest making a few pre-set categories for a new site. As an Administrator you can always add new ones at any time. Any text you add as a description will be included on the writing form.</p>

<h2>Notification Emails</h2>
<p>Enter any email addresses who should be notified if new submissions; you have multiple ones if you separate them by a comma.</p>


<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/twitter-button-hashtags.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Tweeted hashtags</h2>
Enter one or more hashtags to be used when a published item is shared via the Tweet This button. Do not include "#" and separate multiple ones with commas



<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/est-reading-time.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Estimated Reading Time Plugin</h2>
<p>This plugin is optional, if installed and activated, it will add to all published works an estimate of the reading time based on a crude formula. If it is not installed, you will see the notes shown above. This plugin is available in the Wordpress repository, so it is an easy install.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/reading-times-settings.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>If installed, you should <a href="<?php echo admin_url( 'options-general.php?page=eprtoptions')?>">check the settings</a> under <strong>Settings</strong> -&gt; <strong>Post Reading Time</strong>. Set <code>Show in Home Page</code> and <code>Show in Archives</code> to display in listings of published works.</p>


<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/author-account-none.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Author Account Setup</h2>
<p>To provide access to the media uploader, this site uses a Wordpress Authoring Role account that is logged into invisibly to your site visitors (for anyone logged in with an Editor or Administrator account, like you this account is not used.). So your site needs an active user with a name of <strong>writer</strong> and a role of <strong>Author</strong>.</p>


<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/add-author.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>You can follow the link to create an account; for an email you can use a fictitious one on your domain. We suggest using the strong password that Wordpress now suggests. Copy that password, and perhaps save it in a safe place. On a stand along site, you can just paste it into the password field.</p>

<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/add-to-site.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>If this site is on a mulitsite Wordpress, and the TRU Writer has been used on another site, the writer account already exists, so you need to add it to the site via the Author tools. However, you still have to enter the password, so make sure you know the passord that was used on another site. If you do not have access to it, you will have to reset the password at the Network Admin level, and then update the password on the options of all sites using the TRU Writer.</p>


<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/show-password.gif" alt="" style="border:3px solid #000; margin-top:4em;" />

<p>You can now see the password expected for the secret account.</p>




<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/creative-commons.jpg" alt="" style="border:3px solid #000; margin-top:4em;" />

<h2>Creative Commons Settings</h2>


<p>Creative commons licenses can be attached to all works published on your site. Choose <strong>Apply one license to all challenges</strong> to place the same license on all works (a notice will be displayed on the writing form).</p>

<p>Or you can the Creative Commons options to <strong>Enable users to choose license</strong> which will put the menu on the submission form so users can choose a license (or set to All Rights Reserved). At this time, the only way to edit the licenses displayed (e.g. if you do not want certain ones) is to edit <code>functions.php</code> in the template directory. Look for the function <code>function cc_license_select_options</code> and comment out the lines containing license options to hide.</p>

