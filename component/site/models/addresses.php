<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingModelAddresses extends JModelList {
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'address.id',
				'address.label',
				'address.address',
				'address.created',
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'address.id', $direction = 'DESC') {
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

		$orderCol = $app->input->get('filter_order', 'address.id');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'address.id';
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
		$user = JFactory::getUser();
		$query = $this->_db->getQuery(true);
		$query
			->select($this->getState('list.select', 'address.*'))
			->from($this->_db->quoteName('#__dogecointipping_address', 'address'))
        	->join('LEFT', '#__users AS users ON address.user_id = users.id')
			->where($this->_db->quoteName('address.user_id') . ' = ' . $user->id);		
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('address.id = ' . (int) substr($search, 3));
			} else {
				$search = $this->_db->Quote('%' . $this->_db->escape($search) . '%');
				$query->where('(address.label LIKE ' . $search . ' OR address.address LIKE ' . $search . ")");
			}
		}
		
		$query->order($this->getState('list.ordering', 'address.id') . ' ' . $this->getState('list.direction', 'DESC'));
		return $query;
	}

	public function getItems()
	{
		$items = parent::getItems();
		require_once(JPATH_COMPONENT . '/helpers/dogecointipping.php');
		foreach($items as $row) {
			$row->balance = DogecoinTippingHelper::getBalance(array('addresses'=>$row->address));
		}
		return $items;
	}
}
