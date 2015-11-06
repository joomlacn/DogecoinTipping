<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die;
?>
<div class="wall-dashboard<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php if ($this->escape($this->params->get('page_heading'))) :?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1>
<?php endif; ?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo JText::_('COM_DOGECOINTIPPING_DASHBOARD_INFO'); ?></h3>
		</div>
		<div class="panel-body"><?php echo sprintf(JText::_('COM_DOGECOINTIPPING_DASHBOARD_ADDRESS_COUNT'), $this->address_count); ?></div>
		<div class="panel-body"><?php echo sprintf(JText::_('COM_DOGECOINTIPPING_DASHBOARD_INLINE_AMOUNT'), $this->inline_amount); ?></div>
	</div>
</div>
