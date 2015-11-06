<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

class com_DogecoinTippingInstallerScript
{
	public function install()
	{
		echo '<p>' . JText::_('COM_DOGECOINTIPPING_INSTALL_PROMPT') . '</p>';
	}
	
	public function update()
	{
		echo '<p>' . JText::_('COM_DOGECOINTIPPING_UPDATE_PROMPT') . '</p>';
	}
	
	public function uninstall()
	{
		echo '<p>' . JText::_('COM_DOGECOINTIPPING_UNINSTALL_PROMPT') . '</p>';
	}
}
