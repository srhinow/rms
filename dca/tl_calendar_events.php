<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 *
 * @copyright  Sven Rhinow Webentwicklung 2012 <http://www.sr-tag.de>
 * @author     Stefan Lindecke  <stefan@ktrion.de>
 * @author     Sven Rhinow <kservice@sr-tag.de> 
 * @package    rms (Release Management System)
 * @filesource 
 */


/**
 * Table tl_calendar
 */
 
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['rms_notice'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['rms_notice'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false, 'rte'=>FALSE)
		);
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['rms_release_info'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['rms_release_info'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'save_callback' => array
			(
				array('ReleaseManagementSystem', 'sendEmailInfo')
			)
		);
