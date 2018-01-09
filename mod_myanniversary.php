<?php
 /**
*
* MyAnniversary tells you about anniversaries of a current day
*
* Copyright (C) 2012-2018 mokhin-tech.ru. All rights reserved. 
*
* Author is:
* Denis Mokhin < denis@mokhin-tech.ru >
* http://mokhin-tech.ru
*
* @license GNU GPL, see http://www.gnu.org/licenses/gpl-2.0.html
* 
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
**/

	// no direct access
	defined('_JEXEC') or die('Restricted access');		
	
	// Include the syndicate functions only once
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php';
	
	$list = modMyAnniversaryHelper::getList($params);
	require(JModuleHelper::getLayoutPath('mod_myanniversary'));