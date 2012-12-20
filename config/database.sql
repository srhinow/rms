CREATE TABLE `tl_rms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `ref_table` varchar(255) NOT NULL default '',  
  `ref_id` int(10) unsigned NOT NULL default '0',
  `ref_author` int(10) unsigned NOT NULL default '0',
  `ref_notice` longtext NULL,  
  `data` text NULL,  
  PRIMARY KEY  (`id`),
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_content` (
  `rms_notice` longtext NULL,
  `rms_release_info` char(1) NOT NULL default '',
  `rms_first_save` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
