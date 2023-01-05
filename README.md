# TRU Writer Wordpress Theme
by Alan Levine https://cog.dog or https://cogdogblog.com/

[![Wordpress version badge](https://img.shields.io/badge/version-3.3-green.svg)](https://github.com/cogdog/truwriter/blob/master/style.css)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

:house: TRU Writer |
[:mag: Examples](examples.md) | 
[:rocket: Installing](install.md) | 
[:book: Documentation](docs.md) | 
[:speech_balloon: Discussions](https://github.com/cogdog/truwriter/discussions)


This Wordpress Theme powers that look like [TRU Writer Demo](https://splot.ca/writer/); a site that allows people to publish writing to your site, including rich media content, without needing to login or understand the backend of Wordpress (or mess around with Block Editors).  It is one of a collection of the Things Known as SPLOTs, explore these mysterious entities at  https://splot.ca

![](images/tru-writer-i-can-writer.jpg "Sample TRU Writer site front page with image of dog and and an overlay of the writing form")

Once installed, the TRU Writer theme powers sites for generating content online using an rich text editor interface. Writers can also  cut and paste into the editor from word-processing software such as MS Word or Google Docs, carrying forward most structural formatting (here's an example of a post [created by using the copy/paste function (cmd/ctrl + V) from a Word document](http://splot.ca/writer/2014/101). 

Because it requires no login or CMS knowledge, TRU writer is great for projects where multiple people are [contributing to classroom activities](https://biol420.opened.ca/) where many individuals [can publish content on the same site](http://femedtech.net/), even as a means for [running an online journal](http://journal.arganee.world/), [anonymous exposés](http://refugeelearningstories.org/), or even [a conference proposal submission system](http://conf.owlteh.org/contributions/). 

It focuses on the *writing* rather than Wordpress (not that we do not love you, Wordpress!). The TRU Writer allows individuals to publish anonymously, or under an assumed name, or their own name, or whatever. It never requires personal information (entering email is an option that allows a writer to edit their content after publishing).

The TRU Writer Theme was developed along with  [TRU Collector](http://splot.ca/splots/the-comparator/),  [TRU Sounder](http://splot.ca/splots/tru-sounder/), [The Comparator](http://splot.ca/splots/the-comparator/), and the [Daily Blank](http://splot.ca/splots/the-daily-blank/) [while on a fellowship](http://cogdog.trubox.ca) at [Thompson Rivers University](http://tru.ca/)--that's why the "TRU" in the theme name.

For more info, see

* [TRU Writer](https://splot.ca/splots/tru-writer/) The home of SPLOTs (splot.ca)
* [Overly detailed Blog Posts About TRU Collector](https://cogdogblog.com/tag/truwriter/) (cogdogblog.com)
* [Talk About TRU Writer](https://github.com/cogdog/truwriter/discussions) (Github Discussions)


## So You are Interested a TRU Writer Site?

Excellent!

For inspiration I offer [a collection of other sites](examples.md) using this theme, then provide  [details on how to install it](install.md), and once set up, the [documentation](docs.md) for customizing it in WordPress and updating the theme when needed. 

This same [documentation](docs.md), always the latest version, is available inside your own WordPress site under the **TRU Writer Options** menu. Or you can see documentation as well more readable format - [see the Docs!](https://docsify-this.net/?basePath=https://raw.githubusercontent.com/cogdog/tru-collector/master&homepage=docs.md&sidebar=true#/) (all of this thanks to [Docsify This](https://docsify-this.net/)).

## With Thanks

SPLOTs have no venture capital backers, no IPOs, no real funding at all. But they have been helped along by a few groups worth recognizing with an icon and a link.

The original TRU Writer was developed under a [Thompson Rivers University Open Learning Fellowship](http://cogdog.trubox.ca/) and further development was supported in part by a [Reclaim Hosting Fellowship](http://reclaimhosting.com), an [OpenETC grant](https://opened.ca), Coventry University's [Disruptive Media Learning Lab](https://dmll.org.uk/),  plus  ongoing support by [Patreon patrons](https://patreon.com/cogdog).

[![Thompson Rivers University](https://cogdog.github.io/images/tru.jpg)](https://tru.ca) [![Reclaim Hosting](https://cogdog.github.io/images/reclaim.jpg)](https://reclaimhosting.com) [![OpenETC](https://cogdog.github.io/images/openetc.jpg)](https://opened.ca) [![Disruptive Media Learning Lab](https://cogdog.github.io/images/dmll.jpg)](https://dmll.org.uk/)   [![Supporters on Patreon](https://cogdog.github.io/images/patreon.jpg)](https://patreon.com/cogdog) 

*If this kind of stuff has any value to you, please consider supporting me so I can do more!*

[![Support me on Patreon](http://cogdog.github.io/images/badge-patreon.png)](https://patreon.com/cogdog) [![Support me on via PayPal](http://cogdog.github.io/images/badge-paypal.png)](https://paypal.me/cogdog)

## Relatively Cool New Features & Updates

* **Author Features** and **Embedded Docs** The Wordpress dashboard lists all writings with the name entered by your authors, so you can easily see posts by the same person. And there is now a public link to do the same, linked from the author's name at the bootom of a written piece. Inside the theme options, you will be able to see dynamically the same documentation available here.

* **Tag List** Shortcode provides a way to list all tags used, set the sorting, and optionally limit number of tags shown

* **Sort Options** TRU Writer Options provides settings to change the sorting of published items to be by date or alphabetical, and can be ascending or descending. There are also settings to apply it only to the home page, or just to tag and/or category archives. Also updated template to properly display archive headings and descriptions (for tag and category archives)

* **Admin Only Use of Tags/Categories** Theme options can be set to let tags and categories be set only by admins for internal organization, and not shown on writing form

* **Import/Export Settings** The TRU Writer Options interface now features tab or exporting a sites SPLOT settings as a JSON file that can be imported to another site. The documentation tab is gone in lieu of a link to GitHub and mention of a future new documentation site. Hah.

* **Tag Suggestions** Tags entry field provides autocomplete suggestions (or at least is working now). 

* **Alternative Text For Header Images** Optional field added that should be used by all! Provide an alternative text for an image for better web accessibility.

* **Header Image and Caption Options** New TRU Writer options to make these fields required (default), optional, or not used. 

* **Inline Image Uploader** The writing form has a new button for inserting images in the body of text (previously done via the secret login) now all done without using the WordPress media library. Images can be selected or drag/dropped to the control, and are uploaded behind the scenes to the site. Also the Customizer now has controls for changing the colors of the Writing Form background colors and buttons, even for making the buttons *round*. Woah, Neo. Does anyone read this?

* **No More Secret User** This theme no longer requires setting up of a special authoring account, and there is no secret logging in behind the scenes. The writing form has a new drag and drop upload interface for featured image (and a new option for limiting the file size if uploads). The theme has also been simplified by not having a separate Welcome Desk Page for handing the access codes, everything is managed in the Writing Pad template.

* **Options for Special Pages**  No longer are pages for the Welcome Desk (where access codes are entered) and Writer form required to have a set URL; you can create any Page desired for these functions, and set them as the active ones via the theme options. Version 2.0 does some better set up for defaults.

* **Better Front End Editor**  Reduced reliance on special pages. The links to the random entry and the one use to get the edit link are no longer needed, and are handle now in the code. These pages should be deleted from your site. On an update to version 1.7 or later, you might have to go to **Settings** - **Permalinks** and just click save to update the url rules. Also the method for using the special link to edit an entry is now done in a single click. Much better!

* **Better Layout and Media Support** Customizer options for choosing a this or wider content layout. Media can also be uploaded by dragging and dropping files onto the editing area. Inside the code the long `functions.php` is now broken up into more manageable size includes. Small display improvements on single item views. The licensing options updated to be driven by functions, and expanded to include public domain and yikes, even copyright.

* **Options for Email Address** the form field for users entering email addresses can be hidden if not used (this as well will remove and past "request edit link" from published sites. In addition, a new admin option is added to restruct the email addresses allowed to a list of domains.

**Under the Hood** Fixed bug where choosing no comments hid the Reading Time display, changed options editor for default content to be rich text editor, enabled drag and drop media uploading for front page writing form. Also, URLs that WordPress can autoembed (e.g. YouTube, Twitter, Giphy) will now do so automatically in the editor.

* **Theme Option to Remove Tweet This Button** to enable better GDPR compliance.

* **Customize the Writing Form Instructions**  You can now modify all form field labels and descriptions / prompts for the entry fields.

* **Default Menu** On new installs where no menus are defined, the theme generates a simple menu rather than listing all pages 

* **Reading Time Plugin** The [Estimated Reading Time Plugin](https://wordpress.org/plugins/estimated-post-reading-time/) is no longer available, so the theme now uses [Reading Time WP](https://wordpress.org/plugins/reading-time-wp/).

* **Separate CSS / Functions for Custom Installs** Fixed quote bug for captions, and changed writing form so it only displays featured image and intro text for first view of the form (so you can add really long instructions).

* **Tweet This Button** There is a new option so you can have your own hashtags added when someone tweets a published item. Also, twitter card meta data has been added so these tweets have the extended card display that shows the featured image, the site's title, and an excerpt from the item-- example https://twitter.com/cogdog/status/822656183769198593

* **Options Refreshed, Auto Page Creation, Better Reading Time Display** Recoded the theme options so documentation in its own tab. When he theme is activated now, the necessary pages will be created automagically. And if the Estimated reading time plugin is not activated, nothing will be displayed where it normally displays.

* **Provide an Email, Edit your Work Later** A major limitation of the first versions was that authors had no ability to edit their work after publishing (that's what happens when you don't have logins). As of November 2015, authors have a new field where they can enter an email address- if this is provided (it is not required), they will receive via email a special coded URL they can use at anytime to modify their work. When published, any writing with an email is published with a `Get Edit Link` button at the bottom which will resend the link to the email associated with it.

* **Admins Can Get Edit Link for Anybody** Even if the author does not provide an email address, in the Wordpress Dashboard interface, editors and admins can click and copy an edit like they can provide directly to a writer (this is added as a side metabox).
