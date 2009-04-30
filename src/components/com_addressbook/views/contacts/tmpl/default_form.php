<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_addressbook
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Load jQuery validation behaviour for form
phpFrame_HTML::validate('contactsform');
?>

<!-- add jQuery tabs behaviour -->
<script type="text/javascript">
$(function() {
	$('#contact_tabs').tabs();
});
</script>

<div class="componentheading"><?php echo $this->page_title; ?></div>

<form action="index.php" method="post" name="contactsform" id="contactsform">

<div id="contact_tabs">

<ul>
	<li><a href="#general">General</a></li>
	<li><a href="#postal">Mailing Address</a></li>
</ul>

<div id="general">

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_CONTACTS_BASIC_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="givenmsg" for="given">
			<?php echo _LANG_CONTACTS_FIRST_NAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="given" name="given" size="40" maxlength="100" value="<?php echo $this->rows[0]->given; ?>" />
	</td>
</tr>
<tr>
	<td>
		<label id="familymsg" for="family">
			<?php echo _LANG_CONTACTS_LAST_NAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="family" name="family" size="40" maxlength="100" value="<?php echo $this->rows[0]->family; ?>" />
	</td>
</tr>
<tr>
	<td>
		<label id="fnmsg" for="fn">
			<?php echo _LANG_CONTACTS_DISPLAY_NAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" class="required" id="fn" name="fn" size="40" maxlength="100" value="<?php echo $this->rows[0]->fn; ?>" />
	</td>
</tr>
<tr>
	<td>
		<label id="accessmsg" for="access">
			<?php echo _LANG_CONTACTS_ACCESS; ?>
		</label>
	</td>
	<td>
		<input type="radio" name="access" value="0" <?php if ($this->rows[0]->access == 0) echo 'checked'; ?> /> <?php echo _LANG_CONTACTS_PRIVATE; ?> 
		<input type="radio" name="access" value="1" <?php if ($this->rows[0]->access == 1) echo 'checked'; ?> /> <?php echo _LANG_CONTACTS_PUBLIC; ?> 
	</td>
</tr>
<tr>
	<td>
		<label id="categorymsg" for="category">
			<?php echo _LANG_CONTACTS_CATEGORY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="category" name="category" size="40" maxlength="100" value="<?php echo $this->rows[0]->category; ?>" />
	</td>
</tr>
</table>
</fieldset>

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_CONTACTS_EMAIL_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="home_emailmsg" for="home_email">
			<?php echo _LANG_CONTACTS_HOME_EMAIL; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_email" name="home_email" size="40" maxlength="100" value="<?php echo $this->rows[0]->home_email; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_emailmsg" for="work_email">
			<?php echo _LANG_CONTACTS_WORK_EMAIL; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_email" name="work_email" size="40" maxlength="100" value="<?php echo $this->rows[0]->work_email; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="other_emailmsg" for="other_email">
			<?php echo _LANG_CONTACTS_OTHER_EMAIL; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_email" name="other_email" size="40" maxlength="100" value="<?php echo $this->rows[0]->other_email; ?>" />
	</td>
</tr>
</table>
</fieldset>

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_CONTACTS_PHONE_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="home_phonemsg" for="home_phone">
			<?php echo _LANG_CONTACTS_HOME_PHONE; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_phone" name="home_phone" size="40" maxlength="100" value="<?php echo $this->rows[0]->home_phone; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_phonemsg" for="work_phone">
			<?php echo _LANG_CONTACTS_BUSINESS_PHONE; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_phone" name="work_phone" size="40" maxlength="100" value="<?php echo $this->rows[0]->work_phone; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="cell_phonemsg" for="cell_phone">
			<?php echo _LANG_CONTACTS_MOBILE_PHONE; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="cell_phone" name="cell_phone" size="40" maxlength="100" value="<?php echo $this->rows[0]->cell_phone; ?>" />
	</td>
</tr>
</table>
</fieldset>

</div><!-- close #general -->


<div id="postal">

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_CONTACTS_HOME_ADDRESS_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="home_streetmsg" for="home_street">
			<?php echo _LANG_CONTACTS_ADDRESS1; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_street" name="home_street" size="40" maxlength="50" value="<?php echo $this->rows[0]->home_street; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="home_extendedmsg" for="home_extended">
			<?php echo _LANG_CONTACTS_ADDRESS2; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_extended" name="home_extended" size="40" maxlength="50" value="<?php echo $this->rows[0]->home_extended; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="home_localitymsg" for="home_locality">
			<?php echo _LANG_CONTACTS_LOCALITY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_locality" name="home_locality" size="30" maxlength="30" value="<?php echo $this->rows[0]->home_locality; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="home_regionmsg" for="home_region">
			<?php echo _LANG_CONTACTS_REGION; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_region" name="home_region" size="30" maxlength="30" value="<?php echo $this->rows[0]->home_region; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="home_postcodemsg" for="home_postcode">
			<?php echo _LANG_CONTACTS_POST_CODE; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_postcode" name="home_postcode" size="30" maxlength="30" value="<?php echo $this->rows[0]->home_postcode; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="home_countrymsg" for="home_country">
			<?php echo _LANG_CONTACTS_COUNTRY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="home_country" name="home_country" size="30" maxlength="30" value="<?php echo $this->rows[0]->home_country; ?>" />
	</td>
</tr>

</table>
</fieldset>


<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_CONTACTS_WORK_ADDRESS_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="work_streetmsg" for="work_street">
			<?php echo _LANG_CONTACTS_ADDRESS1; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_street" name="work_street" size="40" maxlength="50" value="<?php echo $this->rows[0]->work_street; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_extendedmsg" for="work_extended">
			<?php echo _LANG_CONTACTS_ADDRESS2; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_extended" name="work_extended" size="40" maxlength="50" value="<?php echo $this->rows[0]->work_extended; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_localitymsg" for="work_locality">
			<?php echo _LANG_CONTACTS_LOCALITY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_locality" name="work_locality" size="30" maxlength="30" value="<?php echo $this->rows[0]->work_locality; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_regionmsg" for="work_region">
			<?php echo _LANG_CONTACTS_REGION; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_region" name="work_region" size="30" maxlength="30" value="<?php echo $this->rows[0]->work_region; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_postcodemsg" for="work_postcode">
			<?php echo _LANG_CONTACTS_POST_CODE; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_postcode" name="work_postcode" size="30" maxlength="30" value="<?php echo $this->rows[0]->work_postcode; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="work_countrymsg" for="work_country">
			<?php echo _LANG_CONTACTS_COUNTRY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="work_country" name="work_country" size="30" maxlength="30" value="<?php echo $this->rows[0]->work_country; ?>" />
	</td>
</tr>

</table>
</fieldset>


<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( _LANG_CONTACTS_OTHER_ADDRESS_DETAILS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="other_streetmsg" for="other_street">
			<?php echo _LANG_CONTACTS_ADDRESS1; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_street" name="other_street" size="40" maxlength="50" value="<?php echo $this->rows[0]->other_street; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="other_extendedmsg" for="other_extended">
			<?php echo _LANG_CONTACTS_ADDRESS2; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_extended" name="other_extended" size="40" maxlength="50" value="<?php echo $this->rows[0]->other_extended; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="other_localitymsg" for="other_locality">
			<?php echo _LANG_CONTACTS_LOCALITY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_locality" name="other_locality" size="30" maxlength="30" value="<?php echo $this->rows[0]->other_locality; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="other_regionmsg" for="other_region">
			<?php echo _LANG_CONTACTS_REGION; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_region" name="other_region" size="30" maxlength="30" value="<?php echo $this->rows[0]->other_region; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="other_postcodemsg" for="other_postcode">
			<?php echo _LANG_CONTACTS_POST_CODE; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_postcode" name="other_postcode" size="30" maxlength="30" value="<?php echo $this->rows[0]->other_postcode; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="other_countrymsg" for="other_country">
			<?php echo _LANG_CONTACTS_COUNTRY; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="other_country" name="other_country" size="30" maxlength="30" value="<?php echo $this->rows[0]->other_country; ?>" />
	</td>
</tr>

</table>
</fieldset>

</div><!-- close #postal -->
</div>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="id" value="<?php echo $this->rows[0]->id;?>" />
<input type="hidden" name="component" value="com_addressbook" />
<input type="hidden" name="action" value="save_contact" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>