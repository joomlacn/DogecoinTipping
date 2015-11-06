<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */
defined('_JEXEC') or die;

class PlgContentDogecoinTipping extends JPlugin
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

	static $loaded_js = FALSE;

    /**
     * Use trigger onContentPrepare instead of onContentBeforeDisplay and onContentAfterDisplay to avoid sorting problems
     * with other plugins which use this (wrong!) trigger. Actually this trigger should only be used to manipulate the output
     * and not to add data to the output! (changed since version 2.5-6)
     *
     * @param string  $context
     * @param object  $row
     * @param string  $params
     * @param integer $page
     */
    function onContentPrepare($context, &$row, &$params, $page = 0)
    {
		if (self::$loaded_js == FALSE) {
			$this->addRewardJS();
			self::$loaded_js = TRUE;
		}

    	$input = JFactory::getApplication()->input;
    	$layout = $input->get('layout');
    	$isBlog = FALSE;
    	$isArticle = FALSE;
    	if($context == 'com_content.category' && $layout == 'blog') {
    		$isBlog = TRUE;
    	}
    	if($context == 'com_content.article') {
    		$isArticle = TRUE;
    	}

        if(!($isArticle))
        {
			return true;
        }

		$plgParams = array();
		$plgParams['cat_type'] = $this->params -> get('cat_type', 'all');
		$plgParams['cats'] = $this->params -> get('cats', '0');

		if (!$this->checkArticleCates($row -> catid, $plgParams['cats'], $plgParams['cat_type'])) {
			return true;
		}

		$authorId = $row->created_by;
		$address = $this->getAddress($authorId);
		$info = '';
		if($address) {
			require_once JPATH_PLUGINS . '/content/dogecointipping/libs/phpqrcode/qrlib.php';
			$QRImage = JPATH_ROOT . '/tmp/dogecoin_' . $authorId . '.png';
			$QRImageUrl = JURI::root() . 'tmp/dogecoin_' . $authorId . '.png';
			QRcode::png('dogecoin:' . $address, $QRImage, QR_ECLEVEL_L, 3); 
			$document = JFactory::getDocument();
        	$document->addStyleSheet('plugins/content/dogecointipping/dogecointipping.css', 'text/css');
			
			$rewardHTML = $this->addReward($row);
			$info .= $rewardHTML;
			$info .= '<div class="dogecointipping_info">';
			$info .= '<p class="dogecoin_address"><label>' . JText::_('PLG_CONTENT_DOGECOINTIPPING_AUTHOR_DOGECOIN_ADDRESS') . '</label><span><a href="dogecoin:' . $address . '">' . $address . '</a></span></p>';
			$info .= '<p class="dogecoin_address_qr"><label>' . JText::_('PLG_CONTENT_DOGECOINTIPPING_AUTHOR_DOGECOIN_ADDRESS_QR') . '</label><span><img src="' . $QRImageUrl . '"></span></p>';
			$info .= '</div>';
			
			$row->text .= $info;
		}
		
    }

	/**
	 * add requirement javascript for tipping
	 * 
	 * @access private
	 */
	private function addRewardJS() {
		$post_url = JURI::root() . "index.php?option=com_dogecointipping&task=withdraw.reward";
		$input = JFactory::getApplication()->input;
		$option = $input->get('option');
		$view = $input->get('view');
		$id = $input->get('id', '', 'RAW');
		$catid = $input->get('catid');
		$itemid = $input->get('Itemid');
		$query_string = '';
		$query_string .= "option=$option&view=$view&id=$id&catid=$catid&Itemid=$itemid";
		$article_id = $input->get('id', '', 'INT');
		
		$amount_number = JText::_('PLG_CONTENT_DOGECOINTIPPING_AMOUNT_MUST_NUMBER');
		$submit_data = JText::_('PLG_CONTENT_DOGECOINTIPPING_SUBMIT_DATA');
		$js = "
		function reward_add(element)
		{
			id = element.value;
			console.log(id);
			jQuery('#reward_form_' + id).css('display','');
		}
		function isNum(str)
		{
		    if (str != null && str != '')
		    {
		        return !isNaN(str);
		    }
		    return false;
		}
		function reward_submit(element)
		{
			id = element.value;
			reward_amount = jQuery('#reward_amount_' + id).val();
			to_userid = jQuery('#user_id_' + id).val();
		
			reward_desc = jQuery('#reward_desc_' + id).val();
			if(!isNum(reward_amount)) {
				jQuery('#reward_info_' + id).html('{$amount_number}');
				return false;
			}
			jQuery('#reward_info_' + id).html('{$submit_data}');
			
			jQuery.post('{$post_url}', 
				{
					'reward_amount': reward_amount,
					'to_userid': to_userid,
					'reward_desc': reward_desc,
					'query_string': '" . $query_string . "',
					'article_id': '" . $article_id . "'					
				},
				function(data, status) {
					jQuery('#reward_info_' + id).html(data);
				}
			)	
		}
		";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}
	
	/**
	 * add html input and button for tipping
	 * 
	 * @access private
	 * @param object $row
	 */
	private function addReward($row) {
		require_once(JPATH_SITE . '/components/com_dogecointipping/helpers/dogecointipping.php');
		$my = JFactory::getUser();
		$authorId = $row->created_by;
		$html = '';
		$input = JFactory::getApplication()->input;
		$article_id = $input->get('id', '', 'INT');
		if ($my->guest) {

			$html .= JText::_('JPLG_CONTENT_DOGECOINTIPPING_LOGIN_PROMPT');
			$itemid = $this->getUsersItemId();
			$html .= '<a class="btn" href="' . JRoute::_('index.php?option=com_users&view=login' . $itemid) . '">' . JText::_('PLG_CONTENT_DOGECOINTIPPING_LOGIN') . '</a>';
		} else if ($authorId != $my->id) {
			$reward_count = DogecoinTippingHelper::getRewardCount($article_id);
			$html = '
			<div class="clr"></div>
			<div>
					<button onclick="reward_add(this);" value="' . $row->id . '" type="button" class="reward_btn btn btn-primary">' . JText::_('PLG_CONTENT_DOGECOINTIPPING_TIPPING') . '</button>
					<span>' . JText::_('PLG_CONTENT_DOGECOINTIPPING_TIPPING_PROMPT') . '</span>';
			if ($reward_count > 0) {
				$html .= ' <span>' . sprintf(JText::_('PLG_CONTENT_DOGECOINTIPPING_TIPPING_NUMBER'), $reward_count) . '</span>';
			}
			$html .= '	
					<span id="reward_form_' . $row->id . '" style="display:none;">
						<label for="reward_amount">' . JText::_('PLG_CONTENT_DOGECOINTIPPING_AMOUNT') . '</label>
						<input type="text" size="5" id="reward_amount_' . $row->id . '" name="reward_amount">
						<label for="reward_desc">' . JText::_('PLG_CONTENT_DOGECOINTIPPING_REMARK') . '</label>
						<input type="text" size="10" id="reward_desc_' . $row->id . '" name="reward_desc">
						<button type="button" onclick="reward_submit(this);" value="' . $row->id . '" class="btn btn-primary btn-mini">' . JText::_('JSUBMIT') . '</button>
						<input type="hidden" id="user_id_' . $row->id . '" value="' . $authorId . '">
					</span>
					<div id="reward_info_' . $row->id . '"></div>
			</div>';
		}
		
		return $html;
	}
	
	/**
	 * get user wallet address
	 * 
	 * @access public
	 * @param int $user_id
	 */
	public function getAddress($user_id) {
		$query = $this->db->getQuery(true);
		$query
			->select($this->db->quoteName('address'))
			->from($this->db->quoteName('#__dogecointipping_address'))
			->where($this->db->quoteName('user_id') . ' = ' . $user_id);
		$this->db->setQuery($query);
		if($result = $this->db->loadResult()) {
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * get Itemid of user menu
	 * 
	 * @access public
	 * @static
	 */
    public static function getUsersItemId(){
        $menu = JMenu::getInstance('site');
        $mnuitem = $menu->getItems('link', 'index.php?option=com_users&view=login', true);
        if(empty($mnuitem))
        {
            $mnuitem = $menu->getItems('link', 'index.php?option=com_users&view=login', true);
        }
        $input = JFactory::getApplication()->input;
        $ritemid = $input->get('Itemid');
        $itemid = $mnuitem ? '&Itemid='.$mnuitem->id : (($ritemid)?'&Itemid='.$ritemid:'');
        return $itemid;
    } 

	/**
	 * check article category
	 *
	 * @access public
	 * @static
	 * @param $cat
	 * @param $paramsCat
	 * @param $paramsCatType
	 * @return boolean
	 */
	public function checkArticleCates($cat, $paramsCat, $paramsCatType) {
		if ($paramsCatType == 'all') {
			return true;
		}
		if (is_string($paramsCat)) {
			if ($paramsCatType == 'follow_cate') {
				if ($cat == $paramsCat) {
					return true;
				} else {
					return false;
				}
			} elseif ($paramsCatType == 'except_cate') {
				if ($cat == $paramsCat) {
					return false;
				} else {
					return true;
				}
			}
		} elseif (is_array($paramsCat)) {
			if ($paramsCatType == 'follow_cate') {
				if (in_array($cat, $paramsCat)) {
					return true;
				} else {
					return false;
				}
			} elseif ($paramsCatType == 'except_cate') {
				if (in_array($cat, $paramsCat)) {
					return false;
				} else {
					return true;
				}
			}
		}
	} // end function

}
