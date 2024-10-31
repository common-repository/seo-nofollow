<?php
/**
 * Plugin Name: SEO Nofollow
 * Description: Auto add rel="nofollow" to specified domain names in links. This is an ultra light weight plugin. Whatever affiliate program you are using just add the domain to the list and the Plugin will do the rest. For instance add amazon.com, amzn.to, affili.net and booking.com to the domain list and all links to these websites will be marked as nofollow.
 * Version: 1.0.0
 * Author: Sven Rochel
 * Author URI: https://www.svenrochel.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

//Create a function called "nofollow_setup" if it doesn't already exist
if(!function_exists( 'nofollow_setup' ) ) {
	function nofollow_setup() {
    	add_submenu_page('options-general.php', __('SEO Nofollow', 'nofollow'), __(' SEO Nofollow', 'nofollow'), 'manage_options', 'options-nofollow', 'nofollow_settings');
    	register_setting('nofollow', 'nofollow-domains');
	}
}

//Create a function called "nofollow_settings" if it doesn't already exist
if(!function_exists( 'nofollow_settings' ) ) {
	function nofollow_settings() {
	    echo '
	    		<div class="wrap">
					<h1>Einstellungen › SEO Nofollow</h1>
					<p>Automatically  add rel="nofollow" to links that point to a domain added to the list. This is an ultra light weight plugin. Whatever affiliate program you are using just add the domain to the list and the Plugin will do the rest. For instance add amazon.com, amzn.to, affili.net and booking.com to the domain list and all links to these websites will be marked as nofollow.</p>
					<p><b>Notice:</b> Only one domain per row.</p>
					<form action="options.php" method="post" novalidate="novalidate">';
					settings_fields('nofollow');
					do_settings_sections('nofollow');
					echo '
					
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label for="nofollow-domains">Add domain for nofollow</label>
								</th>
								<td>
									<textarea name="nofollow-domains" rows="14" id="nofollow-domains" class="regular-text">' . esc_attr(get_option('nofollow-domains')) . '</textarea>
								</td>
							</tr>
						</tbody>
					</table>';
					submit_button();
					echo '</form></div>'; 
	}
}

//Create a function called "add_nofollow_code" if it doesn't already exist
if(!function_exists( 'add_nofollow_code' ) ) {
	function add_nofollow_code() {
	    echo get_option('nofollow-domains');
	}
}

//Create a function called "add_nofollow" if it doesn't already exist
if(!function_exists( 'add_nofollow' ) ) {
	function add_nofollow($content) {
		$no_follow_list = explode("\n", str_replace("\r", "", get_option('nofollow-domains')));
		preg_match_all( '~<a.*>~isU', $content, $uri_match );
		
		$no_follow_list_quoted = array_map(function($a) {return preg_quote($a, '/');}, $no_follow_list);
		$no_follow_list_quoted = implode('|', $no_follow_list_quoted);
		$no_follow_list_quoted = '/('. $no_follow_list_quoted .')/i';
		
		for($i = 0; $i <= sizeof($uri_match[0]); $i++) { 
			if(isset($uri_match[0][$i]) && !preg_match('~nofollow~is', $uri_match[0][$i]) && (preg_match($no_follow_list_quoted, $uri_match[0][$i]))) { 
				$uri_change 	= trim( $uri_match[0][ $i ], ">" );
				$uri_change    .= ' rel="nofollow noopener">';
				if(!preg_match('~blank~is', $uri_match[0][$i])) {
					$uri_change .= ' target="_blank">';
				}
				$content 		= str_replace($uri_match[0][$i], $uri_change, $content);
			}
		}
		return $content;
	}
}




// Add action to admin menu
add_action('admin_menu', 'nofollow_setup');

// Add filter to the_content for regex replacement
add_filter('the_content', 'add_nofollow', 299);
