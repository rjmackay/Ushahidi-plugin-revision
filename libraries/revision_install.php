<?php
/**
 * Revision - Install
 *
 * @author	   Robbie Mackay
 * @package	   Revision
 */

class Revision_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = Database::instance();
	}

	/**
	 * Creates the required database tables for the revision plugin
	 */
	public function run_install()
	{
		// Create the database tables.
		// Also include table_prefix in name
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'revision_incident` (
					`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					`incident_id` bigint(20) unsigned NOT NULL,
					`user_id` int(11) unsigned NULL,
					`time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					`changed_data` BLOB NULL COMMENT \'Serialized array of changed fields\',
					`data` BLOB NOT NULL COMMENT \'Serialized array of incident data\',
					`verified_id` bigint(20) unsigned NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `verified_id` (`verified_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
		
		// Copy verify
		if (ORM::factory('revision_incident')->count_all() == 0)
		{
			$this->db->query('
				INSERT INTO `'.Kohana::config('database.default.table_prefix').'revision_incident` 
					(`incident_id`, `user_id`, `time`, `changed_data`, `data`, `verified_id`)
					SELECT `incident_id`, `user_id`, `verified_date`, "a:0:{}", "a:0:{}", `verified`.`id` FROM `'.Kohana::config('database.default.table_prefix').'verified`;
			');
		}

	}

	/**
	 * Deletes the database tables for the revision module
	 */
	public function uninstall()
	{
		// I worry that someone will have tons of data saved, then carelessly click "deactiviate" and blow the whole thing.
		// So I make it harder than that.
	}
}
