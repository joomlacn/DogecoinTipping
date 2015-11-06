<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_dogecointipping'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('dogecointipping', array('default_view'=> 'cpanel'));
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
