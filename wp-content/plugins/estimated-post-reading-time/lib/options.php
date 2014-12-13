<?php



class EstimatedPostReadingTimeOptions {



	public function plugin_add_options() {

		add_options_page('Post Reading Time', 'Post Reading Time', 'manage_options', 'eprtoptions', array(&$this, 'plugin_options_page'));

	}



	function plugin_options_page() {



		$opt_name = array(

		    'eprt_words_per_minute' => 'eprt_words_per_minute',

		    'eprt_show_in_homepage' => 'eprt_show_in_homepage',

		    'eprt_show_in_archive' => 'eprt_show_in_archive',

		    'eprt_lowercase' => 'eprt_lowercase',

		);

		$hidden_field_name = 'eprt_submit_hidden';



		$opt_val = array(

		    'eprt_words_per_minute' => get_option($opt_name['eprt_words_per_minute']),

		    'eprt_show_in_homepage' => get_option($opt_name['eprt_show_in_homepage']),

		    'eprt_show_in_archive' => get_option($opt_name['eprt_show_in_archive']),

		    'eprt_lowercase' => get_option($opt_name['eprt_lowercase']),

		);

		

		if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {

			$opt_val = array(

			    'eprt_words_per_minute' => stripslashes(esc_html(esc_attr(($_POST[$opt_name['eprt_words_per_minute']])))),

			    'eprt_show_in_homepage' => $_POST[$opt_name['eprt_show_in_homepage']],

			    'eprt_show_in_archive' => $_POST[$opt_name['eprt_show_in_archive']],

			    'eprt_lowercase' => $_POST[$opt_name['eprt_lowercase']],

			);

			update_option($opt_name['eprt_words_per_minute'], $opt_val['eprt_words_per_minute']);

			update_option($opt_name['eprt_show_in_homepage'], $opt_val['eprt_show_in_homepage']);

			update_option($opt_name['eprt_show_in_archive'], $opt_val['eprt_show_in_archive']);

			update_option($opt_name['eprt_lowercase'], $opt_val['eprt_lowercase']);

			?>

			<div id="message" class="updated fade">

				<p><strong>

						<?php _e('Options saved.', 'estimated-post-reading-time-locale'); ?>

					</strong></p>

			</div>

			<?php

		}

		

		if(trim($opt_val['eprt_words_per_minute'])==""){

			$opt_val['eprt_words_per_minute'] = "250";

		}

		

		?>



		<div class="wrap">

			<h2><?php _e('Estimated Post Reading Time', 'estimated-post-reading-time-locale'); ?></h2>

			

			<h3><?php _e('Settings', 'estimated-post-reading-time-locale'); ?></h3>

			

			<form name="att_img_options" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">

				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">



				<p><label for=""><?php _e('Words Per Minute', 'estimated-post-reading-time-locale'); ?>:</label>

					<input type="text" name="<?php echo $opt_name['eprt_words_per_minute']; ?>" id="<?php echo $opt_name['eprt_words_per_minute']; ?>" value="<?php echo $opt_val['eprt_words_per_minute']; ?>"/>

				</p>



				<p><label for=""><?php _e('Show in homepage', 'estimated-post-reading-time-locale'); ?>:</label>

					<select name="<?php echo $opt_name['eprt_show_in_homepage']; ?>">

						<option value="1" <?php echo ($opt_val['eprt_show_in_homepage'] == "1") ? 'selected="selected"' : ''; ?> ><?php _e('Yes', 'estimated-post-reading-time-locale'); ?></option>

						<option value="0" <?php echo ($opt_val['eprt_show_in_homepage'] == "0") ? 'selected="selected"' : ''; ?> ><?php _e('No', 'estimated-post-reading-time-locale'); ?></option>						

					</select>

				</p>



				<p><label for=""><?php _e('Show in archives', 'estimated-post-reading-time-locale'); ?>:</label>

					<select name="<?php echo $opt_name['eprt_show_in_archive']; ?>">

						<option value="1" <?php echo ($opt_val['eprt_show_in_archive'] == "1") ? 'selected="selected"' : ''; ?> ><?php _e('Yes', 'estimated-post-reading-time-locale'); ?></option>

						<option value="0" <?php echo ($opt_val['eprt_show_in_archive'] == "0") ? 'selected="selected"' : ''; ?> ><?php _e('No', 'estimated-post-reading-time-locale'); ?></option>

					</select>

				</p>

				

				<p><label for=""><?php _e('All letters lowercase', 'estimated-post-reading-time-locale'); ?>:</label>

					<select name="<?php echo $opt_name['eprt_lowercase']; ?>">

						<option value="1" <?php echo ($opt_val['eprt_lowercase'] == "1") ? 'selected="selected"' : ''; ?> ><?php _e('Yes', 'estimated-post-reading-time-locale'); ?></option>

						<option value="0" <?php echo ($opt_val['eprt_lowercase'] == "0") ? 'selected="selected"' : ''; ?> ><?php _e('No', 'estimated-post-reading-time-locale'); ?></option>

					</select>

				</p>



				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'estimated-post-reading-time-locale'); ?>"></p>

			</form>

			

			<h3><?php _e('Shortcode Usage', 'estimated-post-reading-time-locale'); ?></h3>

			

			<p>In order to show the estimated post reading time in your post/page insert the <code>[est_time]</code> shortcode anywhere in the content.</p>



			<?php

		}



	}

	