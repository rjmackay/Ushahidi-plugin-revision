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
1. Copy the entire /versioncategories/ directory into your /plugins/ directory.
2. Around line 393 of /application/helpers/reports.php, before the previous
categories are deleted add this definition and event:

	$event_data = array('id'=>$incident->id, 'new_categories'=>$post->incident_category);
	Event::run('ushahidi_action.report_categories_changing', $event_data);

3. Activate the plugin.

== Changelog ==
