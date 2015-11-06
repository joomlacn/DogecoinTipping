<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingHelper
{
	public static function getBalance($address)
	{
		$params = JComponentHelper::getParams('com_dogecointipping');
		$apiKey = $params->get('api_key');
		$pin = $params->get('secret_pin');

		require_once(JPATH_ADMINISTRATOR . '/components/com_dogecointipping/libs/block_io.php');
		$block_io = new BlockIO($apiKey, $pin, 2);
		$getBalanceInfo = $block_io->get_balance($address);
		return $getBalanceInfo->data->available_balance;
	}

	public static function getAddressCount()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query
			->select('count(id) AS address_count')
			->from($db->quoteName('#__dogecointipping_address'))
			->where($db->quoteName('user_id') . ' = ' . $user->id);
		$db->setQuery($query);
		$address_count = $db->loadResult();
		return $address_count;
	}

	public static function getRewardCount($article_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query
			->select('count(distinct from_user_id) as reward_count')
			->from($db->quoteName('#__dogecointipping_reward') . ' AS reward')
			->where($db->quoteName('reward.article_id') . ' = ' . $article_id);
		$db->setQuery($query);
		$reward_count = $db->loadResult();
		return $reward_count;
	}
	
	public static function getRewardUsers($article_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query
			->select($db->quoteName('distinct from_user_id'))
			->from($db->quoteName('#__dogecointipping_reward'))
			->where($db->quoteName('article_id') . ' = ' . $db->quote($article_id))
			->order('id DESC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0) {
			$users = array();
			foreach($rows as $row) {
				$query = $db->getQuery(true);
				$query
					->select($db->quoteName(array('jsn_users.avatar', 'users.*')))
					->from($db->quoteName('#__jsn_users', 'jsn_users'))
					->join('INNER', $db->quoteName('#__users', 'users') . ' ON (' . $db->quoteName('jsn_users.id') . ' = ' . $db->quoteName('users.id') . ')')		
					->where($db->quoteName('users.id') . ' =' . $row->from_user_id);
				$db->setQuery($query);
				$result = $db->loadObject();
				$users[] = $result;
			}
			return $users;
		} else {
			return false;
		}
	}

	/**
	 * 得到当前用户的内部账号金额
	 *
	 * @static
	 * @access public
	 * @return double
	 */
	public static function getInlineAmount()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('inline_amount'))
			->from($db->quoteName('#__dogecointipping_address'))
			->where($db->quoteName('user_id') . ' = ' . JFactory::getUser()->id);
		$db->setQuery($query);
		return $db->loadResult();	
	}
}
