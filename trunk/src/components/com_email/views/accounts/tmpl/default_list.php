<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<div class="new">
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=accounts&layout=form'); ?>">
		<?php echo PHPFrame_HTML_Text::_( _LANG_NEW ); ?>
	</a>
</div>

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>
<table class="data_list" width="100%" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
    	<th><?php echo _LANG_CONFIG_SMTP_FROMNAME; ?></th>
    	<th><?php echo _LANG_EMAIL; ?></th>
    	<th><?php echo _LANG_EMAIL_SERVER_TYPE; ?></th>
    	<th><?php echo _LANG_CONFIG_IMAP_HOST; ?></th>
    	<th><?php echo _LANG_CONFIG_SMTP_HOST; ?></th>
    	<th></th>
    	<th></th>
	</tr>
	</thead>
	<?php foreach($this->rows as $row) : ?>
	<tbody>
	<tr>
		<td><?php echo $row->fromname; ?></td>
		<td>
		<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=accounts&layout=form&accountid=".$row->id); ?>">
			<?php echo $row->email_address; ?>
		</a>
		</td>
		<td><?php echo $row->server_type; ?></td>
		<td><?php echo $row->imap_host; ?></td>
		<td><?php echo $row->smtp_host; ?></td>
		<td>
			<?php if ($row->default == '1') : ?>
				Default
			<?php else : ?>
			<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&action=make_default_account&accountid=".$row->id); ?>">
				<?php echo PHPFrame_HTML_Text::_(_LANG_EMAIL_ACCOUNTS_MAKE_DEFAULT); ?>
			</a>
			<?php endif; ?>
		</td>
		<td>
			<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&action=remove_account&accountid=".$row->id); ?>">
				<?php echo PHPFrame_HTML_Text::_(_LANG_DELETE); ?>
			</a>
		</td>
	</tr>
	</tbody>
	<?php endforeach; ?>
</table>
<?php else : ?>
<?php echo PHPFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>