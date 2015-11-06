<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingModelAddress extends JModelAdmin {
	public function getTable($type = 'address', $prefix = 'DogecoinTippingTable', $config = array()) {
		return JTable::getInstance($type, $prefix , $config);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_dogecointipping.address', 'address', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	public function save($data) {
		$result = $this->addAddress($data['label']);
		if(is_array($result)) {
			$data['label'] = $result['label'];
			$data['address'] = $result['address'];
		} else {
			return false;
		}
		parent::save($data);
		return true;
	}

	public function addAddress($label)
	{
		$params = JComponentHelper::getParams('com_dogecointipping');
		$apiKey = $params->get('api_key');
		$pin = $params->get('secret_pin');

		require_once(JPATH_ADMINISTRATOR . '/components/com_dogecointipping/libs/block_io.php');
		$block_io = new BlockIO($apiKey, $pin, 2);
		try {
			if (empty($label)) {
				$getNewAddressInfo = $block_io->get_new_address();
			} else {
				$getNewAddressInfo = $block_io->get_new_address(array('label'=>$label));
			}
			$newAddress = $getNewAddressInfo->data->address;
			$newLabel = $getNewAddressInfo->data->label;
			return array('label'=>$newLabel, 'address'=>$newAddress);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}	
}
