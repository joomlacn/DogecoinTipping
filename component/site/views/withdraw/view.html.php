<?php
/**
 * @copyright Copyright &copy; 2015
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 * @author joomla.cn
 * @link http://www.joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingViewWithdraw extends JViewLegacy {
	protected $state;
		
	public function display($tpl = null) {
		$this->state = $this->get('State');
		$app = JFactory::getApplication();
		$this->params	= $app->getParams();
						
		$itemId = $app->input->get('Itemid', 0);
		$itemStr = '';
		if ($itemId != 0)
		{
			$itemStr = '&Itemid=' . $itemId;
		}

		$form = $this->get('Form');
		$this->state = $this->get('State');
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->form = $form;

		$this->_prepareDocument();			
		parent::display($tpl);	
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		$title = JText::_('COM_DOGECOINTIPPING_WITHDRAW_TITLE');
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

			if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}		
	
}
