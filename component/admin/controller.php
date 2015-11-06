<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingController extends JControllerLegacy
{
	public function display($cacheable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/dogecointipping.php';
		
		$view = $this->input->get('view', 'cpanel');
		$layout = $this->input->get('layout', 'default');
		
		parent::display();
		return $this;
	}
}
