<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingModelWithdraw extends JModelAdmin {
	
	public function getTable($type = 'withdraw', $prefix = 'DogecoinTippingTable', $config = array()) {
		return JTable::getInstance($type, $prefix , $config);
	}

	public function getForm($data = array(), $loadData = FALSE) {
		$form = JForm::getInstance('withdraw', JPATH_COMPONENT . '/models/forms/withdraw.xml');
		return $form;
	}
	
}
