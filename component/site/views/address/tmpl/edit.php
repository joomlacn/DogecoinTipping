<?php
/**
 * @copyright Copyright &copy; 2015
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 * @author joomla.cn
 * @link http://www.joomla.cn
 */

defined('_JEXEC') or die;

JHTML::_('behavior.framework');
JHTML::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_dogecointipping&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" class="form-horizontal form-validate">
	<div class="form-group">
		<?php echo $this->form->getLabel('label'); ?>
        <div class="col-sm-10">
            <?php echo $this->form->getInput('label'); ?>
        </div>
	</div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
    	<input type="submit" class="btn btn-primary" value="<?php echo JText::_('JSUBMIT'); ?>" />
    	<input type="button" name="cancel-btn" id="cancel-btn" class="btn btn-default" value="<?php echo JText::_('JCANCEL'); ?>" />
    </div>
    
    <input type="hidden" name="task" value="address.save" />
    <?php echo JHtml::_('form.token');?>
</form>
