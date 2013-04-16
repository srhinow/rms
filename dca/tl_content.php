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
 * Table tl_content
 */
if($GLOBALS['TL_CONFIG']['rms_active']) 
{
    $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('ReleaseManagementSystem','addRMFields');
    $GLOBALS['TL_DCA']['tl_content']['list']['operations']['toggle']['button_callback'] = array('tl_rms_content','toggleIcon');    
    $GLOBALS['TL_DCA']['tl_content']['list']['operations']['showPreview'] = array                 
    (
				'label'               => &$GLOBALS['TL_LANG']['tl_content']['show_preview'],
				'href'                => 'key=showPreview',
				'class'               => 'browser_preview',
				'icon'                => 'page.gif',
				'attributes'          => 'target="_blank"',
				'button_callback' => array('tl_rms_content','checkPreviewIcon')				    
    );
      
    $GLOBALS['TL_DCA']['tl_content']['fields']['rms_notice'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rms_notice'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false, 'rte'=>FALSE)
		);
    $GLOBALS['TL_DCA']['tl_content']['fields']['rms_release_info'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content']['rms_release_info'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'save_callback' => array
			(
				array('ReleaseManagementSystem', 'sendEmailInfo')
			)
		);
}
		
/**
 * Class tl_rms_calendar_events
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Controller
 */
class tl_rms_content extends Backend
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
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
	$this->import('Database');
	
	//test rms
	$rmsObj = $this->Database->prepare('SELECT * FROM `tl_rms` WHERE `ref_table`=? AND `ref_id`=?')
				 ->execute('tl_content',$row['id']);
				 
	if($rmsObj->numRows > 0)
	{ 
	return '';
	}
	else 
	{
		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_content::invisible', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row['invisible'];

		if ($row['invisible'])
		{
			$icon = 'invisible.gif';
		}		

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}
   }


	/**
	 * Toggle the visibility of an element
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnVisible)
	{
		// Check permissions to edit
		$this->Input->setGet('id', $intId);
		$this->Input->setGet('act', 'toggle');
		$this->checkPermission();

		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_content::invisible', 'alexf'))
		{
			$this->log('Not enough permissions to show/hide content element ID "'.$intId.'"', 'tl_content toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->createInitialVersion('tl_content', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_content']['fields']['invisible']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_content']['fields']['invisible']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_content SET tstamp=". time() .", invisible='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_content', $intId);
		$this->log('A new version of record "tl_content.id='.$intId.'" has been created'.$this->getParentRecords('tl_content', $intId), 'tl_content toggleVisibility()', TL_GENERAL);
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
        $previewLink = $this->ReleaseManagementSystem->getPreviewLink($row['id'],'tl_content');
        
        //test rms
        $rmsObj = $this->Database->prepare('SELECT * FROM `tl_rms` WHERE `ref_table`=? AND `ref_id`=?')
				 ->execute('tl_content',$row['id']);

        if($rmsObj->numRows > 0) return '<a href="'.$previewLink.'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
        else return '';
        
    }
           
}