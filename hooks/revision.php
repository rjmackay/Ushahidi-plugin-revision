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
		Event::add('ushahidi_action.report_edit', array($this, '_save_data'));
		Event::add('ushahidi_action.report_add', array($this, '_save_data'));
		Event::add('ushahidi_action.config_routes', array($this, '_routes'));
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
		$revision->user_id = Auth::instance()->get_user()->id;
		
		// Get previous revision
		$prev_revision = ORM::factory('revision_incident')->where('incident_id',$incident->id)->orderby('time','DESC')->find();
		
		// Save the diff from previous version
		if ($prev_revision->loaded)
		{
			$revision->changed_data = serialize($revision->diff($prev_revision));
		}
		
		$revision->save();
	}//end _save_data
}

new revision;
