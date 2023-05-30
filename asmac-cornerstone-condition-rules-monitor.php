<?php
/*
   Plugin Name: ASMAC Cornerstone Condition Rules Monitor
   Description: Reports via mail if a Cornerstone condition rule is not found.
   Version: 0.6
   Author: Jeff Kellem
   Author URI: https://slantedhall.com/
   License: BSD-2-Clause
	License URI: http://opensource.org/licenses/BSD-2-Clause

   	Copyright 2023 Jeff Kellem.
*/

/*

	If you want to use an email address for notifications that is different than the
	WordPress admin email set in Settings > General, then add the filter:

		asmac_cornerstone_condition_rules_monitor_to_address

	Something like the following should work:

	add_filter('asmac_cornerstone_condition_rules_monitor_to_address', 'example_override_to_address_filter', 10, 1);
	function example_override_to_address_filter($to_address) {
		return 'notification-user@example.com';
	}
*/


class ASMAC_Cornerstone_Condition_Rules_Monitor {

	protected static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new ASMAC_Cornerstone_Condition_Rules_Monitor();
		}
 		return self::$instance;
	}

	public function setup() {
		add_action( 'shutdown', array( $this, 'shutdown' ), 1 );
	}

	public function shutdown() {
		$error = error_get_last();

		if ( is_null( $error ) ) {
			return;
		}

		$message = '';
		if ( str_contains( $error['message'], 'No rule matching function for' ) ) {
			$home_url = get_home_url();
			$message .= "This message indicates that the condition rule is not found.\n";
			$message .= "This could mean that ASMAC Cornerstone Groups Condition plugin\n";	// FIXME: hardcoded
			$message .= "has failed or is not installed but a Cornerstone condition\n";
			$message .= "that no longer exists is defined for an element (or something).\n\n\n";
			$message .= " Message: " . $error['message'];
			//$message .= "\n    File: " . $error['file'] . '[' . $error['line'] . ']';
			$message .= "\n    Site: " . $home_url;
			$message .= "\n Request: " . $_SERVER['REQUEST_URI'];
			// FIXME: could check other spots for referrer or just use wp_get_referer().
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				$referrer = $_SERVER['HTTP_REFERER'];	// use urlencode() wrapper if outputting to HTML
			} else {
				$referrer = 'Unknown: HTTP_REFERER NOT SET';
			}
			$message .= "\nReferrer: " . $referrer;

			require_once( ABSPATH . 'wp-includes/pluggable.php' );
			$user = wp_get_current_user();
			$username = $user->user_login;
			if (0 === $user->ID) {
				$username = "Anonymous";
			}
			$message .= "\n    User: " . $username . ' [' . $user->ID . ']';
			$message .= "\n\n";
			$message .= "RAW ERROR:\n";
			$message .= print_r($error, true);

			$subject = $home_url;
			if (preg_match( '/No rule matching function for (.+)/', $error['message'], $matches )) {
				$subject .= ' rule "' . $matches[1] . '" not found';
			}
		}

		$to_address = apply_filters( 'asmac_cornerstone_condition_rules_monitor_to_address', get_option('admin_email') );

		if ( !empty( $message ) && $to_address ) {
			wp_mail( $to_address, 'ASMAC Cornerstone Condition Rules Monitor: Error notification for ' . $subject, $message );
		}
		// NOTE: choosing to do nothing if somehow the WordPress site admin email address is not set.
		// The original warning message should still be in the error log.
	}
}

ASMAC_Cornerstone_Condition_Rules_Monitor::instance()->setup();
