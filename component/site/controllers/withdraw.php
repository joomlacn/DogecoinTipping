<?php
/**
 * @copyright Copyright &copy; 2015
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 * @author joomla.cn
 * @link http://www.joomla.cn
 */

defined('_JEXEC') or die;

class DogecoinTippingControllerWithdraw extends JControllerForm {
	protected $text_prefix = 'COM_DOGECOINTIPPING_WITHDRAW';

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		require_once(JPATH_ADMINISTRATOR . '/components/com_dogecointipping/libs/block_io.php');
		require_once(JPATH_COMPONENT . '/helpers/dogecointipping.php');
		$params = JComponentHelper::getParams('com_dogecointipping');
		$this->apiKey = $params->get('api_key');
		$this->pin = $params->get('secret_pin');
		$this->withdraw_address = $params->get('withdraw_address');
		$this->block_io = new BlockIO($this->apiKey, $this->pin, 2);	
	}
	
	
	public function withdraw()
	{
		$input = JFactory::getApplication()->input;
		$amounts = $input->get('amounts');
		$from_addresses = $this->getAddressById(JFactory::getUser()->id);
		$to_addresses = $input->get('to_addresses');
		try {
    		$returnInfo = $this->doWithdraw($from_addresses, $to_addresses, $amounts);
			$this->setRedirect(JRoute::_('index.php?option=com_dogecointipping&view=withdraw&layout=edit'), $returnInfo->status);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			$this->setRedirect(JRoute::_('index.php?option=com_dogecointipping&view=withdraw&layout=edit'), $e->getMessage());
			return false;
		}
	}	

	public function doWithdraw($from_address, $to_address, $amounts)
	{
		$address_amounts = DogecoinTippingHelper::getBalance(array('addresses'=>$from_address));
		$inline_amounts = $this->getInlineAmounts($from_address);

		$withdraw_address_amount = 0;
		$withdraw_inline_amount = 0;
		$returnInfo = new stdClass();
		if ($address_amounts - 1 >= $amounts) {
			//withdraw from dogecoin address
			try {
				$withdrawInfo = $this->block_io->withdraw(
					array(
						'from_addresses'=>$from_address, 
						'to_addresses' => $to_address, 
						'amounts' => $amounts
						)
				);			
			} catch(Exception $e) {
				throw $e;
			}
			$returnInfo->status = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_BLOCKIO_SUCCESS'), $from_address, $amounts);
			$withdraw_address_amount = $amounts;
		} elseif ($address_amounts > 1 && $address_amounts - 1 + $inline_amounts - 1 >= $amounts) {
			//withdraw from dogecoin address and site inline amount
			try {
				$withdrawInfo = $this->block_io->withdraw(array('from_addresses'=>$from_address, 'to_addresses' => $to_address, 'amounts' => $address_amounts - 1));			
			} catch (Exception $e) {
				throw $e;
			}
			try {
				$this->withdrawInline($this->withdraw_address, $to_address, $amounts - $address_amounts + 1);	
			} catch (Exception $e) {
				throw $e;
			}
			$returnInfo->status = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_BLOCKIO_SUCCESS'), $from_address, ($address_amounts - 1)) . "<br>";
			$returnInfo->status .= sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_INLINE_SUCCESS'), ($amounts - $address_amounts + 1));
			$withdraw_address_amount = $address_amounts - 1;
			$withdraw_inline_amount = $amounts - $address_amounts + 1;
		} elseif ($inline_amounts - 1 >= $amounts) {
			//withdraw from site inline amount
			try {
				$withdrawInfo = $this->withdrawInline($this->withdraw_address, $to_address, $amounts);
			} catch (Exception $e) {
				throw $e;
			}
			$returnInfo->status = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_INLINE_SUCCESS'), $amounts);
			$withdraw_inline_amount = $amounts;
		} else {
			// no money 
			throw new Exception(JText::_('COM_DOGECOINTIPPING_WITHDRAW_NO_ENOUGH_MONEY'));
		}

		$user = JFactory::getUser();
		$date = JFactory::getDate();

		$data = new stdClass();
		$data->user_id = $user->id;
		$data->from_address = $from_address;
		$data->to_address = $to_address;
		$data->amount = $amounts;
		$data->desc = JText::_('COM_DOGECOINTIPPING_WITHDRAW_TITLE');
		$data->address_amount = $withdraw_address_amount;
		$data->inline_amount = $withdraw_inline_amount;
		$data->created = $date->toSql();
		$result = JFactory::getDBO()->insertObject('#__dogecointipping_withdraw', $data);

		return $returnInfo;
	}


	private function withdrawInline($from_address, $to_address, $amount)
	{
		try {
			$withdrawInfo = $this->block_io->withdraw(array('from_addresses'=>$from_address, 'to_addresses' => $to_address, 'amounts' => $amount));			
		} catch (Exception $e) {
			throw $e;
		}
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('inline_amount') . ' = ' . $db->quoteName('inline_amount') . ' - ' . ($amount + 1)
		);
		$conditions = array(
			$db->quoteName('user_id') . ' = ' . $user->id
		);
		$query
			->update($db->quoteName('#__dogecointipping_address'))
			->set($fields)
			->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	private function rewardInline($from_address, $to_address, $amount)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('inline_amount') . ' = ' . $db->quoteName('inline_amount') . ' - ' . ($amount + 1)
		);
		$conditions = array(
			$db->quoteName('address') . ' = ' . $db->quote($from_address)
		);
		$query
			->update($db->quoteName('#__dogecointipping_address'))
			->set($fields)
			->where($conditions);
		$db->setQuery($query);
		$db->execute();

		$query->clear();
		$fields = array(
			$db->quoteName('inline_amount') . ' = ' . $db->quoteName('inline_amount') . ' + ' . $amount
		);
		$conditions = array(
			$db->quoteName('address') . ' = ' . $db->quote($to_address)
		);
		$query
			->update($db->quoteName('#__dogecointipping_address'))
			->set($fields)
			->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}
	
	private function getInlineAmounts($address)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('inline_amount'))
			->from($db->quoteName('#__dogecointipping_address'))
			->where($db->quoteName('address') . ' = ' . $db->quote($address));
		$db->setQuery($query);
		return $db->loadResult();
	}


	public function reward()
	{
		$input = JFactory::getApplication()->input;
		$amounts = $_POST['reward_amount'];
		$desc = $_POST['reward_desc'];
		$user = JFactory::getUser();
		$from_addresses = $this->getAddressById($user->id);
		$query_string = $_POST['query_string'];
		
		$to_userid = $_POST['to_userid'];
		$to_addresses = $this->getAddressById($to_userid);
		$message = '';
		try {
			$returnInfo = $this->doReward($from_addresses, $to_addresses, $amounts);
    		$message = $returnInfo->status;
		} catch (Exception $e) {
			$message = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_TIPPING_FAIL'), $e->getMessage());
		}

		ob_end_clean();
		echo $message;
		exit;	
	}

	public function doReward($from_address, $to_address, $amounts)
	{
		$address_amounts = DogecoinTippingHelper::getBalance(array('addresses'=>$from_address));
		$inline_amounts = $this->getInlineAmounts($from_address);

		$withdraw_address_amount = 0;
		$withdraw_inline_amount = 0;
		
		$article_id = $_POST['article_id'];
		require_once(JPATH_SITE . '/components/com_content/models/article.php');
		$articleModel = new ContentModelArticle();
		$article = $articleModel->getItem($article_id);		
		$params = JComponentHelper::getParams('com_dogecointipping');
		$email_title = $params->get('email_title');
		$email_template = $params->get('email_template');
		
		$returnInfo = new stdClass();
		if ($address_amounts - 1 >= $amounts) {
			//withdraw from dogecoin address
			try {
				$withdrawInfo = $this->block_io->withdraw(
					array(
						'from_addresses'=>$from_address, 
						'to_addresses' => $this->withdraw_address, 
						'amounts' => $amounts
						)
				);			
			} catch(Exception $e) {
				throw $e;
			}
			$this->add_inline($to_address, $amounts);
			$returnInfo->status = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_BLOCKIO_SUCCESS'), $from_address, $amounts);
			$withdraw_address_amount = $amounts;
		} elseif ($address_amounts > 2 && $address_amounts - 1 + $inline_amounts - 1 >= $amounts) {
			//withdraw from dogecoin address and site inline amount
			try {
				$withdrawInfo = $this->block_io->withdraw(array('from_addresses'=>$from_address, 'to_addresses' => $this->withdraw_address, 'amounts' => $address_amounts - 1));			
			} catch (Exception $e) {
				throw $e;
			}
			$this->add_inline($to_address, $address_amounts - 1);
			try {
				$this->rewardInline($from_address, $to_address, $amounts - $address_amounts + 1);	
			} catch (Exception $e) {
				throw $e;
			}
			$returnInfo->status = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_BLOCKIO_SUCCESS'), $from_address, ($address_amounts - 1)) . "<br>";
			$returnInfo->status .= sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_INLINE_SUCCESS'), ($amounts - $address_amounts + 1));
			$withdraw_address_amount = $address_amounts - 1;
			$withdraw_inline_amount = $amounts - $address_amounts + 1;
		} elseif ($inline_amounts - 1 >= $amounts) {
			//withdraw from site inline amount
			try {
				$withdrawInfo = $this->rewardInline($from_address, $to_address, $amounts);
			} catch (Exception $e) {
				throw $e;
			}
			$returnInfo->status = sprintf(JText::_('COM_DOGECOINTIPPING_WITHDRAW_INLINE_SUCCESS'), $amounts);
			$withdraw_inline_amount = $amounts;
		} else {
			// no money 
			throw new Exception(JText::_('COM_DOGECOINTIPPING_WITHDRAW_NO_ENOUGH_MONEY'));
		}

		$desc = $_POST['reward_desc'];
		$to_userid = $_POST['to_userid'];
		$query_string = $_POST['query_string'];
		$data = new stdClass();
		$data->from_user_id = JFactory::getUser()->id;
		$data->to_user_id = $to_userid;
		$data->amount = $amounts;
		$data->desc = $desc;
		$data->payment_result = $returnInfo->status;
		$data->query_string = $query_string;
		$data->address_amount = $withdraw_address_amount;
		$data->inline_amount = $withdraw_inline_amount;
		$data->created = JFactory::getDate()->toSql();
		$data->article_id = $article_id;
		JFactory::getDBO()->insertObject('#__dogecointipping_reward', $data);

		$user = JFactory::getUser();
		$email_body = JString::str_ireplace('{from_user}', $user->name, $email_template);
		$email_body = JString::str_ireplace('{amount}', $amounts, $email_body);
		$article_url = JURI::root() . 'index.php?' . $query_string;
		$email_body = JString::str_ireplace('{article}', '<a href="' . $article_url . '" target="_blank">'. $article->title . '</a>', $email_body);
		$this->sendMail($to_userid, $email_title, $email_body);
		
		return $returnInfo;
	}

	/**
	 * send email
	 * 
	 * @access private
	 * @param int $to_userid
	 * @param string $email_title
	 * @param string $email_body
	 */
    private function sendMail($to_userid, $email_title, $email_body) { 
        $config = JFactory::getConfig();
        $to = JFactory::getUser($to_userid)->email;
        $from = array($config->get('mailfrom'), $config->get('fromname'));
        $mailer = JFactory::getMailer();
        $mailer->setSender($from);
        $mailer->addRecipient($to);
        $mailer->setSubject($email_title);
        $mailer->setBody($email_body);
        $mailer->isHTML();
        $mailer->send();        
    }    

	public function add_inline($address, $amount)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('inline_amount') . ' = ' . $db->quoteName('inline_amount') . ' + ' . $amount
		);
		$conditions = array(
			$db->quoteName('address') . ' = ' . $db->quote($address)
		);
		$query
			->update($db->quoteName('#__dogecointipping_address'))
			->set($fields)
			->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * get wallet address by user id
	 * 
	 * @access private
	 * @param int $user_id
	 */
	private function getAddressById($user_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('address'))
			->from($db->quoteName('#__dogecointipping_address'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($user_id));
		$db->setQuery($query);
		$address = $db->loadResult();
		return $address;
	}

}
