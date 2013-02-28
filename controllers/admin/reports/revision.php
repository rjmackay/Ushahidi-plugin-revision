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

class Revision_Controller extends Tools_Controller {
	public function __construct()
	{
		parent::__construct();
	}

	public function diff($id = FALSE)
	{
		if (!$id)
		{
			throw new Kohana_404_Exception();
		}
		$this->template->this_page = 'revision';
		$this->template->content = new View('admin/reports/revision');
		
		$this->template->content->revisions = ORM::Factory('revision_incident')->where('incident_id',$id)->orderby('time','DESC')->find_all();
	}

}
