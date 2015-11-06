<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingTableAddress extends JTable {
	public function __construct($db) {
		parent::__construct('#__dogecointipping_address', 'id', $db);
	}
    
    public function store($updateNulls = false)
    {
        $date   = JFactory::getDate();
        $user   = JFactory::getUser();
        if ($this->id) {

        } else {
            if (!intval($this->created)) {
                $this->created = $date->toSql();
            }
            $this->user_id = $user->id;
        }

        // Attempt to store the user data.
        return parent::store($updateNulls);
    }
}
