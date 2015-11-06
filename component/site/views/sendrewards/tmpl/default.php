<?php
/**
* @copyright Copyright &copy; 2015
* @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
* @author joomla.cn
* @link http://www.joomla.cn
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'ordering';
?>
<div class="dogecointipping-send-rewards<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php if ($this->escape($this->params->get('page_heading'))) :?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1>
<?php endif; ?>

<form class="form-inline" action="<?php echo JRoute::_('index.php?option=com_dogecointipping&view=sendrewards');?>" method="post" name="adminForm" id="adminForm">
		<div class="form-group">
			<label class="control-label" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></label>
			<input type="text" class="form-control" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search'));?>" title="<?php echo JText::_('COM_DOGECOINTIPPING_RECEIVED_REWARDS_SEARCH_IN_TITLE');?>" />
			<button class="btn btn-primary" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT')?></button>
			<button class="btn btn-primary" type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR');?></button>
		</div>
<?php if($this->items) :?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_DOGECOINTIPPING_HEADING_TO_USER_NAME', 'users.name', $listDirn, $listOrder); ?>					
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_DOGECOINTIPPING_HEADING_AMOUNT', 'reward.amount', $listDirn, $listOrder); ?>					
				</th>
				<th>
					<?php echo JText::_('COM_DOGECOINTIPPING_HEADING_DESC'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_DOGECOINTIPPING_HEADING_REWARD_PAGE'); ?>
				</th>				
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_DOGECOINTIPPING_HEADING_CREATED', 'reward.created', $listDirn, $listOrder); ?>					
				</th>		
				<th>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'reward.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->items as $i => $item):
			$ordering	= ($listOrder == 'ordering');
		?>
			<tr <?php if($i % 2 != 0) echo 'class="pure-table-odd"'; ?>>
				<td class="center">
					<?php if(!empty($item->name)) : 
						$user_link = JRoute::_('index.php?option=com_users&view=profile&user_id=' . $item->from_user_id);
					?>
					<a href="<?php echo $user_link; ?>">
						<?php echo $this->escape($item->name); ?>
					</a>
					<?php 
					else :
						echo $this->escape($item->name);
					endif; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->amount); ?>
				</td>
				<td>
					<?php echo $this->escape($item->desc); ?>
				</td>
				<td>
					<a target="_blank" href="<?php echo JRoute::_('index.php?' . $item->query_string); ?>">
						<?php echo JText::_("COM_DOGECOINTIPPING_REWARD_PAGE"); ?>
					</a>
				</td>
				<td class="center">
					<?php echo JHTML::_('date', $item->created); ?>
				</td>
				<td>
					<?php echo $item->id;?>
				</td>
			</tr>
		<?php
			$i ++;
		endforeach;
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>
	<div>
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token');?>
	</div>
</form>
</div>
