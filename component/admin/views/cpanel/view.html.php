<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingViewCpanel extends JViewLegacy
{
	public function display($tpl = null)
	{
		DogecoinTippingHelper::addSubmenu('cpanel');
		
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('\n', $errors));
			return false;
		}
		
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = DogecoinTippingHelper::getActions();
		$user  = JFactory::getUser();
		$bar = JToolBar::getInstance('toolbar');
		
		JToolbarHelper::title(JText::_('COM_DOGECOINTIPPING_CPANEL'));

		if($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_dogecointipping');
		}

		JHtmlSidebar::setAction('index.php?option=com_dogecointipping&view=cpanel');
	}
}
