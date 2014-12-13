<?php

/*
  Plugin Name: Estimated Post Reading Time
  Plugin URI: http://wordpress.org/extend/plugins/estimated-post-reading-time/
  Description: Calculates an average required time to complete reading a post.
  Version: 1.3.2
  Author: Konstantinos Kouratoras
  Author URI: http://www.kouratoras.gr
  Author Email: kouratoras@gmail.com
  License: GPL v2

  Copyright 2012 Konstantinos Kouratoras (kouratoras@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class EstimatedPostReadingTime {

	/* -------------------------------------------------- */
	/* Constructor
	  /*-------------------------------------------------- */

	public function __construct() {

		//Load localisation files
		add_action( 'init', array(&$this, 'plugin_textdomain'));
		
		//Shortcode
		add_action("init", array(&$this, "register_estimate_time_shortcode"));
				
		//Options Page
		require_once( plugin_dir_path(__FILE__) . "/lib/options.php" );
		$estimatedPostReadingTimeOptions = new EstimatedPostReadingTimeOptions();
		add_action("admin_menu", array(&$estimatedPostReadingTimeOptions, "plugin_add_options"));
	}
	
	function plugin_textdomain() {

		load_plugin_textdomain(
			'estimated-post-reading-time-locale',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
	}

	function estimate_time_shortcode() {

		$result = "";
		
		$show_in_homepage = get_option("eprt_show_in_homepage", "0");
		$show_in_archive = get_option("eprt_show_in_archive", "0");
		
		if ($show_in_homepage == "0" && (is_home() || is_front_page())) {
			return "";
		}

		if ($show_in_archive == "0" && is_archive()) {
			return "";
		}

		$wpm = get_option("eprt_words_per_minute", 250);
		$lowercase = get_option("eprt_lowercase", "0");
		
		if (trim($wpm) == "") {
			$wpm = "250";
		}

		global $post;
		$content = strip_tags($post->post_content);		
		$content_words = str_word_count($content);
		$estimated_minutes = floor($content_words / $wpm);

		if ($estimated_minutes < 1) {
			$result = ($lowercase == '1' ? __("less than a minute", "estimated-post-reading-time-locale") : __("Less than a minute", "estimated-post-reading-time-locale"));
		}
		else if ($estimated_minutes > 60) {
			if ($estimated_minutes > 1440){
				$result = ($lowercase == '1' ? __("more than a day", "estimated-post-reading-time-locale") : __("More than a day", "estimated-post-reading-time-locale"));
			}
			else {
				$result = floor($estimated_minutes / 60) . " " . __("hours", "estimated-post-reading-time-locale");
			}
		}
		else if ($estimated_minutes == 1) {
			$result = $estimated_minutes . " " . __("minute", "estimated-post-reading-time-locale");
		}
		else {
			$result = $estimated_minutes . " " . __("minutes", "estimated-post-reading-time-locale");
		}


		return $result;
	}

	function register_estimate_time_shortcode() {
		add_shortcode("est_time", array(&$this, "estimate_time_shortcode"));
	}
	
}

new EstimatedPostReadingTime();