<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingControllerSync extends JControllerAdmin {
	/**
	 * 为网站中每个已经存在的用户初始化一个钱包地址
	 * 
	 * @access public
	 * @return void
	 */
	public function sync()
	{
		require_once JPATH_COMPONENT . '/helpers/dogecointipping.php';

		$users = $this->getUsers();
		$syncCount = 0;
		foreach($users as $user) {
			if(!$this->hasAddress($user->id)) {
				$this->addAddress($user->id);
				$syncCount++;
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=com_dogecointipping'), sprintf(JText::_('COM_DOGECOINTIPPING_SYNC_SYNC_PROMPT'), $syncCount));
	}
	
	/**
	 * 得到全部用户ID号
	 * 
	 * @access public
	 * @return array
	 */
	public function getUsers()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__users'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * 判断用户是不是已经有一个钱包地址
	 * 
	 * @access public
	 * @param int $user_id
	 */
	public function hasAddress($user_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__dogecointipping_address'))
			->where($db->quoteName('user_id') . ' = ' . $user_id);
		$db->setQuery($query);
		$result = $db->loadResult();
		if($result) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	

	/**
	 * add a wallet address to a user
	 * 
	 * @access public
	 * @param int $user_id
	 * @param string $label
	 */
	public function addAddress($user_id, $label = '')
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
			$db = JFactory::getDBO();
			$record = new stdClass();
			$record->user_id = $user_id;
			$date   = JFactory::getDate();
			$record->created = $date->toSql();
			$record->label = $newLabel;
			$record->address = $newAddress;
			$db->insertObject('#__dogecointipping_address', $record);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
}
