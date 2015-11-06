<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */
defined('_JEXEC') or die;

class PlgUserDogecoinTipping extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.2
	 */
	protected $db;
	
	/**
	 * Remove all address for the user name
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was successfully stored in the database
	 * @param   string   $msg      Message
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName('#__dogecointipping_address'))
			->where($this->db->quoteName('user_id') . ' = ' . (int) $user['id']);
		$this->db->setQuery($query)->execute();

		return true;
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method creates a contact for the saved user
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		// If the user wasn't stored we don't create address
		if (!$success)
		{
			return false;
		}

		// If the user isn't new we don't create address
		if (!$isnew)
		{
			return false;
		}

		// Ensure the user id is really an int
		$user_id = (int) $user['id'];

		// If the user id appears invalid then bail out just in case
		if (empty($user_id))
		{
			return false;
		}

		$this->addAddress($user_id);
		return true;
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
