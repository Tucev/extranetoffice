<?php
/**
 * src/components/com_users/views/users/tmpl/default_detail.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<?php if (is_object($data['row'])) : ?>

<div class="row_icons">
    
    <img border="0" src="<?php echo $data['row']->photo; ?>" 
         alt="<?php echo $data['row']->firstname." ".$data['row']->lastname; ?>" />
    
    <div class="row_icons_heading">
        <?php
        $shortlastname = PHPFrame_Base_String::limitChars($data['row']->lastname, 10);
        echo PHPFrame_User_Helper::fullname_format($data['row']->firstname, $shortlastname);
        ?>
    </div>

</div>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>