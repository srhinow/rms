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
 * PHP version 5
 * @copyright  Sven Rhinow 2013
 * @author     Sven Rhinow <http://www.sr-tag.de>
 * @package    BBK (BilderBuchKino)
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_bbk_properties
 */
$GLOBALS['TL_DCA']['tl_rms_settings'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => false,
		'onload_callback' => array
		(
		    array('tl_rms_settings', 'create_property_entry'),
// 			array('tl_rms_settings', 'checkPermission')
		),
		'onsubmit_callback' => array
		(
// 			array('tl_rms_settings', 'sendReservationEmail')
		)		
	
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('mediatyp'),
			'flag'                    => 12,
			'panelLayout'             => 'filter;search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title', 'author'),
			'format'                  => '%s (%s)',		
		),
		'global_operations' => array
		(
			'settings' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms_settings']['settings'],
				'href'                => 'table=tl_bbk_properties&act=edit',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			),			
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)			
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms_settings']['edit'],
				'href'                => 'table=tl_story_book_cinema&act=edit',
				'icon'                => 'edit.gif',
				'attributes'          => 'class="contextmenu"'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms_settings']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
// 				'button_callback'     => array('story_book_cinema', 'editHeader'),
				'attributes'          => 'class="edit-header"'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms_settings']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms_settings']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			)			
			
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'   => 'control_group,whitelist_domains,release_tables,sender_email,prevjump_newsletter,prevjump_news,prevjump_calendar_events',

	),

	// Fields
	'fields' => array
	(	    
	    
	    'control_group' => array
	    (
		'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['control_group'],
		'exclude'                 => true,
		'inputType'               => 'radio',
		'foreignKey'              => 'tl_user_group.name',
		'eval'                    => array('multiple'=>false)
	    ),
	    'whitelist_domains' => array
	    (
		'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['whitelist_domains'],
		'exclude'                 => true,
		'inputType'               => 'checkboxWizard',		
		'options_callback'        => array('tl_rms_settings', 'getRootPages'),
		'eval'                    => array('multiple'=>true,'decodeEntities'=>true)
	    ),	    
	    'release_tables' => array
	    (
		'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['release_tables'],
		'inputType'               => 'checkbox',
		'options_callback'        => array('tl_rms_settings', 'getReleaseTables'),
		'eval'                    => array('multiple'=>true)
	    ),	
	    'sender_email' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['sender_email'],
		    'exclude'                 => true,
		    'inputType'               => 'text',
		    'eval'                    => array('decodeEntities'=>true, 'tl_class'=>'clr long')
	    ),	        
	    'prevjump_newsletter' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['prevjump_newsletter'],
		    'exclude'                 => true,
		    'inputType'               => 'pageTree',
		    'eval'                    => array('fieldType'=>'radio')
	    ),
	    'prevjump_news' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['prevjump_news'],
		    'exclude'                 => true,
		    'inputType'               => 'pageTree',
		    'eval'                    => array('fieldType'=>'radio')
	    ),
	    'prevjump_calendar_events' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_rms_settings']['prevjump_calendar_events'],
		    'exclude'                 => true,
		    'inputType'               => 'pageTree',
		    'eval'                    => array('fieldType'=>'radio')
	    ),
	)
);


/**
 * Class tl_bbk_properties
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_rms_settings extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Check permissions to edit table tl_bbk_properties
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (!is_array($this->User->calendars) || empty($this->User->calendars))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->calendars;
		}

		$id = strlen($this->Input->get('id')) ? $this->Input->get('id') : CURRENT_ID;

		// Check current action
		switch ($this->Input->get('act'))
		{
			case 'create':
				if (!strlen($this->Input->get('pid')) || !in_array($this->Input->get('pid'), $root))
				{
					$this->log('Not enough permissions to create Event Reservation in channel ID "'.$this->Input->get('pid').'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'edit':
			case 'show':
			case 'copy':
			case 'delete':
			case 'toggle':
				$objRecipient = $this->Database->prepare("SELECT pid FROM tl_bbk_properties WHERE id=?")
											   ->limit(1)
											   ->execute($id);

				if ($objRecipient->numRows < 1)
				{
					$this->log('Invalid Event Reservation ID "'.$id.'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				if (!in_array($objRecipient->pid, $root))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' recipient ID "'.$id.'" of calendar event ID "'.$objRecipient->pid.'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'select':
			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				if (!in_array($id, $root))
				{
					$this->log('Not enough permissions to access calendar event ID "'.$id.'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				$objRecipient = $this->Database->prepare("SELECT id FROM tl_bbk_properties WHERE pid=?")
											 ->execute($id);

				if ($objRecipient->numRows < 1)
				{
					$this->log('Invalid Event Reservation ID "'.$id.'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				$session = $this->Session->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objRecipient->fetchEach('id'));
				$this->Session->setData($session);
				break;

			default:
				if (strlen($this->Input->get('act')))
				{
					$this->log('Invalid command "'.$this->Input->get('act').'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				elseif (!in_array($id, $root))
				{
					$this->log('Not enough permissions to access Event Reservation ID "'.$id.'"', 'tl_bbk_properties checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}
	
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
	
         /**
         * create an entry if id=1 not exists 
         * @return none
         */
         public function create_property_entry()
         {
	     $testObj = $this->Database->execute('SELECT * FROM `tl_rms_settings`');
	     
	     if($testObj->numRows == 0)
	     {
		 $this->Database->execute('INSERT INTO `tl_rms_settings`(`id`) VALUES(1)');
	     }
         }
         	
	/**
	 * Get all root-Pages and return them as array
	 * @param DataContainer
	 * @return array
	 */
	public function getRootPages(DataContainer $dc)
	{
	    $rootArr = array();
	    $rootPageObj = $this->Database->prepare('SELECT * FROM `tl_page` WHERE `type`=?')->execute('root');
	    
	    if($rootPageObj->numRows > 0)
	    {
		while($rootPageObj->next())
		{
		   $rootArr[$rootPageObj->id] = $rootPageObj->title;		    
		}		
	    }
	    return $rootArr;
	}
	
	/**
	 * Return available rms tables
	 *
	 * @return array Array of tag tables
	 */
	public function getReleaseTables()
	{
		$tables = array();
		foreach ($GLOBALS['rms_extension']['tables'] as $sourcetable)
		{
			$tables[$sourcetable] = $sourcetable;
		}
		return $tables;
	}		
}

?>