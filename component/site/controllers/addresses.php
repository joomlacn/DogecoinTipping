<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingControllerAddresses extends JControllerAdmin {
	
	protected $text_prefix = 'COM_DOGECOINTIPPING_ADDRESSES';
	
	public function getModel($name = 'Address', $prefix = 'DogecoinTippingModel', $config = array('ignore_request' => false)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}