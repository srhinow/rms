<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
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
 * @copyright  Sven Rhinow Webentwicklung 2012 <http://www.sr-tag.de>
 * @author     Stefan Lindecke  <stefan@ktrion.de>
 * @author     Sven Rhinow <kservice@sr-tag.de> 
 * @package    rms (Release Management System)
 * @license    LGPL 
 * @filesource
 */

class ReleaseManagementSystem extends Backend
{
        
        /**
        * release_menagement Fields for defined palettes
        * @var string
        */        
        protected $rm_field_str = ';{rms_legend:hide},rms_notice,rms_release_info';
        
        /**
        * defined blacklist palettes
        * @var array
        */
        protected $rm_palettes_blacklist = array('__selector__','alias','accordionstart','accordionstop','toplink');	
	
	/**
	* implement Backend - callbacks
	* @var string 
	*/
	public function handleBackendUserAccessControlls($strTable)
	{
		$this->import("BackendUser");

		$arrAllowedTables = array('tl_content');
		
		if(!$GLOBALS['TL_CONFIG']['rms_control_group']) $GLOBALS['TL_CONFIG']['rms_control_group'] = 0;
		if(!$GLOBALS['TL_CONFIG']['rms_active']) $GLOBALS['TL_CONFIG']['rms_active'] = false;
		
		if ((in_array($strTable, $arrAllowedTables)) && (!$this->BackendUser->isMemberOf($GLOBALS['TL_CONFIG']['rms_control_group'])  || $this->Input->get("author")) && ($GLOBALS['TL_CONFIG']['rms_active']))
		{
			if ($this->Input->get("act")=="edit")
			{				
			    $GLOBALS['TL_DCA'][$strTable]['config']['dataContainer'] = 'Memory';				
			    $GLOBALS['TL_DCA'][$strTable]['config']['onload_callback'][] = array('ReleaseManagementSystem','onLoadCallback');
			    
			    $GLOBALS['TL_DCA'][$strTable]['config']['onsubmit_callback'][] = array('ReleaseManagementSystem','onSubmitCallback');			    			    
			}								
		}
		
		//add everytime in tl_content
		if(($strTable == 'tl_content')  && ($GLOBALS['TL_CONFIG']['rms_active']))
		{
		    $GLOBALS['TL_DCA'][$strTable]['config']['onload_callback'][] = array('ReleaseManagementSystem','addRMFields');
		   # $GLOBALS['TL_DCA'][$strTable]['config']['onsubmit_callback'][] = array('ReleaseManagementSystem','sendEmailInfo');
		    $GLOBALS['TL_DCA'][$strTable]['list']['global_operations']['showPreview'] = array                 
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content']['show_preview'],
				'href'                => 'key=showPreview',
				'class'               => 'browser_preview',
				'attributes'          => 'target="_blank"'
			);
		}
		
	}
	
	/**
	* Frontend-Preview from not released Content if set get-Parameter 'do=preview'
	* HOOK: getContentElement
	* @var object
	* @var string
	* @return string
	*/
	public function previewContentElement(Database_Result $objElement, $strBuffer)
	{
	    if($this->Input->get('do') == 'preview' || $this->Input->get('do') == 'article')
	    {
		$objStoredData = $this->Database->prepare("SELECT `data` FROM `tl_rms` WHERE `ref_id`=? AND `ref_table`=?")->execute(
								    $objElement->id,
								    'tl_content');
		if ($objStoredData->numRows==1)
		{	
		    $objRow = $this->overwriteDbObj($objElement, deserialize($objStoredData->data));		    
		    $objRow->typePrefix = 'ce_';
		    $strClass = $this->findContentElement($objRow->type);
		    $objElement = new $strClass($objRow);
		    $strBuffer = $objElement->generate();		    
		}								    
	    }
	    else
	    {
		if($objElement->rms_first_save) $strBuffer='';

	    }
	    	    
	    return  $strBuffer;
	}	
	
	/**
	* Overwrite db-object with data-array
	* @var object
	* @var string
	* @return object
	*/
	public function overwriteDbObj(Database_Result $origObj, $newArr)
	{
	    if(is_array($newArr) && count($newArr) > 0)
	    {
 		unset($newArr['id']); 
 		unset($newArr['pid']);
 		
 		foreach($newArr as $k => $v)
 		{
		    $origObj->$k = $v;
 		}
	    }
	    return  $origObj;
	}
	
	/**
	* add RMS-Fields in menny content-elements (DCA)
	* @var object
	*/
	public function addRMFields(DataContainer $dc)
	{
	    $strTable = $this->Input->get("table");
	    
	    //add Field in meny content-elements
            foreach($GLOBALS['TL_DCA'][$strTable]['palettes'] as $name => $field)
            {
		if(in_array($name,$this->rm_palettes_blacklist)) continue;

		$GLOBALS['TL_DCA'][$strTable]['palettes'][$name] .=  $this->rm_field_str;
            }
            	
	}
	
	/**
	* send Email for new release if Checkbox selected
	* @var object
	*/
	public function sendEmailInfo($varValue, DataContainer $dc)
	{
	    $strTable = $this->Input->get("table");

	    $this->import("BackendUser");
	    			      
	    if($varValue == 1)
	    {
		//send Email
		$email = new Email();
		$email->from = $this->BackendUser->email;
		$email->fromName = $this->BackendUser->email;
		$email->charset = 'utf-8';
		$email->subject = 'Freigabe-Aufforderung';
		$email->text = $dc->Input->post('rms_notice');
		$email->sendTo(($GLOBALS['TL_CONFIG']['rms_sender']) ? $GLOBALS['TL_CONFIG']['rms_sender'] : $GLOBALS['TL_CONFIG']['adminEmail']);
	    }
	    
	    //disable everytime sendEmail 		      
	    $this->Database->prepare('UPDATE `tl_content` SET `rms_release_info`="" WHERE `id`=?')->execute($dc->id);		   	     
	     	     
	}
	
	/**
	* overwrite DCA-values if current BackendUser a low-level-redakteur
	* @var object
	*/
	public function onLoadCallback(DataContainer $dc)
	{
	    $this->import("BackendUser");
	    
	    $strTable = $this->Input->get("table");
	    
	    // dont new if super_redacteure
	    $userID =  ($this->Input->get("author")) ? $this->Input->get("author") :  $this->BackendUser->id;
	    
	    $objStoredData = $this->Database->prepare("SELECT data FROM tl_rms WHERE ref_id=? AND ref_table=? AND ref_author=?")->execute(
										$this->Input->get("id"),
										$strTable,
										$userID);
			
	    if ($objStoredData->numRows > 0)
	    {		    
		$dc->setDataArray(deserialize($objStoredData->data));
	    }
	    else
	    {
		    
		$objData = $this->Database->prepare("SELECT * FROM ".$strTable." WHERE id=?")->limit(1)->execute($this->Input->get("id"));
		    
		$arrData = $objData->fetchAllAssoc();
		    
		$dc->setDataArray($arrData[0]);
	    }
	    
	}
	/**
	* set / update a entry in rms-table
	* @var object
	*/
	public function onSubmitCallback(DataContainer $dc)
	{
		$this->import("BackendUser");
		
		// dont new if super_redacteure
		$userID =  ($this->Input->get("author")) ? $this->Input->get("author") :  $this->BackendUser->id;
		
		$objData = $this->Database->prepare("SELECT id FROM tl_rms WHERE ref_id=? AND ref_table=? AND ref_author=?")->execute(
										$this->Input->get("id"),
										$this->Input->get("table"),
										$userID);
		$data = $dc->getDataArray();
		                                
		$arrSubmitData = array(
				'tstamp' => time(),
				'ref_id' => $this->Input->get("id"),
				'ref_table' =>  $this->Input->get("table"),
				'ref_author' => $userID,
				'ref_notice' => $data['rms_notice'],
				'data'=> $data			
			);	
			
			
		if ($objData->numRows==1)
		{
			$this->Database->prepare("UPDATE tl_rms %s WHERE id=?")->set($arrSubmitData)->execute($objData->id);
		}
		else
		{
			$this->Database->prepare("INSERT INTO tl_rms %s")->set($arrSubmitData)->execute();
		}
		
		// create / first-save
		if ($this->Input->get("mode") == 1)
		{
		     $data['tstamp'] = time();
		     $data['rms_first_save'] = 1;
		     $objUpdate = $this->Database->prepare("UPDATE ".$this->Input->get("table")." %s WHERE id=?")->set($data)->execute($this->Input->get("id"));
		}
		
	}
	
	
	/**
	* overwrite the old entry if entry acknowdlge
	* @var object
	*/
	public function acknowdlgeEntry(DataContainer $dc)
	{
		$objData = $this->Database->prepare("SELECT data,ref_table,ref_id FROM tl_rms WHERE id=?")->limit(1)->execute($dc->id);
	
		$arrData = deserialize($objData->data);
		if(is_array($arrData) && count($arrData)>0)
		{
		    unset($arrData['id']);
		    unset($arrData['pid']);
		    $arrData['rms_notice'] = '';
		    $arrData['rms_release_info'] = '';
		    $arrData['rms_first_save'] = '';
		    $arrData['tstamp'] = time();	    
		    
		    $objUpdate = $this->Database->prepare("UPDATE ".$objData->ref_table." %s WHERE id=?")->set($arrData)->execute($objData->ref_id);
				    
		    $this->Database->prepare("DELETE FROM tl_rms WHERE id=?")->execute($dc->id);
                }
		$this->redirect(str_replace('&key=acknowledge', '', $this->Environment->request));
		
	}
	
	/**
	* open Frontend with nooutorised Content
	*/
	public function showPreviewInBrowser()
	{
	    $contentId = $this->Input->get('id');
	    
	    if($contentId)
	    {
		$this->import('Database');
		$pageObj = $this->Database->prepare('SELECT `p`.* FROM `tl_page` `p` 
		LEFT JOIN `tl_article` `a` ON `p`.`id`=`a`.`pid`
		WHERE `a`.`id`=?')
					  ->limit(1)
					  ->execute($contentId);
					  
		if($pageObj->numRows > 0) $strUrl = $this->generateFrontendUrl($pageObj->row(),'/do/preview');
		  
		$this->redirect($strUrl);			  
					  
	    }
	}
	
	
}