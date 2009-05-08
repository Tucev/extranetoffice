<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

//var_dump($data); exit;
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<div class="new">
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_addressbook&view=contacts&layout=form'); ?>" title="<?php echo phpFrame_HTML_Text::_( _LANG_ADDRESSBOOK_CONTACT_NEW ); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_ADDRESSBOOK_CONTACT_NEW ); ?>
	</a>
</div>

<br />

<script language="javascript" type="text/javascript">
function submit_filter(reset) {
	var form = document.forms['listsearchform'];
	
	if (reset){
		form.search.value = '';
	}
	
	form.submit();
}
</script>
<div class="list_filter_container">
<form action="index.php" id="listsearchform" name="listsearchform" method="post">
<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>">
<button type="button" class="button" onclick="submit_filter(false);">Search</button>
<button type="button" class="button" onclick="submit_filter(true);">Reset</button>
<input type="hidden" name="component" value="com_addressbook" />
<input type="hidden" name="view" value="contacts" />
<input type="hidden" name="layout" value="list" />
</form>
</div>

<br />

<?php if (is_array($data['rows']) && count($data['rows']) > 0) : ?>
<table class="data_list" width="100%" cellpadding="0" cellspacing="0">
  <thead>
  <tr>
    <th><?php echo _LANG_CONTACTS_DISPLAY_NAME; ?></th>
    <th><?php echo _LANG_CONTACTS_EMAIL; ?></th>
    <th><?php echo _LANG_CONTACTS_PHONE; ?></th>
    <th></th>
    <th></th>
  </tr>
  </thead>
  <tbody>
  <?php $k = 0; ?>
  <?php foreach($data['rows'] as $contact) : ?>
  <tr class="row<?php echo $k; ?>">
    <td valign="top">
    <a href="index.php?option=com_addressbook&view=contacts&layout=form&id=<?php echo $contact->id; ?>">
	<?php echo $contact->fn; ?>
	</a>
    </td>
    <td>
    
    	<?php if (!empty($contact->home_email)) : ?>
    	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=messages&layout=form&to=".$contact->home_email); ?>">
    		<?php echo $contact->home_email;  ?>
    	</a><br />
    	<?php endif; ?>
    	
    	<?php if (!empty($contact->work_email)) : ?>
    	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=messages&layout=form&to=".$contact->work_email); ?>">
    	<?php echo $contact->work_email; ?>
    	</a><br />
    	<?php endif; ?>
    	
    	<?php if (!empty($contact->other_email)) : ?>
    	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=messages&layout=form&to=".$contact->other_email); ?>">
    	<?php echo $contact->other_email; ?>
    	</a><br />
    	<?php endif; ?>
    	
    </td>
    <td>
    	<?php if (!empty($contact->home_phone)) echo $contact->home_phone.'<br />'; ?>
    	<?php if (!empty($contact->work_phone)) echo $contact->work_phone.'<br />'; ?>
    	<?php if (!empty($contact->cell_phone)) echo $contact->cell_phone.'<br />'; ?>
    </td>
	<td>
		<a href="index.php?component=com_addressbook&action=export_contacts&id=<?php echo $contact->id; ?>">
		<?php echo _LANG_EXPORT_VCARD; ?>
		</a>
	</td>
	<td>
		<a href="index.php?option=com_intranetoffice&amp;view=contacts&amp;type=edit&amp;id=<?php echo $contact->id; ?>" title="<?php echo phpFrame_HTML_Text::_( _LANG_EDIT ); ?>">
			<img src="templates/<?php echo config::TEMPLATE; ?>/images/icons/generic/16x16/edit.png" alt="<?php echo phpFrame_HTML_Text::_( _LANG_EDIT ); ?>" />
		</a>
		
		<a href="Javascript:confirm_delete(<?php echo $contact->id; ?>, '<?php echo phpFrame_HTML_Text::_($contact->fn, true); ?>');" title="<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>">
			<img src="templates/<?php echo config::TEMPLATE; ?>/mages/icons/generic/16x16/remove.png" alt="<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>" />
		</a>
	</td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<br />

<?php echo $data['page_nav']->getListFooter(); ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>