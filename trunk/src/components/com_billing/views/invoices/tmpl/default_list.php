<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_billing
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<h2><?php echo $this->page_title; ?></h2>

<?php
echo "From: ".$dtstart_yyyy."-".$dtstart_mm."-".$dtstart_dd." To: ".$dtend_yyyy."-".$dtend_mm."-".$dtend_dd;
exit;
$basis = $filter['basis'];
$type = $filter['type'];
$pluginused = $filter['pluginused'];
?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(task) {
	var form = document.filterForm;
			
	if (task == 'export') {
		form.task.value = 'export';
	}
	else {
		form.task.value = '';
	}
			
	form.submit();
}
//-->
</script>
		
<div class="list_filter_container">
		
		<form name="filterForm" id="filterForm" action="index.php" method="POST">
		
		From: 
		<select name="dtstart_dd">
			<?php for ($i=1; $i<32; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php if ($i == $dtstart_dd) echo 'selected'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		<select name="dtstart_mm">
			<?php for ($i=1; $i<13; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php if ($i == $dtstart_mm) echo 'selected'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		<select name="dtstart_yyyy">
			<?php for ($i=2006; $i<2010; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php if ($i == $dtstart_yyyy) echo 'selected'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		
		To: 
		<select name="dtend_dd">
			<?php for ($i=1; $i<32; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php if ($i == $dtend_dd) echo 'selected'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		<select name="dtend_mm">
			<?php for ($i=1; $i<13; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php if ($i == $dtend_mm) echo 'selected'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		<select name="dtend_yyyy">
			<?php for ($i=2006; $i<2010; $i++) : ?>
			<option value="<?php echo $i; ?>" <?php if ($i == $dtend_yyyy) echo 'selected'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		
		<br />
		<br />
		<select name="basis">
			<option value="datepaid" <?php if ($basis == 'datepaid') { echo 'selected'; } ?>>Date Paid</option>
			<option value="billdate" <?php if ($basis == 'billdate') { echo 'selected'; } ?>>Date Billed</option>
		</select>
		
		<select name="type">
			<option value="">All</option>
			<option value="hosting" <?php if ($type == 'hosting') { echo 'selected'; } ?>>Hosting</option>
			<option value="development" <?php if ($type == 'development') { echo 'selected'; } ?>>Development</option>
			<option value="consultancy" <?php if ($type == 'consultancy') { echo 'selected'; } ?>>Consultancy</option>
			<option value="training" <?php if ($type == 'training') { echo 'selected'; } ?>>Training</option>
			<option value="other" <?php if ($type == 'other') { echo 'selected'; } ?>>Other</option>
		</select>
		
		<select name="pluginused">
			<option value="">All</option>
			<option value="2checkout" <?php if ($pluginused == '2checkout') { echo 'selected'; } ?>>2checkout</option>
			<option value="paypal" <?php if ($pluginused == 'paypal') { echo 'selected'; } ?>>PayPal</option>
			<option value="Undefined" <?php if ($pluginused == 'Undefined') { echo 'selected'; } ?>>Undefined</option>
		</select>
		
		<br />
		<br />
		
		<input type="hidden" name="option" value="com_invoices" />
		<input type="hidden" name="task" value="" />
		
		<input type="button" name="show_date_range_btn" value="Show Date Range" onclick="submitbutton();" />
		<input type="button" name="export_qif_btn" value="Export QIF" onclick="submitbutton('export');" />
		</form>
		
		</div><!-- close .list_filter_container -->
		
		
		<h4>Period: Between <?php echo $filter['date_range']; ?></h4>
		Records: <?php echo count($rows); ?>
		
		<table class="report" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th>id</th>
			<th>customerid</th>
			<th>name</th>
			<th>organisation</th>
			<th>billdate</th>
			<th>datepaid</th>
			<th>amount</th>
			<th>subtotal</th>
			<th>VAT</th>
			<th>paid</th>
			<th>pluginused</th>
			<th>type</th>
		</tr>
		<?php if (is_array($rows)) : ?>
		<?php $k=0; ?>
		<?php foreach ($rows as $row) : ?>
		<tr class="row<?php echo $k; ?>">
			<?php $invoice_link = $path_to_ce."index.php?frmClientID=".$row->customerid."&billdetailid=".$row->id."&fuse=clients&view=ViewInvoices"; ?>
			<?php $client_link = $path_to_ce."index.php?fuse=clients&frmClientID=".$row->customerid."&view=ShowCustomerData"; ?>
			<td>
				<a href="<?php echo $invoice_link; ?>" target="_blank">
					<?php echo $row->id; ?>
				</a>
			</td>
			<td>
				<a href="<?php echo $client_link; ?>" target="_blank">
					<?php echo $row->customerid; ?>
				</a>
			</td>
			<td><?php echo $row->firstname.' '.$row->lastname; ?></td>
			<td><?php echo $row->organization; ?></td>
			<td><?php echo $row->billdate; ?></td>
			<td><?php echo $row->datepaid; ?></td>
			<td><?php echo number_format($row->amount, 2, '.', ','); ?></td>
			<td><?php echo number_format($row->subtotal, 2, '.', ','); ?></td>
			<td><?php echo number_format(($row->amount - $row->subtotal), 2, '.', ','); ?></td>
			<td><?php echo $row->paid; ?></td>
			<td><?php echo $row->pluginused; ?></td>
			<td><?php echo $row->type; ?></td>
			<?php $total_amount += $row->amount; ?>
			<?php $total_subtotal += $row->subtotal; ?>
			<?php $total_vat += ($row->amount - $row->subtotal); ?>
		</tr>
		<?php $k = 1 - $k; ?>
		<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td colspan="6"></td>
			<th><?php echo $total_amount; ?></th>
			<th><?php echo $total_subtotal; ?></th>
			<th><?php echo $total_vat; ?></th>
			<td colspan="3"></td>
		</tr>
		</table>