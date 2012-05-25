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
2. Activate the plugin.
3. Edit application/views/admin/reports and change these lines:
   Replace:
			$edit_count = $incident_orm->verify->count();
   With:
			$edit_count = $incident_orm->revision_incidents->count();

   Replace:
			foreach ($incident_orm->verify as $verify)
			{
				$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".$verify->user->name." : ".$verify->verified_date."</li>";
			}
   With:
			foreach ($incident_orm->revision_incidents as $revision)
			{
				$edit_log .= "<li>$revision</li>";
			}
			$edit_log .= "</ul><a href='". url::site('admin/reports/revision/diff/'.$incident_id)."'>Full revision log</a></div>";
4. Edit application/models/incident.php and add 'revision_incidents' to end of the $has_many array, ie:
			protected $has_many = array(
				'category' => 'incident_category',
				'media',
				'verify',
				'comment',
				'rating',
				'alert' => 'alert_sent',
				'incident_lang',
				'form_response',
				'cluster' => 'cluster_incident',
				'geometry',
				'revision_incidents'
			);

   Also add this function at the end of the class
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
