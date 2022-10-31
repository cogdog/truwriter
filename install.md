# Installing The TRU Writer Theme

Here are your instructions for installing the[TRU Writer SPLOT WordPress theme](https://github.com/cogdog/truwriter).

Using this theme requires a self-hosted--or institutionally hosted (lucky you)-- Wordpress site (the kind that you download from [wordpress.org](http://www.wordpress.org). You cannot use this theme on the free "wordpress.com" site unless you have a business plan. Maybe check out [Reclaim Hosting](https://reclaimhosting.com/) if you need to set up your own hosting space. 

## Installing TRU Writer


The TRU Writer is a child theme based on [the free and elegant Radcliffe theme by Anders Noren](https://wordpress.org/themes/radcliffe). Install this theme first from within the Wordpress Dashboard under **Appearance** -- **Themes** searching on `Radcliffe`.

### Installing TRU Writer From Scratch

You can [download a .zip file of this theme](https://github.com/cogdog/truwriter/archive/refs/heads/master.zip) via the green **Code*" button above. 

The zip can be uploaded directly to your site via **Themes** in the Wordpress dashboard, then **Add Theme** and finally **Upload Theme**. If you run into size upload limits or just prefer going old school like me, unzip the package and ftp the entire folder into your `wp-content/themes` directory.

To get the TRU Writer working all you need to do is activate the "TRU Writer" theme when it appears in the Wordpress dashboard under **Appearance** --> **Themes**.  

### Installing TRU Writer in One Click with WP Pusher (get automatic updates!)

To have your site stay up to date automatically, I recommend trying the [WP Pusher plugin](https://wppusher.com/) which makes it easier to install themes and plugins that are published in GitHub. It takes a few steps to set up, but it's a thing of beauty when done.

To use WP-Pusher you will need to have or create an account on [GitHub](https://github.com/) (free). Log in. 

Next [download WP Pusher plugin](https://wppusher.com/download) as a ZIP file. From the plugins area of your Wordpress dashboard, click the **Upload Plugin** button, select that zip file to upload, and activate the plugin.

Then click the **WP Pusher** option in your Wordpress Dashboard, and then click the **GitHub** tab. Next click the **Obtain a GitHub Token** button to get an authentication token. Copy the one that is generated, paste into the field for it, and finally, click **Save GitHub** Token.

Now you are ready to install TRU Writer! 

![](images/wp-pusher.jpg "WP Pusher Settings")

Look under **WP Pusher** for **Install Theme**. In the form that appears, under **Theme Repository**, enter `cogdog/truwriter`. Also check the option for **Push-to-Deploy** (this will automatically update your site when the theme is updated) finally, click **Install Theme**.

Woah Neo?

Not only does this install the theme without any messy download/uploads, each time I update the theme on GitHub, your site will be automatically updated to the newest version. 

### Installing in One Click From Reclaim Hosting (get semi automatic updates!)

If you are wise enough to host your web sites at [Reclaim Hosting](http://reclaimhosting.com/) you have the option of installing a fully functioning site with this theme ([a copy of the demo site](http://lab.cogdogblog.com/writer/)) including recommended plugins, configured settings and sample content, all done  in one click. *But wait there is more!* With this method of installing your site, future updates to the theme are automatically added to your site (though not as frequently as the WP Pusher method).

In your cpanel, under **Applications** go to **All Applications**. This theme is available listed under Fratured Applications; just install from there.

![](images/reclaim-featured.jpg "Reclaim Hosting Featured Applications")

*Note that unlike other WordPress installs, this one will not preserve your username/password, so be sure to save that information.* When it's done, log into your new site and start making it your own. 

## Inserting Demo Content

If you want a site that is not completely empty, after setting up with WP-Pusher or from scratch, you can import all the content set up on the [public demo site](https://lab.cogdogblog.com/writer). 

Install all content by [downloading the WordPress export for that site](https://github.com/cogdog/truwriter/blob/master/data/truwriter.xml).  Running the WordPress Importer (under **Tools** -- **Import**) and upload that file when prompted.

You can also get a copy of the Widgets used on that site too. First intall/activate the [Widget Importer & Exporter plugin](https://wordpress.org/plugins/widget-importer-exporter/). Download the [Writer Widgets data file](https://github.com/cogdog/truwriter/blob/master/data/writer-widgets.wie). Look under the **Tools** menu for **Widget Importer & Exporter** and use the Import Widgets section to upload the data file. Boom! You got my widgets.
