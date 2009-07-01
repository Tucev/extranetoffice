<?php
/**
 * src/components/com_admin/views/organisations/tmpl/default_list.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

PHPFrame_HTML::confirm(
    'delete_organisation', 
    _LANG_ADMIN_ORGANISATIONS_DELETE, 
    _LANG_ADMIN_ORGANISATIONS_DELETE_CONFIRM, 
    "div[id^='ui-tabs']:has(a.delete_organisation)"
);
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>


<?php if (PHPFrame::Session()->getUser()->groupid == 1) : ?>
<div class="new">
    <?php 
    PHPFrame_HTML::dialog(
        _LANG_ADMIN_ORGANISATIONS_NEW, 
        'index.php?component=com_admin&action=get_organisation_form', 
        550, 
        390, 
        true, 
        "div[id^='ui-tabs']:has(a.delete_organisation)"
    ); 
    ?>
</div>
<?php endif; ?>

<br />

<?php echo $this->renderRowCollectionFilter($data['rows']); ?>

<br />

<?php if ($data['rows']->countRows() > 0) : ?>
<table class="data_list" width="100%" cellpadding="0" cellspacing="0">
  <thead>
  <tr>
    <th></th>
    <th><?php echo _LANG_ORGANISATIONS_NAME; ?></th>
    <th></th>
  </tr>
  </thead>
  <tbody>
  <?php $k = 0; ?>
  <?php foreach($data['rows'] as $row) : ?>
  <tr class="row<?php echo $k; ?>">
    <td>
        <input type="checkbox" name="ids[]" value="<?php echo $row->id; ?>" />
    </td>
    <td valign="top">
    	<?php 
    	PHPFrame_HTML::dialog(
    	    $row->name, 
    	    'index.php?component=com_admin&action=get_organisation_form&organisationid='.$row->id, 
    	    550, 
    	    390, 
    	    true, 
    	    "div[id^='ui-tabs']:has(a.delete_organisation)"
    	); 
    	?>
	</td>
	<td>
        <a class="delete_organisation" 
           title="<?php echo $row->name; ?>" 
           href="index.php?component=com_admin&action=remove_organisation&organisationid=<?php echo $row->id; ?>">
            <?php echo _LANG_DELETE; ?>
        </a>
    </td>
  </tr>
  <?php $k = 1 - $k; ?>
  <?php endforeach; ?>
  </tbody>
</table>

<br />

<?php echo $this->renderPagination($data['rows']); ?>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>