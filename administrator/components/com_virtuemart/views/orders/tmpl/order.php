<?php
/**
 * Display form details
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk, Max Milbers, Valérie Isaksen
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2021 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: order.php 10542 2021-09-20 13:20:00Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea($this);
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_ORDER_PRINT_PO_LBL');

// Get the plugins
vDispatcher::importVMPlugins('vmpayment');

$jsOrderStatusShopperEmail = '""';
$j = 'if (typeof Virtuemart === "undefined")
	var Virtuemart = {};
	Virtuemart.confirmDelete = "'. vmText::_('COM_VIRTUEMART_ORDER_DELETE_ITEM_JS', true) .'";
	jQuery(document).ready(function() {
		Virtuemart.onReadyOrderItems();
	});
	var editingItem = 0;';
vmJsApi::addJScript('onReadyOrder',$j);

vmJsApi::addJScript('/administrator/components/com_virtuemart/assets/js/orders.js',false,false);

?>
<div style="text-align: left;">
<form name='adminForm' id="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>


<table class="adminlist table table-striped" width="100%">
	<tr>
		<td width="100%">
		<?php echo $this->displayDefaultViewSearch ('COM_VIRTUEMART_ORDER_PRINT_NAME'); ?>
			<span class="btn btn-small " >
		<a class="updateOrder" href="#"><span class="icon-nofloat vmicon vmicon-16-save"></span>
		<?php echo vmText::_('COM_VIRTUEMART_ORDER_SAVE_USER_INFO'); ?></a></span>
		&nbsp;&nbsp;
				<span class="btn btn-small " >
		<a href="#" onClick="javascript:Virtuemart.resetOrderHead(event);" ><span class="icon-nofloat vmicon vmicon-16-cancel"></span>
		<?php echo vmText::_('COM_VIRTUEMART_ORDER_RESET'); ?></a>
					</span>

		<?php $this->createPrintLinks($this->orderbt,$print_link,$deliverynote_link,$invoices_link);
		?>
			<span style="float:right">
			<?php
			echo $print_link;
			echo $deliverynote_link;
			echo $invoices_link;
			?>
			</span>
		</td>
	</tr>
</table>
</form>

<table class="adminlist table" style="table-layout: fixed;">
	<tr>
		<td valign="top">
		<table class="adminlist" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PO_LBL') ?></th>
				<th><?php
				$unequal = (int)$this->currency->truncate($this->orderbt->toPay-$this->orderbt->paid);
				if($unequal){
					$icon 	= 'unpublish';
					$val    = '1';
					$text   = 'COM_VIRTUEMART_ORDER_SET_PAID';
				} else {
					$icon 	= 'publish';
					$val    = '0';
					$text   = 'COM_VIRTUEMART_ORDER_SET_UNPAID';
				}

				$link = 'index.php?option=com_virtuemart&view=orders&task=toggle.paid.'.$val.'&cidName=virtuemart_order_id&virtuemart_order_id[]='.$this->orderID.'&rtask=edit&'.JSession::getFormToken().'=1';
				echo JHtml::_ ('link', JRoute::_ ($link, FALSE), '<span class="icon-'.$icon.'"><span>', array('title' => vmText::_ ($text) . ' ' . $this->orderbt->order_number));
				/*($this->orderbt->paid, $this->orderID,'toggle.paid'); */echo $this->currency->priceDisplay($this->orderbt->paid); ?></th>
				<th>
				<?php
					if(empty($this->orderbt->invoice_locked)){
						$icon 	= 'publish';
						$val    = '1';
						$text   = 'COM_VM_ORDER_INVOICE_LOCK';
						$textState = '';
					} else {
						$icon 	= 'unpublish';
						$val    = '0';
						$text   = 'COM_VIRTUEMART_INVOICE_UNLOCK';
						$textState = vmText::_('COM_VM_ORDER_INVOICE_LOCKED');
					}

					$link = 'index.php?option=com_virtuemart&view=orders&task=toggle.invoice_locked.'.$val.'&cidName=virtuemart_order_id&virtuemart_order_id[]='.$this->orderID.'&rtask=edit&'.JSession::getFormToken().'=1';
					echo JHtml::_ ('link', JRoute::_ ($link, FALSE), '<span class="icon-'.$icon.'"><span>', array('title' => vmText::_ ($text) . ' ' . $this->orderbt->order_number)); echo $textState; ?>
				</th>
			</tr>
			</thead>
			<?php
			/*	$print_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
				$print_link = "<a title=\"".vmText::_('COM_VIRTUEMART_PRINT')."\" href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
				$print_link .=   $this->orderbt->order_number . ' </a>';	*/
			?>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') ?></strong></td>
				<?php
				$orderLink=JURI::root() .'index.php?option=com_virtuemart&view=orders&layout=details&order_number='.$this->orderbt->order_number.'&order_pass='.$this->orderbt->order_pass;

				?>
				<td><?php echo $this->orderbt->order_number; ?> <a href="<?php echo $orderLink ?>" target="_blank" title="<?php echo  vmText::_ ('COM_VIRTUEMART_ORDER_VIEW_ORDER_FRONTEND')?>"><span class="vm2-modallink"></span></a></td>
				<?php /*<td><?php echo  $print_link;?></td> */ ?>
			</tr>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PO_PASS') ?></strong></td>
				<td><?php echo  $this->orderbt->order_pass;?></td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE') ?></strong></td>
				<td><?php  echo vmJsApi::date($this->orderbt->created_on,'LC2',true); ?></td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></strong></td>
				<td><?php echo $this->orderstatuslist[$this->orderbt->order_status]; ?></td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></strong></td>
				<td><?php
					if ($this->orderbt->virtuemart_user_id) {
						$userlink = JRoute::_ ('index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=' . $this->orderbt->virtuemart_user_id);
						echo $this->orderbt->order_name;
						echo ' <a href="'.$userlink.'" target="_blank" title="'.vmText::_ ('COM_VIRTUEMART_ORDER_EDIT_USER') . ' ' . $this->orderbt->order_name.'"><span class="icon-edit"></span></a>';
					} else {
						echo $this->orderbt->first_name.' '.$this->orderbt->last_name;
					}
					if( !empty($this->orderbt->order_language) and $this->orderbt->order_language!=VmConfig::$vmlangTag ) {
						echo '<span style="float: right;"> '.$this->orderbt->order_language. ' </span>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PO_IPADDRESS') ?></strong></td>
				<td><?php echo $this->orderbt->ip_address; ?></td>
			</tr>
			<?php
			if ($this->orderbt->coupon_code) { ?>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_COUPON_CODE') ?></strong></td>
				<td><?php echo $this->orderbt->coupon_code; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="key"><strong><?php echo vmText::_('COM_VIRTUEMART_INVOICE') ?></strong></td>
				<td>
					<?php
					if ($this->orderbt->invoiceNumbers) {
						$countInvoices=count($this->orderbt->invoiceNumbers);
						foreach ($this->orderbt->invoiceNumbers as $index =>$invoiceNumber){
								echo $invoiceNumber;
								if ($countInvoices >1 and $index != ($countInvoices-1))  {
									?>
									<br>
									<?php
								}
						}
					}
					?>
				</td>
			</tr>
		</table>
		</td>
		<td valign="top">
		<table class="adminlist table">
			<thead>
				<tr>
					<th><?php echo vmText::_('COM_VIRTUEMART_ORDER_HISTORY_DATE_ADDED') ?></th>
					<th><?php echo vmText::_('COM_VIRTUEMART_ORDER_HISTORY_CUSTOMER_NOTIFIED') ?></th>
					<th><?php echo vmText::_('COM_VIRTUEMART_ORDER_LIST_STATUS') ?></th>
					<th><?php echo vmText::_('COM_VIRTUEMART_COMMENT') ?></th>
				</tr>
			</thead>
			<?php
			foreach ($this->orderdetails['history'] as $this->orderbt_event ) {
				echo "<tr >";
				echo "<td class='key'>". vmJsApi::date($this->orderbt_event->created_on,'LC2',true) ."</td>\n";
				if ($this->orderbt_event->customer_notified == 1) {
					echo '<td align="center">'.vmText::_('COM_VIRTUEMART_YES').'</td>';
				}
				else {
					echo '<td align="center">'.vmText::_('COM_VIRTUEMART_NO').'</td>';
				}
				if(!isset($this->orderstatuslist[$this->orderbt_event->order_status_code])){
					if(empty($this->orderbt_event->order_status_code)){
						$this->orderbt_event->order_status_code = 'unknown';
					}
					$this->orderstatuslist[$this->orderbt_event->order_status_code] = vmText::sprintf('COM_VIRTUEMART_UNKNOWN_ORDER_STATUS',$this->orderbt_event->order_status_code);
				}

				echo '<td align="center">'.$this->orderstatuslist[$this->orderbt_event->order_status_code].'</td>';
				echo "<td>".$this->orderbt_event->comments."</td>\n";
				echo "</tr>\n";
			}
			?>
			<tr>
				<td colspan="4">
				<a href="#" class="show_element btn btn-small"><span class="vmicon vmicon-16-editadd"></span><?php echo vmText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></a>
				<div style="display: none; background: white; z-index: 100;"
					class="element-hidden vm-absolute"
					id="updateOrderStatus"><?php echo $this->loadTemplate('editstatus'); ?>
				</div>
				</td>
			</tr>

			<?php
				// Load additional plugins

				$_returnValues1 = vDispatcher::trigger('plgVmOnUpdateOrderBEPayment',array($this->orderID));
				$_returnValues2 = vDispatcher::trigger('plgVmOnUpdateOrderBEShipment',array(  $this->orderID));
				$_returnValues = array_merge($_returnValues1, $_returnValues2);
				$_plg = '';
				foreach ($_returnValues as $_returnValue) {
					if ($_returnValue !== null) {
						$_plg .= ('	<td colspan="4">' . $_returnValue . "</td>\n");
					}
				}
				if ($_plg !== '') {
					echo "<tr>\n$_plg</tr>\n";
				}
			?>

		</table>
		</td>
	</tr>
</table>

<form action="index.php" method="post" name="orderForm" id="orderForm"><!-- Update order head form -->
<table class="adminlist table" >
	<?php // if ($this->orderbt->customer_note || true) {
	if(true){ ?>


						<thead>

						<th colspan="2" width="50%"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_SHIPMENT') ?></th>

						<th colspan="2" width="50%"><?php echo vmText::_('COM_VIRTUEMART_ORDER_NOTE') ?></th>

						</thead>
					<tr>
						<td><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?></td>
						<?php
						$model = VmModel::getModel('paymentmethod');
						$payments = $model->getPayments();
						$model = VmModel::getModel('shipmentmethod');
						$shipments = $model->getShipments();
						?>
						<td>
							<input  type="hidden" size="10" name="virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>"/>
							<!--
							<?php echo VmHTML::select("virtuemart_paymentmethod_id", $payments, $this->orderbt->virtuemart_paymentmethod_id, '', "virtuemart_paymentmethod_id", "payment_name"); ?>
							<span id="delete_old_payment" style="display: none;"><br />
								<input id="delete_old_payment" type="checkbox" name="delete_old_payment" value="1" /> <label class='' for="" title="<?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_DELETE_DESC'); ?>"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_DELETE'); ?></label>
							</span>
							-->
							<?php
							foreach($payments as $payment) {
								if($payment->virtuemart_paymentmethod_id == $this->orderbt->virtuemart_paymentmethod_id) echo $payment->payment_name;
							}
							?>
						</td>
						<td>
							<?php echo '<textarea class="textarea" name="order_note" cols="60" rows="2">'.$this->orderbt->order_note.' </textarea>' ?>
						</td>
					</tr>
					<tr>
						<td><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL') ?></td>
						<td>
							<input type="hidden" size="10" name="virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>"/>
							<!--
							<?php echo VmHTML::select("virtuemart_shipmentmethod_id", $shipments, $this->orderbt->virtuemart_shipmentmethod_id, '', "virtuemart_shipmentmethod_id", "shipment_name"); ?>
							<span id="delete_old_shipment" style="display: none;"><br />
								<input id="delete_old_shipment" type="checkbox" name="delete_old_shipment" value="1" /> <label class='' for=""><?php echo vmText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
							</span>
							-->
							<?php
							foreach($shipments as $shipment) {
								if($shipment->virtuemart_shipmentmethod_id == $this->orderbt->virtuemart_shipmentmethod_id) echo $shipment->shipment_name;
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo vmText::_('COM_VIRTUEMART_DELIVERY_DATE') ?></td>
						<td><input type="text" maxlength="190" class="required" value="<?php echo $this->orderbt->delivery_date; ?>" size="30" name="delivery_date" id="delivery_date_field"></td>
					</tr>
	<?php } ?>
</table>
&nbsp;
<table width="100%">
	<tr>
		<td width="50%" valign="top">
		<table class="adminlist table">
			<thead>
				<tr>
					<th  style="text-align: center;" colspan="2"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></th>
				</tr>
			</thead>

			<?php
			foreach ($this->userfields['fields'] as $_field ) {

				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				<label for="'.$_field['name'].'_field">'."\n";
				echo '					'.$_field['title'] . ($_field['required']?' *': '')."\n";
				echo '				</label>'."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				if ($_field['type'] === 'hidden') {
					echo '				'.htmlentities($_field['value'],ENT_COMPAT, 'UTF-8', false)."\n";
				}
				else {
					echo '				'.$_field['formcode']."\n";
				}
				echo '			</td>'."\n";
				echo '		</tr>'."\n"; //*/
			/*	$fn = $_field['name'];
				$fv = $_field['value'];
				$ft = $_field['title'];
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				'.$ft."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo "				<input name='BT_$fn' id='$fn' value='$fv' size='50'>\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n";*/
			}
			?>

		</table>
		</td>
		<td width="50%" valign="top">
		<table class="adminlist table">
			<thead>
				<tr>
					<th   style="text-align: center;" colspan="2"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></th>
				</tr>
			</thead>
<?php
			//Create Ship to address if one does not already exist
			if($this->orderdetails['details']['has_ST'] == false){
				echo '<td class="key"><label for="STsameAsBT">'. vmText::_('COM_VM_ST_SAME_AS_BT').'</label></td>';
			echo '<td><input id="STsameAsBT" type="checkbox" checked name="STsameAsBT" value="1" /></td>';
			}

			foreach ($this->shipmentfields['fields'] as $_field ) {
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				<label for="'.$_field['name'].'_field">'."\n";
				echo '					'.$_field['title'] . ($_field['required']?' *': '')."\n";
				echo '				</label>'."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				if ($_field['type'] === 'hidden') {
					echo '				'.htmlentities($_field['value'],ENT_COMPAT, 'UTF-8', false)."\n";
				}
				else {
					echo '				'.$_field['formcode']."\n";
				}
				echo '			</td>'."\n";
				echo '		</tr>'."\n";
			}
			?>

		</table>
		</td>
	</tr>
</table>
		<input type="hidden" name="task" value="updateOrderHead" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<input type="hidden" name="old_virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>" />
		<input type="hidden" name="old_virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
</form>
	<?php  $oId=0;  ?>
	<table width="100%">
		<tr id="updateOrderItemStatus" class="viewMode">
			<td colspan="11">

				<?php if(vmAccess::manager('orders.edit')) { ?>

					<a href="#" class="btn btn-small enableEdit">
						<span class="icon-nofloat vmicon vmicon-16-edit"></span><?php echo '&nbsp;' . vmText::_('COM_VIRTUEMART_ORDER_ITEMS_EDIT'); ?>
					</a>
					<a href="#" id="add-order-item" class="btn btn-small orderEdit">
						<span class="icon-nofloat vmicon vmicon-16-new"></span><?php echo '&nbsp;' . vmText::_('COM_VIRTUEMART_ORDER_ITEM_NEW'); ?>
					</a>
					<a href="#" class="btn btn-small cancelEdit orderEdit">
						<span class="icon-nofloat vmicon vmicon-16-remove 4remove"></span>
						<?php echo '&nbsp;' . vmText::_('COM_VIRTUEMART_ORDER_ITEMS_EDIT_CANCEL'); ?>
					</a>
				<?php } ?>

				<?php if(vmAccess::manager('orders.status') or vmAccess::manager('orders.edit')) { ?>
					<a class="btn btn-small  updateOrderItemStatus orderEdit" href="#"><span
								class="icon-nofloat vmicon vmicon-16-save"></span><?php echo vmText::_('COM_VIRTUEMART_ORDER_ITEMS_SAVE'); ?></a>
				<?php } ?>
			</td>
		</tr>
	</table>



<table width="100%" id="order-items-table">
	<tr>
		<td colspan="2">
		<form action="index.php" method="post" name="orderItemForm" id="orderItemForm"><!-- Update linestatus form -->
		<table class="adminlist table"  id="itemTable" >
			<thead>
				<tr>
					<!--<th class="title" width="5%" align="left"><?php echo vmText::_('COM_VIRTUEMART_ORDER_EDIT_ACTIONS') ?></th> -->
					<th class="title" width="3" align="left">#</th>
					<th class="title" width="47" align="left"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_QUANTITY') ?></th>
					<th class="title" width="*" align="left"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></th>
					<th class="title" width="10%" align="left"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?></th>
					<th class="title" width="10%"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_ITEM_STATUS') ?></th>
					<th class="title" width="50"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_NET') ?></th>
					<th class="title" width="50"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_BASEWITHTAX') ?></th>
					<th class="title" width="50"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_GROSS') ?></th>
					<th class="title" width="50"><?php
						if(is_array($this->taxBill) and count($this->taxBill)==1){
							reset($this->taxBill);
							$t = current($this->taxBill);
							echo shopFunctionsF::getTaxNameWithValue($t->calc_rule_name,$t->calc_value);
						} else {
							echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_TAX');
						}
					//echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_TAX') ?></th>
					<th class="title" width="50"> <?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_DISCOUNT') ?></th>
					<th class="title" width="5%"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></th>
				</tr>
			</thead>
		<?php $i=1;
		$i=1;
		$rowColor = 0;
		$nbItems=count($this->orderdetails['items']);
		$this->itemsCounter=0;


		foreach ($this->orderdetails['items'] as  $index=> $item) { ?>
			<?php
			$this->item=$item;
			$tmpl = "add-tmpl-" . $index;

			?>
			<tr id="<?php echo $tmpl ?>" class="order-item <?php echo $rowColor?> ">
				<?php //echo vmText::_ ('COM_VIRTUEMART_PRODUCT_PRICE_ORDER');
				echo $this->loadTemplate ('item'); ?>
			</tr>

			<?php
		}
		// TODO move that to fillVoidOrderItem, from the table ?
		$emptyItem=new stdClass();
		$emptyItem->product_quantity=0;
		$emptyItem->virtuemart_order_item_id=0; // 0-xx-yy : cloned or new order tiem
		$emptyItem->virtuemart_product_id='';
		$emptyItem->order_item_sku='';
		$emptyItem->order_item_name='';
		$emptyItem->order_status='';
		$emptyItem->product_discountedPriceWithoutTax='';
		$emptyItem->product_item_price='';
		$emptyItem->product_basePriceWithTax='';
		$emptyItem->product_final_price='';
		$emptyItem->product_tax='';
		$emptyItem->product_subtotal_discount='';
		$emptyItem->product_subtotal_with_tax='';
		$emptyItem->order_status='P';
		$emptyItem->linkedit='';
		$emptyItem->tax_rule=0;
		$emptyItem->tax_rule_id=array();
		$this->item=$emptyItem;
		?>
		<tr id="add-tmpl" class="removable row<?php echo $rowColor?>">
			<?php echo $this->loadTemplate ('item'); ?>
		</tr>
		<!--/table -->
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<input type="hidden" name="virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>" />
		<input type="hidden" name="virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>" />
		<input type="hidden" name="order_total" value="<?php echo $this->orderbt->order_total; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form> <!-- Update linestatus form -->
		<!--table class="adminlist" cellspacing="0" cellpadding="0" -->
			<tr>
				<td align="left" colspan="1"><?php $editLineLink=JRoute::_('index.php?option=com_virtuemart&view=orders&orderId='.$this->orderbt->virtuemart_order_id.'&orderLineId=0&tmpl=component&task=editOrderItem'); ?>
				<!-- <a href="<?php echo $editLineLink; ?>" class="modal"> <?php echo JHtml::_('image',  'administrator/components/com_virtuemart/assets/images/icon_16/icon-16-editadd.png', "New Item"); ?>
				New Item </a>--></td>
				<td align="right" colspan="4">
				<div align="right"><strong> <?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL') ?>:
				</strong></div>
				</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_subtotal); ?></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_tax); ?></td>
				<td align="right"> <?php echo $this->currency->priceDisplay($this->orderbt->order_discountAmount); ?></td>
				<td width="15%" align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_salesPrice); ?></td>
			</tr>
			<?php
			/* COUPON DISCOUNT */
			//if (VmConfig::get('coupons_enable') == '1') {

				if ($this->orderbt->coupon_discount > 0 || $this->orderbt->coupon_discount < 0) {
					?>
			<tr>
				<td align="right" colspan="5"><strong><?php echo vmText::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?></strong></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><?php
				    echo $this->currency->priceDisplay($this->orderbt->coupon_discount);  ?>
                    <input class='orderEdit' type="text" size="8" name="coupon_discount" value="<?php echo $this->orderbt->coupon_discount; ?>"/>
				</td>
			</tr>
			<?php
				//}
			}?>



	<?php
		foreach($this->orderdetails['calc_rules'] as $rule){
			if ($rule->calc_kind == 'DBTaxRulesBill') { ?>
			<tr >
				<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>

				<td align="right">
				<!--
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);?>
					<input class='orderEdit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>][calc_tax]" value="<?php echo $rule->calc_amount; ?>"/>
				-->
				</td>
				<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
				<td align="right"  style="padding-right: 5px;">
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);?>
					<input class='orderEdit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
				</td>
			</tr>
			<?php
			} elseif ($rule->calc_kind == 'taxRulesBill') { ?>
			<tr >
				<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>
				<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
				<td align="right"> </td>
				<td align="right"  style="padding-right: 5px;">
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?>
					<input class='orderEdit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
				</td>
			</tr>
			<?php
			 } elseif ($rule->calc_kind == 'DATaxRulesBill') { ?>
			<tr >
				<td colspan="5"   align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>

				<td align="right"> </td>
				<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
				<td align="right"  style="padding-right: 5px;">
					<?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?>
					<input class='orderEdit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
				</td>
			</tr>

			<?php
			 }

		}
		?>

			<tr>
				<td align="right" colspan="5"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>:</strong></td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment); ?>
					<input class='orderEdit' type="text" size="8" name="order_shipment" value="<?php echo $this->orderbt->order_shipment; ?>"/>
				</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment_tax); ?>
					<input class='orderEdit' type="text" size="12" name="order_shipment_tax" value="<?php echo $this->orderbt->order_shipment_tax; ?>"/>
				</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment+$this->orderbt->order_shipment_tax); ?></td>

			</tr>
			<tr>
				<td align="right" colspan="5"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT') ?>:</strong></td>
				<td align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment); ?>
					<input class='orderEdit' type="text" size="8" name="order_payment" value="<?php echo $this->orderbt->order_payment; ?>"/>
				</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment_tax); ?>
					<input class='orderEdit' type="text" size="12" name="order_payment_tax" value="<?php echo $this->orderbt->order_payment_tax; ?>"/>
				</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment+$this->orderbt->order_payment_tax); ?></td>

			</tr>
			<?php
			if(is_array($this->taxBill) and count($this->taxBill)!=1){
				reset($this->taxBill);
				foreach($this->taxBill as $rule){
					if ($rule->calc_kind != 'taxRulesBill' and $rule->calc_kind != 'VatTax' ) continue;
					?>
                    <tr>
                    <td colspan="5" align="right"><?php echo $rule->calc_rule_name ?> </td>
                    <td align="right" colspan="3"></td>
                    <td align="right" style="padding-right: 5px;">
						<?php echo $this->currency->priceDisplay( $rule->calc_amount );
						/* <input class='orderEdit' type="text" size="8"
								name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_calc_id ?>]"
								value="<?php echo $rule->calc_amount; ?>"/>*/
						?>
                    </td>
                    <td align="right" colspan="2"></td>
                    </tr><?php
				}
			}

			?>
			<tr>
				<td align="right" colspan="5"><strong><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>:</strong></td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">&nbsp;</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($this->orderbt->order_billTaxAmount); ?>
					<input class='orderEdit' type="text" size="12" name="order_billTaxAmount" value="<?php echo $this->orderbt->order_billTaxAmount; ?>"/>
					<span style="display: block; font-size: 80%;" title="<?php echo vmText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE_DESC'); ?>">
						<input class='orderEdit' type="checkbox" name="calculate_billTaxAmount" value="1" checked /> <label class='orderEdit' for="calculate_billTaxAmount"><?php echo vmText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
					</span>
				</td>
				<td align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_billDiscountAmount); ?></strong></td>
				<td align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_total); ?></strong>
				</td>
			</tr>
            <tr style="border-top-style:double">
                <td align="left" colspan="3" style="padding-right: 5px;"><strong><?php echo vmText::_('COM_VM_ORDER_BALANCE') ?></strong></td>

            <?php

            $tp = '';
            $detail=false;
            if (empty($this->orderbt->paid) ) {
                $t = vmText::_('COM_VM_ORDER_UNPAID');
                /*echo '<td colspan="1"></td>';
                echo '<td align="left" colspan="2" style="padding-right: 5px;">'.$t.'</td>';
                echo '<td><input class="orderEdit" type="text" size="8" name="paid" value="'.$this->orderbt->paid.'"/></td>';*/
                //echo '</tr>';
            } else if(!$unequal){
                $t =  vmText::_('COM_VM_ORDER_PAID');
            } else if($unequal>0.0){
                $t =  vmText::sprintf('COM_VM_ORDER_PARTIAL_PAID',$this->orderbt->paid);
                $detail=true;
            } else {
                $t =  vmText::sprintf('COM_VM_ORDER_CREDIT_BALANCE',$this->orderbt->paid);
                $detail=true;
            }
            $trOpen = true;
            $colspan = '5';
            if(empty($this->toRefund) and !$detail){
                echo '<td align="left" colspan="2" style="padding-right: 5px;">'.$t.'</td>';
				echo '<td align="left" style="padding-right: 5px;">'.$this->orderbt->paid_on.'</td>';
                echo '<td><input class="orderEdit" type="text" size="8" name="paid" value="'.$this->orderbt->paid.'"/></td>';
                echo '</tr>';
                $trOpen = false;
            }

            if(!empty($this->toRefund)){
                echo '<td colspan="8">'.vmText::_('COM_VM_ORDER_PRODUCTS_TO_REFUND').'</td>';
                if($trOpen) {
                    echo '</tr>';
                    $trOpen = false;
                }
                foreach ($this->toRefund as $index => $item) {

                    $tmpl = "refund-tmpl-" . $index;

                    echo '<tr id="'.$tmpl.'" class="order-item '.$rowColor.' ">';
                    echo '<td colspan="3"></td>';
                    echo '<td colspan="3">'.$item->order_item_name.'</td>';
                    echo '<td colspan="2">'.$item->order_item_sku.'</td>';
                    echo '<td>'.$this->currency->priceDisplay($item->product_tax).'</td>';
                    echo '<td></td>';
                    echo '<td>'.$this->currency->priceDisplay($item->product_subtotal_with_tax).'</td>';
                    echo '</tr>';
                    $this->orderbt->order_total -= $item->product_subtotal_with_tax;
                    $colspan1 = '5';
					$colspan2 = '5';
                }
            } else {
                $colspan1 = '3';
				$colspan2 = '4';
            }
			//$colspan = '3';
            if(!empty($this->toRefund) or $detail){

                if($unequal>0.0){

                    if (empty($this->orderbt->paid)){
                        $t =  vmText::_('COM_VM_ORDER_UNPAID');
                    } else {
                        $t =  vmText::_('COM_VM_ORDER_PARTIAL_PAID');
                    }
                    $l = vmText::_('COM_VM_ORDER_OUTSTANDING_AMOUNT');

                } else {
                    $t = vmText::_('COM_VM_ORDER_PAID');
                    $l = vmText::_('COM_VM_ORDER_BALANCE');
                }
				$totalDiff = (int)$this->currency->truncate($this->orderbt->toPay-$this->orderbt->order_total);

                $tp .= '';
                if($totalDiff){
                    if(!$trOpen){
                        $tp .= '<tr>';
                        $trOpen= true;
                    }
                    $tp .= '<td colspan="'.$colspan1.'"></td>';
                    $tp .= '<td align="left" colspan="'.$colspan2.'" style="padding-right: 5px;">'.vmText::_('COM_VM_ORDER_NEW_TOTAL').'</td>';
                    $tp .= '<td>'.$this->currency->priceDisplay($this->orderbt->toPay).'</td>';

                    if($trOpen) {
                        $tp .= '</tr>';
                        $trOpen = false;
                    }
                }

                if(!$trOpen){
                    $tp .= '<tr>';
                    $trOpen= true;
                }
                $tp .= '<td colspan="'.$colspan1.'"></td>';
                $tp .= '<td align="left" colspan="'.$colspan2.'" style="padding-right: 5px;">'.$t.'</td>';

                $tp .= '<td>'.$this->currency->priceDisplay($this->orderbt->paid).'<input class="orderEdit" type="text" size="8" name="paid" value="'.$this->orderbt->paid.'"/></td>';
				//$tp .= '<td align="left" style="padding-right: 5px;">'.$this->orderbt->paid_on.'</td>';
                $tp .= '</tr>';

                $tp .= '<tr>';
                $tp .= '<td colspan="5"></td>';
                $tp .= '<td align="right" colspan="5" style="padding-right: 5px;">'.$l.'</td>';
                $tp .= '<td align="right" >'.$this->currency->priceDisplay(abs($this->orderbt->order_total - $this->orderbt->paid) ).'</td>';
                echo $tp;
            }
            if($trOpen) {
                echo '</tr>';
                $trOpen = false;
            }

            if ($this->orderbt->user_currency_rate != 1.0) { ?>
			<tr>
				<td align="right" colspan="5"><em><?php echo vmText::_('COM_VIRTUEMART_ORDER_USER_CURRENCY_RATE') ?>:</em></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><em><?php echo  $this->orderbt->user_currency_rate ?></em></td>
			</tr>
			<?php }
			?>
		</table>
		</td>
	</tr>
</table>
&nbsp;
<table width="100%">
	<tr>
		<td valign="top" width="50%"><?php

		$returnValues = vDispatcher::trigger('plgVmOnShowOrderBEShipment',array(  $this->orderID,$this->orderbt->virtuemart_shipmentmethod_id, $this->orderdetails));

		foreach ($returnValues as $returnValue) {
			if ($returnValue !== null) {
				echo $returnValue;
			}
		}
		?>
		</td>
		<td valign="top"><?php

		$_returnValues = vDispatcher::trigger('plgVmOnShowOrderBEPayment',array( $this->orderID,$this->orderbt->virtuemart_paymentmethod_id, $this->orderdetails));

		foreach ($_returnValues as $_returnValue) {
			if ($_returnValue !== null) {
				echo $_returnValue;
			}
		}
		?></td>
	</tr>

</table>

</div>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea();

/*
<script type="text/javascript">


// jQuery('select#order_items_status').change(function() {
	////selectItemStatusCode
	// var statusCode = this.value;
	// jQuery('.selectItemStatusCode').val(statusCode);
	// return false
// });

</script>*/

vmJsApi::addJScript( '/administrator/components/com_virtuemart/assets/js/dynotable.js', false, false );

$j = 'jQuery(document).ready(function ($) {
        jQuery("#order-items-table").dynoTable({
            removeClass: ".order-item-remove", //remove class name in  table
            cloneClass: ".order-item-clone", //Custom cloner class name in  table
            addRowTemplateId: "#add-tmpl", //Custom id for  row template
            addRowButtonId: "#add-order-item", //Click this to add a new order item
            lastRowRemovable:true, //let the table be empty.
            orderable:true, //items can be rearranged
            dragHandleClass: ".order-item-ordering", //class for the click and draggable drag handle
            insertRowPlace: ".order-item", //class for the click and draggable drag handle
            onRowRemove:function () {
            },
            onBeforeRowInsert:function (newTr) {
            	var randomNumber = Math.floor(Math.random() * 100);
            	$(newTr).find("*").addBack().filter("[name]").each(function () {
            		var name=this.name;
            		var needle = "item_id["
					var newname = name.replace(needle, needle+"0-"+randomNumber+"-");
                    this.name = newname;
                    this.id += randomNumber;
				});
            },
             onRowClone:function () {
            },
            onRowAdd:function (newTr) {
            	$(".orderEdit").show();
				$(".orderView").hide();
            },
            onTableEmpty:function () {
            },
            onRowReorder:function () {
            }
        });
        
      
        
    });';
vmJsApi::addJScript('dynotable_order_item_ini',$j);


?>