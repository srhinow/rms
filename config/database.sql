CREATE TABLE `tl_rms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `ref_table` varchar(255) NOT NULL default '',  
  `ref_id` int(10) unsigned NOT NULL default '0',
  `ref_author` int(10) unsigned NOT NULL default '0',
  `ref_notice` longtext NULL,
  `status` int(10) unsigned NOT NULL default '0',
  `data` text NULL,  
  PRIMARY KEY  (`id`),
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `tl_rms_settings` (
  `id` int(10) unsigned NOT NULL auto_increment,  
  `tstamp` int(10) unsigned NOT NULL default '0',
  `modify` int(10) unsigned NOT NULL default '0',  
  `control_group` int(10) unsigned NOT NULL default '0',
  `whitelist_domains` varchar(255) NOT NULL default '',
  `release_tables` varchar(255) NOT NULL default '',  
  `sender_email` varchar(155) NOT NULL default '',
  `prevjump_newsletter` int(10) unsigned NOT NULL default '0',
  `prevjump_news` int(10) unsigned NOT NULL default '0',
  `prevjump_calendar_events` int(10) unsigned NOT NULL default '0',  
  PRIMARY KEY  (`id`), 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_content` (
  `rms_notice` longtext NULL,
  `rms_release_info` char(1) NOT NULL default '',
  `rms_first_save` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_article` (
  `rms_notice` longtext NULL,
  `rms_release_info` char(1) NOT NULL default '',
  `rms_first_save` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_newsletter` (
  `rms_notice` longtext NULL,
  `rms_release_info` char(1) NOT NULL default '',
  `rms_first_save` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_news` (
  `rms_notice` longtext NULL,
  `rms_release_info` char(1) NOT NULL default '',
  `rms_first_save` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_calendar_events` (
  `rms_notice` longtext NULL,
  `rms_release_info` char(1) NOT NULL default '',
  `rms_first_save` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
