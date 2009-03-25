<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<div class="new">
	<a href="<?php echo route::_('index.php?option=com_email&view=accounts&layout=form'); ?>">
		<?php echo text::_( _LANG_NEW ); ?>
	</a>
</div>

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php foreach($this->rows as $row) : ?>
<div class="row_icons">
	
	<a href="<?php echo route::_("index.php?option=com_email&view=accounts&layout=form&accountid=".$row->id); ?>">
		<?php echo $row->email_address; ?>
	</a>

</div>
<?php endforeach; ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>