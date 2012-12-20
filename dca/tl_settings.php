<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2012 <http://www.sr-tag.de>
 * @author     Stefan Lindecke  <stefan@ktrion.de>
 * @author     Sven Rhinow <kservice@sr-tag.de> 
 * @package    rms (Release Management System)
 * @license    LGPL 
 */

/**
 * System configuration
 */
 
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{rms_legend:hide},rms_active,rms_control_group,rms_sender,rms_senderName';
 
$GLOBALS['TL_DCA']['tl_settings']['fields']['rms_active'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['rms_active'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		);
		
$GLOBALS['TL_DCA']['tl_settings']['fields']['rms_control_group'] = array
		(
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['rms_control_group'],
		'exclude'                 => true,
		'inputType'               => 'radio',
		'foreignKey'              => 'tl_user_group.name',
		'eval'                    => array('multiple'=>false)
		);

$GLOBALS['TL_DCA']['tl_settings']['fields']['rms_sender'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['rms_sender'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'email', 'maxlength'=>128, 'decodeEntities'=>true, 'tl_class'=>'clr w50')
		);		
		
			
?>