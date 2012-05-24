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
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

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
