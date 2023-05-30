# ASMAC Cornerstone Condition Rules Monitor WordPress Plugin

Reports via email if a Cornerstone conditions rule not found.

Useful if there's a missing condition rule definition or if, for example, the [ASMAC Cornerstone Groups Condition](https://github.com/asmac-org/asmac-cornerstone-groups-condition) plugin was deactivated or removed but WordPress pages still had Cornerstone conditions defined referencing those group memberships.

Cornerstone conditions will consider an undefined condition rule as if the rule returned false. So, any content or elements set with conditions will not show when the condition rule is not defined.

## Requirements

* WordPress -- tested with WordPress 6.2.2.
* PHP 8.0.28 -- tested with PHP 8.0.28.
* themeco [Cornerstone](https://theme.co/docs/cornerstone-overview) builder -- tested with Cornerstone 7.2.5 using Pro theme 6.2.5.
* itthinx [Groups WordPress plugin](https://wordpress.org/plugins/groups/) -- tested with Groups 2.18.0

## Installation

1. Upload or extract the `asmac-cornerstone-condition-rules-monitor` folder to your site's `/wp-content/plugins/` directory. You can also use the *Add new* option found in the *Plugins* menu in WordPress.  
2. Enable the plugin from the *Plugins* menu in WordPress.

## Usage

Ideally, nothing happens. If a warning message about `PHP Warning:  No rule matching function for groups_is_member  in` is triggered, then an email will be sent to the admin_email defined in WordPress Settings > General.

You can also add a filter to override the email address for notifications:

	asmac_cornerstone_condition_rules_monitor_to_address

Something like the following should work:

```php
	add_filter('asmac_cornerstone_condition_rules_monitor_to_address', 'example_override_to_address_filter', 10, 1);
	function example_override_to_address_filter($to_address) {
		return 'notification-user@example.com';
```

## Limitations

Hardcoded name of the plugin being monitored. Warning messages will be for any Cornerstone condition rule not found.

## Credits

Written by Jeff Kellem for ASMAC (American Society of Music Arrangers and Composers). Instead of writing up notes to describe why themeco should consider adding support for Groups, this initial code was written, though the [docs for conditions](https://theme.co/docs/cornerstone-developer-guide#conditions) are lacking.

This monitor was written to make sure notification happened in case a condition still existed when the matching condition rule no longer did, e.g., if the plugin providing the condition rule was deactivated or removed.

© 2023 Jeff Kellem<br/>
License: BSD-2-CLAUSE
