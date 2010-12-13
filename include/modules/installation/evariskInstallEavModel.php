<?php

function eavModelInstall()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ENTITY . "'") != TABLE_ENTITY) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ENTITY . " (
				`entity_type_id` smallint(5) unsigned NOT NULL auto_increment,
				`entity_type_code` varchar(50) collate utf8_unicode_ci NOT NULL default '',
				`entity_model` varchar(255) collate utf8_unicode_ci NOT NULL,
				`attribute_model` varchar(255) collate utf8_unicode_ci NOT NULL,
				`entity_table` varchar(255) collate utf8_unicode_ci NOT NULL default '',
				`value_table_prefix` varchar(255) collate utf8_unicode_ci NOT NULL default '',
				`default_attribute_set_id` smallint(5) unsigned NOT NULL default '0',
				PRIMARY KEY  (`entity_type_id`),
				KEY `entity_name` (`entity_type_code`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO `wp_eav__entity_type` (`entity_type_id`, `entity_type_code`, `entity_model`, `attribute_model`, `entity_table`, `value_table_prefix`, `default_attribute_set_id`) VALUES(1, 'eva_users', 'evarisk/users', '', 'users', 'users_', 1);";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ENTITY_ATTRIBUTE_LINK . "'") != TABLE_ENTITY_ATTRIBUTE_LINK) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ENTITY_ATTRIBUTE_LINK . " (
				`entity_attribute_id` int(10) unsigned NOT NULL auto_increment,
				`entity_type_id` smallint(5) unsigned NOT NULL default '0',
				`attribute_set_id` smallint(5) unsigned NOT NULL default '0',
				`attribute_group_id` smallint(5) unsigned NOT NULL default '0',
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`sort_order` smallint(6) NOT NULL default '0',
				PRIMARY KEY  (`entity_attribute_id`),
				UNIQUE KEY `attribute_group_id` (`attribute_group_id`,`attribute_id`),
				KEY `attribute_set_id_3` (`attribute_set_id`,`sort_order`),
				KEY `FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE` (`attribute_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_SET . "'") != TABLE_ATTRIBUTE_SET) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_SET . " (
				`attribute_set_id` smallint(5) unsigned NOT NULL auto_increment,
				`entity_type_id` smallint(5) unsigned NOT NULL default '0',
				`attribute_set_name` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL default '',
				`sort_order` smallint(6) NOT NULL default '0',
				PRIMARY KEY  (`attribute_set_id`),
				UNIQUE KEY `entity_type_id` (`entity_type_id`,`attribute_set_name`),
				KEY `entity_type_id_2` (`entity_type_id`,`sort_order`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		INSERT INTO " . TABLE_ENTITY . " (`attribute_set_id`, `entity_type_id`, `attribute_set_name`, `sort_order`) VALUES(1, 1, 'evariskUserDefault', 1);";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_OPTION_VALUE . "'") != TABLE_ATTRIBUTE_OPTION_VALUE) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_OPTION_VALUE . " (
				`value_id` int(10) unsigned NOT NULL auto_increment,
				`option_id` int(10) unsigned NOT NULL default '0',
				`value` varchar(255) collate utf8_unicode_ci NOT NULL default '',
				PRIMARY KEY  (`value_id`),
				KEY `FK_ATTRIBUTE_OPTION_VALUE_OPTION` (`option_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Attribute option values';";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_OPTION . "'") != TABLE_ATTRIBUTE_OPTION) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_OPTION . " (
				`option_id` int(10) unsigned NOT NULL auto_increment,
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`sort_order` smallint(5) unsigned NOT NULL default '0',
				PRIMARY KEY  (`option_id`),
				KEY `FK_ATTRIBUTE_OPTION_ATTRIBUTE` (`attribute_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Attributes option (for source model)';";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE_GROUP . "'") != TABLE_ATTRIBUTE_GROUP) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE_GROUP . " (
				`attribute_group_id` smallint(5) unsigned NOT NULL auto_increment,
				`attribute_set_id` smallint(5) unsigned NOT NULL default '0',
				`attribute_group_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
				`sort_order` smallint(6) NOT NULL default '0',
				`default_id` smallint(5) unsigned default '0',
				PRIMARY KEY  (`attribute_group_id`),
				UNIQUE KEY `attribute_set_id` (`attribute_set_id`,`attribute_group_name`),
				KEY `attribute_set_id_2` (`attribute_set_id`,`sort_order`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			INSERT INTO " . TABLE_ATTRIBUTE_GROUP . " (`attribute_group_id`, `attribute_set_id`, `attribute_group_name`, `sort_order`, `default_id`) VALUES(1, 1, 'user_profile', 1, 1);";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_ATTRIBUTE . "'") != TABLE_ATTRIBUTE) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_ATTRIBUTE . " (
				`attribute_id` smallint(5) unsigned NOT NULL auto_increment,
				`entity_type_id` smallint(5) unsigned NOT NULL default '0',
				`attribute_status` enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				`attribute_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
				`attribute_model` varchar(255) collate utf8_unicode_ci default NULL,
				`backend_model` varchar(255) collate utf8_unicode_ci default NULL,
				`backend_type` enum('static','datetime','decimal','int','text','varchar') collate utf8_unicode_ci NOT NULL default 'static',
				`backend_table` varchar(255) collate utf8_unicode_ci default NULL,
				`frontend_model` varchar(255) collate utf8_unicode_ci default NULL,
				`frontend_input` varchar(50) collate utf8_unicode_ci default NULL,
				`frontend_label` varchar(255) collate utf8_unicode_ci default NULL,
				`frontend_class` varchar(255) collate utf8_unicode_ci default NULL,
				`source_model` varchar(255) collate utf8_unicode_ci default NULL,
				`is_global` tinyint(1) unsigned NOT NULL default '1',
				`is_visible` tinyint(1) unsigned NOT NULL default '1',
				`is_required` tinyint(1) unsigned NOT NULL default '0',
				`is_user_defined` tinyint(1) unsigned NOT NULL default '0',
				`default_value` text collate utf8_unicode_ci,
				`is_searchable` tinyint(1) unsigned NOT NULL default '0',
				`is_filterable` tinyint(1) unsigned NOT NULL default '0',
				`is_comparable` tinyint(1) unsigned NOT NULL default '0',
				`is_visible_on_front` tinyint(1) unsigned NOT NULL default '0',
				`is_html_allowed_on_front` tinyint(1) unsigned NOT NULL default '0',
				`is_unique` tinyint(1) unsigned NOT NULL default '0',
				`is_filterable_in_search` tinyint(1) unsigned NOT NULL default '0',
				`used_for_sort_by` tinyint(1) unsigned NOT NULL default '0',
				`is_configurable` tinyint(1) unsigned NOT NULL default '1',
				`apply_to` varchar(255) collate utf8_unicode_ci NOT NULL,
				`position` int(11) NOT NULL,
				`note` varchar(255) collate utf8_unicode_ci NOT NULL,
				`is_visible_in_advanced_search` tinyint(1) unsigned NOT NULL default '0',
				PRIMARY KEY  (`attribute_id`),
				UNIQUE KEY `entity_type_id` (`entity_type_id`,`attribute_code`),
				KEY `IDX_USED_FOR_SORT_BY` (`entity_type_id`,`used_for_sort_by`),
				KEY `IDX_USED_IN_PRODUCT_LISTING` (`entity_type_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}
}



function eavModelUsersValueInstall()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_DATETIME . "'") != TABLE_EAV_USER_DATETIME) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_DATETIME . " (
				`value_id` int(11) NOT NULL auto_increment,
				`entity_type_id` smallint(8) unsigned NOT NULL default '0',
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`entity_id` int(10) unsigned NOT NULL default '0',
				`value` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY  (`value_id`),
				UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
				KEY `FK_CUSTOMER_DATETIME_ENTITY_TYPE` (`entity_type_id`),
				KEY `FK_CUSTOMER_DATETIME_ATTRIBUTE` (`attribute_id`),
				KEY `FK_CUSTOMER_DATETIME_ENTITY` (`entity_id`),
				KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_DECIMAL . "'") != TABLE_EAV_USER_DECIMAL) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_DECIMAL . " (
				`value_id` int(11) NOT NULL auto_increment,
				`entity_type_id` smallint(8) unsigned NOT NULL default '0',
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`entity_id` int(10) unsigned NOT NULL default '0',
				`value` decimal(12,4) NOT NULL default '0.0000',
				PRIMARY KEY  (`value_id`),
				UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
				KEY `FK_CUSTOMER_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
				KEY `FK_CUSTOMER_DECIMAL_ATTRIBUTE` (`attribute_id`),
				KEY `FK_CUSTOMER_DECIMAL_ENTITY` (`entity_id`),
				KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_INT . "'") != TABLE_EAV_USER_INT) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_INT . " (
				`value_id` int(11) NOT NULL auto_increment,
				`entity_type_id` smallint(8) unsigned NOT NULL default '0',
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`entity_id` int(10) unsigned NOT NULL default '0',
				`value` int(11) NOT NULL default '0',
				PRIMARY KEY  (`value_id`),
				UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
				KEY `FK_CUSTOMER_INT_ENTITY_TYPE` (`entity_type_id`),
				KEY `FK_CUSTOMER_INT_ATTRIBUTE` (`attribute_id`),
				KEY `FK_CUSTOMER_INT_ENTITY` (`entity_id`),
				KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_TEXT . "'") != TABLE_EAV_USER_TEXT) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_TEXT . " (
				`value_id` int(11) NOT NULL auto_increment,
				`entity_type_id` smallint(8) unsigned NOT NULL default '0',
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`entity_id` int(10) unsigned NOT NULL default '0',
				`value` text collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (`value_id`),
				UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
				KEY `FK_CUSTOMER_TEXT_ENTITY_TYPE` (`entity_type_id`),
				KEY `FK_CUSTOMER_TEXT_ATTRIBUTE` (`attribute_id`),
				KEY `FK_CUSTOMER_TEXT_ENTITY` (`entity_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EAV_USER_VARCHAR . "'") != TABLE_EAV_USER_VARCHAR) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EAV_USER_VARCHAR . " (
				`value_id` int(11) NOT NULL auto_increment,
				`entity_type_id` smallint(8) unsigned NOT NULL default '0',
				`attribute_id` smallint(5) unsigned NOT NULL default '0',
				`entity_id` int(10) unsigned NOT NULL default '0',
				`value` varchar(255) collate utf8_unicode_ci NOT NULL default '',
				PRIMARY KEY  (`value_id`),
				UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`),
				KEY `FK_CUSTOMER_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
				KEY `FK_CUSTOMER_VARCHAR_ATTRIBUTE` (`attribute_id`),
				KEY `FK_CUSTOMER_VARCHAR_ENTITY` (`entity_id`),
				KEY `IDX_VALUE` (`entity_id`,`attribute_id`,`value`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

}



function evaUsersGroup()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP . "'") != TABLE_EVA_USER_GROUP) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_USER_GROUP . " (
				`user_group_id` smallint(5) unsigned NOT NULL auto_increment,
				`user_group_status` enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				`user_group_name` varchar(255) collate utf8_unicode_ci NOT NULL,
				`user_group_description` text collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (`user_group_id`),
				KEY `user_group_status` (`user_group_status`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP_DETAILS . "'") != TABLE_EVA_USER_GROUP_DETAILS) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_USER_GROUP_DETAILS . " (
				`user_group_id` smallint(5) unsigned NOT NULL,
				`user_id` bigint(20) unsigned NOT NULL,
				PRIMARY KEY  (`user_group_id`,`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		// Execution de la requete
		dbDelta($sql);
	}
}



function evaRoles()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EVA_USER_GROUP_ROLES_DETAILS . "'") != TABLE_EVA_USER_GROUP_ROLES_DETAILS) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_USER_GROUP_ROLES_DETAILS . " (
				`user_group_id` int(11) unsigned NOT NULL,
				`eva_role_id` int(11) unsigned NOT NULL,
				PRIMARY KEY  (`user_group_id`,`eva_role_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liaison entre groupes d''utilisateurs et roles (profil)';";
		// Execution de la requete
		dbDelta($sql);
	}

	/*	We check that table exists, if it's not the case we create it	*/
	if( $wpdb->get_var("show tables like '" . TABLE_EVA_ROLES . "'") != TABLE_EVA_ROLES) {
		// On construit la requete SQL de création de table
		$sql = 
			"CREATE TABLE IF NOT EXISTS " . TABLE_EVA_ROLES . " (
				`eva_role_id` int(11) unsigned NOT NULL auto_increment,
				`eva_role_status` enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
				`eva_role_label` varchar(255) collate utf8_unicode_ci NOT NULL,
				`eva_role_name` varchar(255) collate utf8_unicode_ci NOT NULL,
				`eva_role_description` text collate utf8_unicode_ci NOT NULL,
				`eva_role_capabilities` text collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (`eva_role_id`),
				KEY `eva_role_status` (`eva_role_status`),
				KEY `eva_role_label` (`eva_role_label`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Définition des roles pour evarisk';";
		// Execution de la requete
		dbDelta($sql);
	}
}