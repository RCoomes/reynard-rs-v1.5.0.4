<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
JLoader::load('RedshopHelperProduct');
$producthelper = new producthelper;
$option = JRequest::getVar('option', '', 'request', 'string');
?>
<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton) {
			form.task.value = pressbutton;
		}

		if ((pressbutton == 'add') || (pressbutton == 'edit')) {
			form.view.value = "discount_detail";
		}
		try {
			form.onsubmit();
		}
		catch (e) {
		}

		form.submit();
	}

</script>
<form action="<?php echo 'index.php?option=' . $option; ?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<div class="filterItem">
			<?php echo JText::_('COM_REDSHOP_SHOPPERGRP_FILTER'); ?>
			: <?php echo $this->lists ['shopper_group']; ?>
		</div>
		<table class="adminlist table table-striped">
			<thead>
			<tr>
				<th width="5%">
					<?php echo JText::_('COM_REDSHOP_NUM'); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('redshopgrid.checkall'); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_AMOUNT', 'amount', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_CONDITION', 'condition', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_DISCOUNT_TYPE', 'discount_type', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>

				<th>
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_DISCOUNT_AMOUNT', 'discount_amount', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_PUBLISHED', 'published', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_ID', 'discount_id', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>

			</tr>
			</thead>
			<?php

			$k = 0;
			for ($i = 0, $n = count($this->discounts); $i < $n; $i++)
			{
				$row = $this->discounts[$i];
				$row->id = $row->discount_id;
				$link = JRoute::_('index.php?option=com_redshop&view=discount_detail&task=edit&cid[]=' . $row->discount_id);

				$published = JHtml::_('jgrid.published', $row->published, $i, '', 1);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center">
						<?php echo JHTML::_('grid.id', $i, $row->id); ?>
					</td>
					<td align="center">
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_REDSHOP_EDIT_DISCOUNT'); ?>">
							<?php echo $producthelper->getProductFormattedPrice($row->amount);//number_format($row->amount,2,PRICE_SEPERATOR,THOUSAND_SEPERATOR).CURRENCY_SYMBOL; ?></a>
					</td>
					<td align="center">
						<?php
						switch ($row->condition)
						{
							case '1':
								echo JText::_('COM_REDSHOP_LOWER');
								break;
							case '2':
								echo JText::_('COM_REDSHOP_EQUAL');
								break;
							case '3':
								echo JText::_('COM_REDSHOP_HIGHER');
								break;
						}
						?>
					</td>
					<td align="center">
						<?php if ($row->discount_type == 0) echo JText::_('COM_REDSHOP_TOTAL');
						else echo JText::_('COM_REDSHOP_PERCENTAGE');
						?>
					</td>
					<td align="center">
						<?php
						if ($row->discount_type == 0) echo $producthelper->getProductFormattedPrice($row->discount_amount);
						else echo $row->discount_amount . '%';

						?>
					</td>
					<td align="center"><?php echo $published;?></td>
					<td align="center"><?php echo $row->discount_id; ?></td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>

			<tfoot>
			<td colspan="9">
				<?php if (version_compare(JVERSION, '3.0', '>=')): ?>
					<div class="redShopLimitBox">
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				<?php endif; ?>
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="view" value="discount"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
