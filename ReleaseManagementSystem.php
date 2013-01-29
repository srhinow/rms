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
        protected $rm_palettes_blacklist = array('__selector__');	
	
	/**
	* implement Backend - callbacks
	* @var string 
	*/
	public function handleBackendUserAccessControlls($strTable)
	{
	    $this->import("BackendUser");

	    $arrAllowedTables = array('tl_content','tl_newsletter','tl_calendar_events','tl_news');
	    
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
	    
	    //add everytime in allowed tables
	    if((in_array($strTable, $arrAllowedTables)) && ($GLOBALS['TL_CONFIG']['rms_active']))
	    {
		$GLOBALS['TL_DCA'][$strTable]['config']['onload_callback'][] = array('ReleaseManagementSystem','addRMFields');
		
		
		
		switch($strTable)
		{
		case 'tl_content':
		    $GLOBALS['TL_DCA'][$strTable]['list']['global_operations']['showPreview'] = array                 
			(
				'label'               => &$GLOBALS['TL_LANG'][$strTable]['show_preview'],
				'href'                => 'key=showPreview',
				'class'               => 'browser_preview',
				'attributes'          => 'target="_blank"'
			);
		break;
		default:
		    $GLOBALS['TL_DCA'][$strTable]['list']['operations']['showPreview'] = array                 
			(
				'label'               => &$GLOBALS['TL_LANG'][$strTable]['show_preview'],
				'href'                => 'key=showPreview',
				'class'               => 'browser_preview',
				'icon'                => 'page.gif',
				'attributes'          => 'target="_blank"'
			);			
		}   
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
                $id = false;
                 
                //region
                switch($this->Input->get('region'))
                {
		    case 'news':
		    case 'newsletter':
		    case 'calendar_events':
			    $typePrefix = 'mod_';			      		    
		    break;
		    default:
		        $typePrefix = 'ce_';
		        
			$objStoredData = $this->Database->prepare("SELECT `data` FROM `tl_rms` WHERE `ref_id`=? AND `ref_table`=?")
							->execute($objElement->id, 'tl_content');
									        
			if ($objStoredData->numRows  == 1)
			{				
			    $objRow = $this->overwriteDbObj($objElement, deserialize($objStoredData->data));		    			
			    $objRow->typePrefix = $typePrefix;
			    $objRow->published = 1;			    
			    $strClass = $this->findContentElement($objRow->type);
			    $objElement = new $strClass($objRow);
			    $strBuffer = $objElement->generate(); 			    
			}						        
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

	    $this->import("BackendUser","User");
	   		    		      
	    if($varValue == 1)
	    {		
                
		//mail from editor to Super-Editor (question)
		if(stristr($GLOBALS['TL_CONFIG']['rms_sender'],$this->BackendUser->email) === false)
		{		    

		    
		    $email = new Email();
		    $email->from = $this->BackendUser->email;
		    $email->charset = 'utf-8';
		    $email->subject = 'Freigabe-Aufforderung';
		    $email->text = $dc->Input->post('rms_notice');
		    $email->sendTo(($GLOBALS['TL_CONFIG']['rms_sender']) ? $GLOBALS['TL_CONFIG']['rms_sender'] : $GLOBALS['TL_CONFIG']['adminEmail']);
		}
		else
		//send Email from Super-Editor to editor  (answer)
		{
		    //get the author-email from this change
		    $lastEditorObj = $this->Database->prepare('SELECT * FROM `tl_user` WHERE `id`=?')
		    ->limit(1)
		    ->execute($this->Input->get('author'));		                

		    if(!$lastEditorObj->email) return;
                	
		    $email = new Email();
		    $email->from = $this->BackendUser->email;
		    $email->charset = 'utf-8';
		    $email->subject = 'Freigabe-Aufforderung (Antwort)';
		    $email->text = $dc->Input->post('rms_notice');
		    $email->sendTo($lastEditorObj->email);		
		}
	    }
	    
	    //disable everytime sendEmail 		      
	    $this->Database->prepare('UPDATE `'.$strTable.'` SET `rms_release_info`="" WHERE `id`=?')->execute($dc->id);		   	     
	    
	    return '';	     	     
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
	    $dc->setActiveRecord();
	    
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
		                               
		//correct time-data
		switch($this->Input->get('table')) 
		{
		    case 'tl_calendar_events': $data = $this->adjustTimeCalEvents($data); break;                 
		    case 'tl_news': $data = $this->adjustTimeNews($data); break;                                
		}                                
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
		$isNewEntryObj = $this->Database->prepare('SELECT count(*) c FROM `'.$this->Input->get("table").'` WHERE `id`=? AND `tstamp`=?')
						->limit(1)
						->execute($this->Input->get("id"),0);

		if ((int) $isNewEntryObj->c == 1)
		{
		     $data['tstamp'] = time();
		     $data['rms_first_save'] = 1;
		     $objUpdate = $this->Database->prepare("UPDATE ".$this->Input->get("table")." %s WHERE id=?")->set($data)->execute($this->Input->get("id"));
		}
		
	}
	/**
	* custom adjustTime for tl_calendar_events
	* @var array
	*/
	protected function adjustTimeCalEvents($data = array())
	{
	   if(count($data) < 1)
	   {
	      return; 
	   }
	   
	    $arrSet['startTime'] = $data['startDate'];
	    $arrSet['endTime'] = $data['startDate'];

	    // Set end date
	    if (strlen($data['endDate']))
	    {
		    if ($data['endDate'] > $data['startDate'])
		    {
			    $arrSet['endDate'] = $data['endDate'];
			    $arrSet['endTime'] = $data['endDate'];
		    }
		    else
		    {
			    $arrSet['endDate'] = $data['startDate'];
			    $arrSet['endTime'] = $data['startDate'];
		    }
	    }
	    // Add time
	    if ($data['addTime'])
	    {
		    $arrSet['startTime'] = strtotime(date('Y-m-d', $arrSet['startTime']) . ' ' . date('H:i:s', $data['startTime']));
		    $arrSet['endTime'] = strtotime(date('Y-m-d', $arrSet['endTime']) . ' ' . date('H:i:s', $data['endTime']));	    
	    }

	    // Adjust end time of "all day" events
	    elseif ((strlen($data['endDate']) && $arrSet['endDate'] == $arrSet['endTime']) || $arrSet['startTime'] == $arrSet['endTime'])
	    {
		    $arrSet['endTime'] = (strtotime('+ 1 day', $arrSet['endTime']) - 1);
	    }

	    $arrSet['repeatEnd'] = 0;

	    if ($data['recurring'])
	    {
		    $arrRange = deserialize($data['repeatEach']);

		    $arg = $arrRange['value'] * $data['recurrences'];
		    $unit = $arrRange['unit'];

		    $strtotime = '+ ' . $arg . ' ' . $unit;
		    $arrSet['repeatEnd'] = strtotime($strtotime, $arrSet['endTime']);
	    }

            $data = array_merge($data,$arrSet);

            return $data;
	   
	}
	
	/**
	* custom adjustTime for tl_news
	* @var array
	*/
	protected function adjustTimeNews($data = array())
	{
	    // Return if there is no active record (override all)
	    if(count($data) < 1)
	    {
		return; 
	    }

	    $arrSet['date'] = strtotime(date('Y-m-d', $data['date']) . ' ' . date('H:i:s', $data['time']));
	    $arrSet['time'] = $arrSet['date'];

            $data = array_merge($data,$arrSet);

            return $data;
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
	   $this->redirect($this->getPreviewLink());
	}
	
	/**
	* get Preview Link-Date
	*/
	 public function getPreviewLink()
	 {
	 	
	 	$return = array();
	 	
	 	switch($this->Input->get('table'))
		{
		case 'tl_content':    
		
		    $pageObj = $this->Database->prepare('SELECT `p`.* FROM `tl_page` `p` 
		    LEFT JOIN `tl_article` `a` ON `p`.`id`=`a`.`pid`
		    LEFT JOIN `tl_content` `c` ON `a`.`id`=`c`.`pid`
		    WHERE `c`.`id`=?')
				    ->limit(1)
				    ->execute($this->Input->get('id'));
				    
                    if($pageObj->numRows > 0) $strUrl = $this->generateFrontendUrl($pageObj->row(),'/do/preview');
		    $strPreviewUrl = $this->Environment->base.$strUrl;
		    
		break;		    
		case 'tl_newsletter':    		    
		        
		    //get Preview-Link
		    if($GLOBALS['TL_CONFIG']['rms_prevjump_newsletter'])
		    {
			$objJumpTo = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1" : ""))
										->limit(1)
										->execute($GLOBALS['TL_CONFIG']['rms_prevjump_newsletter']);
    
			if ($objJumpTo->numRows)
			{
				$strUrl = $this->generateFrontendUrl($objJumpTo->fetchAssoc(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ?  '/%s' : '/do/preview/items/%s'));
			}                  
		    }
		    
		    //get Link-Title
		    $pageObj = $this->Database->prepare('SELECT * FROM `tl_newsletter` WHERE `id`=?')
					      ->limit(1)
					      ->execute($this->Input->get('id'));
		    
		    $strPreviewUrl = sprintf($strUrl, $pageObj->alias);
		    
		break;		    
		case 'tl_calendar_events':
		    
		    if($GLOBALS['TL_CONFIG']['rms_prevjump_calendar_events'])
		    {
			$objJumpTo = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1" : ""))
										->limit(1)
										->execute($GLOBALS['TL_CONFIG']['rms_prevjump_calendar_events']);
    
			if ($objJumpTo->numRows)
			{
				$strUrl = $this->generateFrontendUrl($objJumpTo->fetchAssoc(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ?  '/%s' : '/do/preview/events/%s'));
			}                  
		    }
		    
		    //get Link-Title
		    $pageObj = $this->Database->prepare('SELECT * FROM `tl_calendar_events` WHERE `id`=?')
					      ->limit(1)
					      ->execute($this->Input->get('id'));
					      		    	
		    $strPreviewUrl = sprintf($strUrl, $pageObj->alias);	
		    
		break;			    
		case 'tl_news':

		    if($GLOBALS['TL_CONFIG']['rms_prevjump_news'])
		    {
			$objJumpTo = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1" : ""))
										->limit(1)
										->execute($GLOBALS['TL_CONFIG']['rms_prevjump_news']);
    
			if ($objJumpTo->numRows)
			{
			    $strUrl = $this->generateFrontendUrl($objJumpTo->fetchAssoc(), ($GLOBALS['TL_CONFIG']['useAutoItem'] ?  '/%s' : '/do/preview/items/%s'));			
			}                  
		    }
		    
		    //get Link-Title
		    $pageObj = $this->Database->prepare('SELECT * FROM `tl_news` WHERE `id`=?')
					      ->limit(1)
					      ->execute($this->Input->get('id'));
					      
		    $strPreviewUrl = sprintf($strUrl, $pageObj->alias);					      		    		
		    break;		    		    		    		    		    		    
		}
				
		return $strPreviewUrl;		
		
	 }
	
}
