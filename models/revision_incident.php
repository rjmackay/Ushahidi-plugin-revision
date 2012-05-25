<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Version Categories
 *
 * @author     John Etherton <john@ethertontech.com>
 * @package    Version Categories Plugin
 */

class Revision_incident_Model extends ORM {
	// Database table name
	protected $table_name = 'revision_incident';

	/**
	 * Many-to-one relationship definition
	 * @var array
	 */
	protected $belongs_to = array('incident','user');

	/*
	 * Diff this revision against a previous one
	 */
	public function diff(Revision_incident_Model $prev_revision)
	{
		$data_this = unserialize($this->data);
		$data_prev = unserialize($prev_revision->data);
		return $this->array_diff_assoc_recursive($data_this,$data_prev);
	}
	
	public function data()
	{
		$data = @unserialize($this->data);
		return $data ? $data : FALSE;
	}
	
	public function changed_data($ignore_date_and_id = TRUE)
	{
		$changed = @unserialize($this->changed_data);
		
		if ($ignore_date_and_id)
		{
			unset($changed['incident_datemodify']);
			if (isset($changed['location']))
			{
				unset($changed['location']['location_date']);
				if (count($changed['location']) == 0)
					unset($changed['location']);
			}
			
			if (isset($changed['incident_person']))
			{
				unset($changed['incident_person']['id']);
				unset($changed['incident_person']['person_date']);
				if (count($changed['incident_person']) == 0)
					unset($changed['incident_person']);
			}

			if (isset($changed['media']))
			{
				foreach ($changed['media'] as $k => $m)
				{
					unset($changed['media'][$k]['media_date']);
					unset($changed['media'][$k]['id']);
					if (count($changed['media'][$k]) == 0)
						unset($changed['media'][$k]);
				}
				if (count($changed['media'] == 0))
					unset($changed['media']);
			}
			
			if (count($changed) == 0)
				$changed = FALSE;
		}
		
		return $changed ? $changed : FALSE;
	}
	
	public function changed_data_str()
	{
		if ($this->changed_data())
		{
			return implode(', ',array_keys($this->changed_data()));
		}
		return FALSE;
	}
	
	public function __toString()
	{
		$line = '';
		$line .= isset($this->user_id) ? Kohana::lang('ui_admin.edited_by')." ".$this->user->name.". " : '';
		$line .= $this->changed_data() ? Kohana::lang('revision.changed_fields')." ".$this->changed_data_str(). " " : '';
		$line .= "(" .$this->time. ")";
		return $line;
	}

	/*
	 * Recursive array_diff_assoc_recursive - Helper function for diff()
	 */
	private function array_diff_assoc_recursive($array1, $array2)
	{
		foreach ($array1 as $key => $value)
		{
			if (is_array($value))
			{
				if (!array_key_exists($key, $array2))
				{
					$difference[$key] = $value;
				}
				elseif (!is_array($array2[$key]))
				{
					$difference[$key] = $value;
				}
				else
				{
					$new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
					if ($new_diff != FALSE)
					{
						$difference[$key] = $new_diff;
					}
				}
			}
			elseif (!array_key_exists($key, $array2) || $array2[$key] != $value)
			{
				$difference[$key] = $value;
			}
		}
		return !isset($difference) ? FALSE : $difference;
	}

}
