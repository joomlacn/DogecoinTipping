<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingHelper
{
	public static function addSubmenu($name = 'extensions')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_DOGECOINTIPPING_SUBMENU_CPANEL'),
			'index.php?option=com_dogecointipping&view=cpanel',
			$name == 'cpanel'
		);
	}
	
	public static function getActions($categoryId = 0)
	{
		$user = JFactory::getUser();
		$result = new JObject();

		if (empty($categoryId))
		{
			$assetName = 'com_dogecointipping';
			$level = 'component';
		}
		else
		{
			$assetName = 'com_dogecointipping.category.'.(int) $categoryId;
			$level = 'category';
		}
		
		$actions = JAccess::getActions('com_dogecointipping', $level);
		
		foreach($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
		
		return $result;
	}

}
