<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Revision - sets up the hooks
 *
 * @author	   Robbie Mackay
 * @package	   Revision
 */

class revision {
	
	/**
	 * Registers the main event add method
	 */
	 
	 
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));

	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		/*
		Event::add('ushahidi_action.report_edit', array($this, '_save_data'));
		Event::add('ushahidi_action.report_add', array($this, '_save_data'));
		Event::add('ushahidi_action.report_approve', array($this, '_save_data'));
		Event::add('ushahidi_action.report_unapprove', array($this, '_save_data'));
		*/
		
		// Hook on custom save event instead
		Event::add('ushahidi_action.report_save', array($this, '_save_data'));
		
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports' AND Router::$method == 'index')
		{
			Event::add('ushahidi_action.report_extra_admin', array($this, 'report_extra_admin'));
			Event::add('ushahidi_action.header_scripts', array($this, 'header_scripts'));
		}
	}
	
	/**
	 * This will save the changes to the categories
	 */
	public function _save_data()
	{
		//pull the data from the event
		$incident = Event::$data;
		
		
		$data = $incident->as_array();
		$data['location'] = $incident->location->as_array();
		$data['incident_person'] = $incident->incident_person->as_array();
		
		$data['category'] = array();
		foreach ($incident->category as $category)
		{
			$data['category'][] = $category->as_array();
		}
		
		$data['form_response'] = array();
		foreach ($incident->form_response as $response)
		{
			$data['form_response'][] = $response->as_array();
		}
		
		$data['media'] = array();
		foreach ($incident->media as $media)
		{
			$data['media'][] = $media->as_array();
		}
		
		$revision = ORM::factory('revision_incident');
		$revision->incident_id = $incident->id;
		$revision->data = serialize($data);
		if (Auth::instance()->get_user() instanceof User_Model)
		{
			$revision->user_id = Auth::instance()->get_user()->id;
		}
		
		// Get previous revision
		$prev_revision = ORM::factory('revision_incident')->where('incident_id',$incident->id)->orderby('time','DESC')->find();
		
		// Save the diff from previous version
		if ($prev_revision->loaded)
		{
			$revision->changed_data = serialize($revision->diff($prev_revision));
		}
		
		$revision->save();
	}//end _save_data
	
	/**
	 * Add styles to hide default edit logs
	 */
	public function header_scripts()
	{
		echo "
			<style>
				.post-edit-log-blue, .post-edit-log-gray {display: none;}
				.post-edit-log-blue.revision-log, .post-edit-log-gray.revision-log {display: block;}
			</style>
		";
	}
	
	/**
	 * Generate revision log
	 */
	public function report_extra_admin()
	{
		$incident = Event::$data;
		$incident_id = $incident->incident_id;
		
		// Get Edit Log
		$revisions = ORM::factory('revision_incident')->where('incident_id', $incident_id)->orderby('time','DESC')->find_all();
		$edit_count = $revisions->count();
		$edit_css = ($edit_count == 0) ? "post-edit-log-gray" : "post-edit-log-blue";
		
		$edit_log = "";
		$edit_log .= "<div class=\"revision-log ".$edit_css."\">"
			. "<a href=\"javascript:showLog('revision_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>"
			. "<div id=\"revision_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
		
		foreach ($revisions as $revision)
		{
			$edit_log .= "<li>$revision</li>";
		}
		$edit_log .= "</ul><a href='". url::site('admin/reports/revision/diff/'.$incident_id)."'>Full revision log</a></div>";
		
		echo $edit_log;
	}
}

new revision;
