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
 * Table tl_newsletter
 */
require_once(TL_ROOT.'/system/config/localconfig.php');
 
if($GLOBALS['TL_CONFIG']['rms_active']) 
{
    $GLOBALS['TL_DCA']['tl_newsletter']['config']['onload_callback'][] = array('ReleaseManagementSystem','addRMFields'); 
    $GLOBALS['TL_DCA']['tl_newsletter']['list']['operations']['send']['button_callback'] = array('tl_rms_newsletter','checkSendIcon');
    $GLOBALS['TL_DCA']['tl_newsletter']['list']['operations']['showPreview'] = array                 
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events']['show_preview'],
				'href'                => 'key=showPreview',
				'class'               => 'browser_preview',
				'icon'                => 'page.gif',
				'attributes'          => 'target="_blank"',
				'button_callback' => array('tl_rms_newsletter','checkPreviewIcon')
			);

 
    $GLOBALS['TL_DCA']['tl_newsletter']['fields']['rms_notice'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_newsletter']['rms_notice'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false, 'rte'=>FALSE)
		);
    $GLOBALS['TL_DCA']['tl_newsletter']['fields']['rms_release_info'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_newsletter']['rms_release_info'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'save_callback' => array
			(
				array('ReleaseManagementSystem', 'sendEmailInfo')
			)
		);
};

/**
 * Class tl_rms_newsletter
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Controller
 */
class tl_rms_newsletter extends Backend
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
     * Return the "toggle send-button"
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function checkSendIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $this->import('Database');

        //test rms
        $rmsObj = $this->Database->prepare('SELECT * FROM `tl_rms` WHERE `ref_table`=? AND `ref_id`=?')
				 ->execute('tl_newsletter',$row['id']);
        if($rmsObj->numRows > 0) return '';
        else return '<a href="'.$this->addToUrl('id='.$row['id'].'&'.$href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
        
    }  
    /**
     * Return the "toggle preview-button" 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function checkPreviewIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $this->import('Database');
        $this->import('ReleaseManagementSystem');
        $previewLink = $this->ReleaseManagementSystem->getPreviewLink($row['id'],'tl_newsletter'); 
                
        //test rms
        $rmsObj = $this->Database->prepare('SELECT * FROM `tl_rms` WHERE `ref_table`=? AND `ref_id`=?')
				 ->execute('tl_newsletter',$row['id']);
        if($rmsObj->numRows > 0) return '<a href="'.$previewLink.'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
        else return '';
        
    }       

}