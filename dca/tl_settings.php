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
 
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{rms_legend:hide},rms_active,rms_control_group,rms_sender,rms_senderName,rms_prevjump_newsletter,rms_prevjump_news,rms_prevjump_calendar_events';
 
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
			'eval'                    => array('decodeEntities'=>true, 'tl_class'=>'clr')
		);
				
$GLOBALS['TL_DCA']['tl_settings']['fields']['rms_prevjump_newsletter'] = array
		(
		    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['rms_prevjump_newsletter'],
		    'exclude'                 => true,
		    'inputType'               => 'pageTree',
		    'eval'                    => array('fieldType'=>'radio')
		);
				
$GLOBALS['TL_DCA']['tl_settings']['fields']['rms_prevjump_news'] = array
		(
		    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['rms_prevjump_news'],
		    'exclude'                 => true,
		    'inputType'               => 'pageTree',
		    'eval'                    => array('fieldType'=>'radio')
		);
			
$GLOBALS['TL_DCA']['tl_settings']['fields']['rms_prevjump_calendar_events'] = array
		(
		    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['rms_prevjump_calendar_events'],
		    'exclude'                 => true,
		    'inputType'               => 'pageTree',
		    'eval'                    => array('fieldType'=>'radio')
		);
		
/**
 * Class tl_content
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_rms_settings extends Backend
{						
	/**
	 * Get all modules and return them as array
	 * @return array
	 */
	public function getModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}						
}						
?>