=== About ===
name: Revisions
website: https://github.com/rjmackay/Ushahidi-plugin-revision
description: Creates a record of what changed in a report when
version: 1.0
requires: 2.4
tested up to: 2.4
author: Robbie Mackay
author website: http://robbiemackay.com

== Description ==
This will create a table that tracks a history of report changes

== Installation ==
1. Copy the entire /revision/ directory into your /plugins/ directory.
2. Activate the plugin.
3. Edit application/models/incident.php and add this function at the end of the class
			/**
			 * Overrides the default save method for the ORM.
			 * 
			 */
			public function save()
			{
				// Fire an event on every save
				Event::run('ushahidi_action.report_save', $this);
				
				parent::save();
			}

== Changelog ==
