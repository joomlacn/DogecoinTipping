<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;

class DogecoinTippingTableWithdraw extends JTable {
	public function __construct($db) {
		parent::__construct('#__dogecointipping_withdraw', 'id', $db);
	}
}