<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Revision Controller - for debugging
 *
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Revision
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Revision_debug_Controller extends Admin_Controller {
	public function __construct()
	{
		parent::__construct();
	}

	public function index($id = FALSE)
	{
		header("Content-type: text/plain; charset=UTF-8\n");
		$this->template->this_page = 'revision';
		$this->template->content = '';
		
		foreach(ORM::Factory('revision_incident')->find_all() as $revision)
		{
			var_dump(unserialize($revision->data));
			//var_dump($revision);
		}
		exit();
	}

	public function diff($id)
	{
		header("Content-type: text/plain; charset=UTF-8\n");
		$this->template->this_page = 'revision';
		$this->template->content = '';
		
		$revisions = ORM::factory('revision_incident')->where('incident_id',$id)->orderby('time', 'DESC')->find_all(2,0);
		
		$revisions = $revisions->as_array();
		if (count($revisions) > 0)
		{
			$data0 = unserialize($revisions[0]->data);
			$data1 = unserialize($revisions[1]->data);
			
			var_dump($revisions[0]->diff($revisions[1]));
			var_dump($revisions[1]->diff($revisions[0]));
		}
		
		exit();
	}

}
