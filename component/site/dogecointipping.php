<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

$controller = JControllerLegacy::getInstance('DogecoinTipping');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
