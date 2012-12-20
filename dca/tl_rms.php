<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');
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
 * Table tl_rms
 */
$GLOBALS['TL_DCA']['tl_rms'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'closed'                      => true,
		'notEditable'                 => true
	),

	// List
	'list'  => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('tstamp DESC', 'id DESC'),
			'panelLayout'             => 'filter;sort,search,limit',
		),
		'label' => array
		(
			'fields'                  => array('tstamp', 'ref_author','ref_table','ref_notice'),
			'label_callback'   => array('tl_rms', 'listRecipient'),

		),
		'global_operations' => array
		(
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
				'label'               => &$GLOBALS['TL_LANG']['tl_article']['edit'],
				'href'                => 'do=article&table=tl_content&act=edit',
				'icon'                => 'edit.gif',
				'button_callback'     => array('tl_rms', 'editArticle'),
				'attributes'          => 'class="contextmenu"'
			),			
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'acknowledge' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rms']['acknowledge'],
				'href'                => 'key=acknowledge',
				'icon'                => 'ok.gif',
			)
		)
	),

	// Fields
	'fields' => array
	(
		'tstamp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rms']['tstamp'],
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 6
		),
		'ref_table' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rms']['ref_table'],
			'filter'                  => false,
			'sorting'                 => true,
			'reference'               => &$GLOBALS['TL_LANG']['tl_rms']
		),
		'ref_author' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rms']['ref_author'],
			'filter'                  => true,
			'foreignKey'				=> 'tl_user.username',
			'sorting'                 => true
		),
		'ref_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rms']['ref_id'],
			'search'                  => false,
			'filter'                  => false,
			'sorting'                 => true
		),
		'ref_notice' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rms']['ref_notice'],
			'search'                  => true
		),		
		'data' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rms']['data'],
			'search'                  => false
		),

	)
);
class tl_rms extends Backend
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
	 * List a recipient
	 * @param array
	 * @return string
	 */
	public function listRecipient($row)
	{
		$this->import('Database');
		
		$userObj = $this->Database->prepare('SELECT * FROM `tl_user` WHERE `id`=?')
		               ->limit(1)
			       ->execute($row['ref_author']);
		
		$pageObj = $this->Database->prepare('SELECT `p`.* FROM `tl_page` `p` 
		LEFT JOIN `tl_article` `a` ON `p`.`id`=`a`.`pid`
		LEFT JOIN `tl_content` `c` ON `a`.`id`=`c`.`pid`
		WHERE `c`.`id`=?')
				->limit(1)
				->execute($row['ref_id']);			  
		if($pageObj->numRows > 0) $strUrl = $this->generateFrontendUrl($pageObj->row(),'/do/preview');
// 		print_r($row);
		
		$label = '
		<strong>Vorchau-Link: </strong><a href="'.$this->Environment->base.$strUrl.'" target="_blank">'.$pageObj->title.'</a><br>
		<strong>Author:</strong> '.$userObj->name.' ('.$userObj->email.')<br>
		<strong>Änderungs-Notiz:</strong> '.nl2br($row['ref_notice']);
		


		return sprintf('<div style="float:left">%s</div>',$label) . "\n";
	}

	/**
	 * Return the edit article button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function editArticle($row, $href, $label, $title, $icon, $attributes)
	{
		$objPage = $this->Database->prepare("SELECT * FROM tl_content WHERE id=?")
								  ->limit(1)
								  ->execute($row['ref_id']);

		return  '<a href="'.$this->addToUrl($href.'&amp;id='.$objPage->id).'&amp;author='.$row['ref_author'].'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}	
}	
