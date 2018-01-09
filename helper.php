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

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.utilities.utility' );

abstract class modMyAnniversaryHelper
{   
   public static function getList(&$params)
   {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_content'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
   
		// Get the dbo
		$db =   JFactory::getDbo();		

		global $mainframe;
			
		$user		= JFactory::getUser();
		$count		= (int) $params->get('count', 5);
		$catid		= trim( $params->get('catid') );		
		
		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels($user->get('id'));
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		
		// Ordering
		$ordering   = (int) $params->get( 'ordering', 0 );
		switch ($ordering)
		{
			case 1:
				$ordering		= 'a.modified DESC, a.created DESC';
				break;
			case 0:
			default:
				$ordering		= 'a.created DESC';
				break;
		}
		
		if ($catid)
		{
			$ids = explode( ',', $catid );
			JArrayHelper::toInteger( $ids );
			$catCondition = ' AND (c.id=' . implode( ' OR c.id=', $ids ) . ')';
		}		
		else
			$catCondition ='';
		
		$where		= 'a.state = 1 AND c.published = 1';
		
		$currdate=JHtml::date('now','Y-m-d H:i:s','UTC');
		$yesterday=JHtml::date('-1 day','Y-m-d H:i:s','UTC');
		$currYear=JHtml::date('now','Y','UTC');
		
		// Content Items only
		$query = 'SELECT b.*,'
				. ' YEAR(b.create_rel)-YEAR(b.created) as years'
				. ' FROM ('
				. ' SELECT a.*,'
				. ' c.alias AS category_alias,'
				. ' DATE_FORMAT('
					. 'CONCAT_WS( 	 " ",'
					.				'CONCAT_WS( "-", "'.$currYear.'", month(a.created), day(a.created) ),'
								.	'CONCAT_WS( ":", hour(a.created), minute(a.created), second(a.created) )'
							.  '),'
					.' "%Y-%m-%d %H:%i:%s"'
							. ') as create_rel,'
				. ' CASE WHEN CHAR_LENGTH( a.alias ) THEN CONCAT_WS( ":", a.id, a.alias ) ELSE a.id END AS slug,'
				. ' CASE WHEN CHAR_LENGTH( c.alias) THEN CONCAT_WS( ":", c.id, c.alias) ELSE c.id END as catslug'
				. ' FROM `#__content` AS a'
				. ' INNER JOIN `#__categories` AS c ON c.id = a.catid'
				. ' WHERE '.$where
				. ' AND a.access IN ('.$groups.')'
				. ' AND c.access IN ('.$groups.')'
				. $catCondition				
				. ' ORDER BY '.$ordering.' ) b'
				. ' WHERE b.create_rel BETWEEN str_to_date("'.$yesterday.'","%Y-%m-%d %H:%i:%s") AND str_to_date("'.$currdate.'","%Y-%m-%d %H:%i:%s")';		
		
		$db->setQuery($query, 0, $count);
		$items = $db->loadObjectList();					

		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			} else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}						
		}

		return $items;
	}
}
?>