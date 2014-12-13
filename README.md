# TRU Writer Wordpress Theme
by Alan Levine http://cogdog.info/ or http://cogdogblog.com/

## What is this?
This Wordpress Theme powers [TRU Writer](http://splot.ca/writer/) a means to let site users create rich published writing in Wordpress without logins or needs to learn Wordpress. See [an example created by copy/paste from a Word document](http://splot.ca/writer/2014/101)

![](images/web2storytelling.jpg "Sketch to Painting")


## How to Install
I will make the big leap in that you have a self hosted Wordpress site and can install themes. The Comparator is a child theme based on [the free Radcliffe theme by Anders Noren](https://wordpress.org/themes/radcliffe) 

Very very crucial. Do not just use the zip of this repo as a theme upload. It will not work. If you are uploading in the wordpress admin, you will need to make separate zips of the two themes (comparator and wordpress-bootstrap, and upload each.

In addition the site uses the [Estimated Post Reading Time plugin[(http://wordpress.org/extend/plugins/estimated-post-reading-time/) which can be installed directly in your site, but a copy is provide just for the sake of completedness. The theme will nudge you to install it.

Create a user account with Author capability. Edit functions.php near **function truwriter_autologin()** to use these credentials for the auto login.

Documentation in flux. Beta baby.  On day.