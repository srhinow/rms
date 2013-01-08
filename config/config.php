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

require_once(TL_ROOT.'/system/config/localconfig.php');
 
if($GLOBALS['TL_CONFIG']['rms_active']) 
{
    array_insert($GLOBALS['BE_MOD']['content'], 1, array
    (
    'rms' => array (
	    'tables' => array('tl_rms'),
	    'icon'  => 'system/modules/rms/html/promotion.png',
	    'stylesheet' => 'system/modules/rms/html/be.css',
	    'acknowledge' => array('ReleaseManagementSystem','acknowdlgeEntry'),	    
	    )
    ));
    
    $GLOBALS['BE_MOD']['content']['article']['showPreview'] = array('ReleaseManagementSystem', 'showPreviewInBrowser');
    $GLOBALS['BE_MOD']['content']['article']['stylesheet'] = 'system/modules/rms/html/be.css';
    
    
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('ReleaseManagementSystem', 'handleBackendUserAccessControlls');
    $GLOBALS['TL_HOOKS']['getContentElement'][] = array('ReleaseManagementSystem', 'previewContentElement');
}

$GLOBALS['FE_MOD']['news']['newsreader_rms'] = 'ModuleNewsReaderRMS';
$GLOBALS['FE_MOD']['newsletter']['nl_reader_rms'] = 'ModuleNewsletterReaderRMS';
$GLOBALS['FE_MOD']['events']['eventreader_rms'] = 'ModuleEventReaderRMS';