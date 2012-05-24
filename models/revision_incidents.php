<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Version Categories
 *
 * @author     John Etherton <john@ethertontech.com>
 * @package    Version Categories Plugin
 */

class Revision_incidents_Model extends ORM {
	// Database table name
	protected $table_name = 'revision_incidents';

	/**
	 * One-to-one relationship definition
	 * @var array
	 */
	protected $has_one = array('verify');

	/**
	 * Many-to-one relationship definition
	 * @var array
	 */
	protected $belongs_to = array('incident');

	/*
	 * Diff this revision against a previous one
	 */
	public function diff(Revision_incidents_Model $prev_revision, $ignore_date_and_null = TRUE)
	{
		$data_this = unserialize($this->data);
		$data_prev = unserialize($prev_revision->data);
		return $this->array_diff_assoc_recursive($data_this,$data_prev);
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
