<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingModelSendRewards extends JModelList {
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'reward.id',
				'reward.amount',
				'reward.from_user_id',
				'reward.to_user_id',
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'reward.id', $direction = 'DESC') {
		$app = JFactory::getApplication();
	
		// List state information
		$value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$orderCol = $app->input->get('filter_order', 'reward.id');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'reward.id';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'DESC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}
		$this->setState('list.direction', $listOrder);		
		
		//parent::populateState($orderCol, $listOrder);
	}
	
	protected function getStoreId($id = '') {
		$id .= ':' . $this->getState('filter.search');
		return parent::getStoreId($id);
	}
	
	protected function getListQuery() {
		$query = $this->_db->getQuery(true);
		
		$query->select($this->getState('list.select', 'reward.*, users.name, users.username'));
		$query->from('#__dogecointipping_reward AS reward');
        $query->join('LEFT', '#__users AS users ON reward.to_user_id = users.id');
        $user = JFactory::getUser();
        $query->where('reward.from_user_id = ' . $user->id);		
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('reward.id = ' . (int) substr($search, 3));
			} else {
				$search = $this->_db->Quote('%' . $this->_db->escape($search) . '%');
				$query->where('(reward.desc LIKE ' . $search . ")");
			}
		}
		
		$query->order($this->getState('list.ordering', 'reward.id') . ' ' . $this->getState('list.direction', 'DESC'));
		return $query;
	}

}
