<?php
/**
 * src/components/com_admin/views/organisations/tmpl/default_form.php
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

PHPFrame_HTML::validate('organisationsform');
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<form action="index.php" method="post" id="organisationsform" name="organisationsform">

    <fieldset>
        <legend><?php echo PHPFrame_Base_String::html( _LANG_ADMIN_ORGANISATION_DETAILS ); ?></legend>
        
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
        <tr>
            <td width="22%"><?php echo PHPFrame_Base_String::html( _LANG_NAME ); ?></td>
            <td><input class="required" type="text" size="30" name="name" value="<?php if (isset($data['row'])) echo $data['row']->name; ?>" /></td>
        </tr>
        </table>
    </fieldset>

<div style="clear:both; margin-top:30px;"></div>

<?php if (PHPFrame::Request()->get('tmpl') != 'component') : ?>
<button type="button" onclick="Javascript:window.history.back();"><?php echo PHPFrame_Base_String::html( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SAVE); ?></button>
<?php endif; ?>

<input type="hidden" name="id" value="<?php if (isset($data['row'])) echo $data['row']->id;?>" />
<input type="hidden" name="component" value="com_admin" />
<input type="hidden" name="action" value="save_organisation" />
<input type="hidden" name="tmpl" value="<?php echo PHPFrame::Request()->get('tmpl', ''); ?>" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>
</form>
