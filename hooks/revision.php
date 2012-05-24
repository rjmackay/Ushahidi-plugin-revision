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
		Event::add('ushahidi_action.report_submit', array($this, '_save_data'));
		Event::add('ushahidi_action.report_submit_admin', array($this, '_save_data'));
	}
	
	/**
	 * This will save the changes to the categories
	 */
	public function _save_data()
	{
		//pull the data from the event
		$incident = Event::$data;
		$id = $incident->id;
		
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
		
		$revision = ORM::factory('revision_incidents');
		$revision->incident_id = $incident->id;
		$revision->data = serialize($data);
		$revision->save();
	}//end _save_data
}

new revision;
