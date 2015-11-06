<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */

defined('_JEXEC') or die;
?>
<form method="post" name="adminForm" id="adminForm">
	<div>
		<p>
			<?php echo JText::_('COM_DOGECOINTIPPING_CPANEL_SYNC_DESC'); ?>
		</p>
		<a href="<?php echo JRoute::_('index.php?option=com_dogecointipping&task=sync.sync'); ?>" class="btn btn-large btn-primary">
			<?php echo JText::_('COM_DOGECOINTIPPING_CPANEL_SYNC'); ?>
		</a>	
	</div>	
</form>


