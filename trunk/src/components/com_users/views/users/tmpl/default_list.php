<?php
/**
 * src/components/com_users/views/users/tmpl/default_list.php
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

<?php if ($data['rows']->countRows() > 0) : ?>

<?php foreach($data['rows'] as $row) : ?>
<div class="row_icons">
    
    <a href="<?php echo $row->detail_url; ?>">
    	<img border="0" src="<?php echo $row->photo; ?>" 
         	 alt="<?php echo $row->firstname." ".$row->lastname; ?>" />
    </a>
    
    <div class="row_icons_heading">
    	<a href="<?php echo $row->detail_url; ?>" 
       	   title="<?php echo $row->firstname." ".$row->lastname; ?>"> 
    
             <?php 
             $shortlastname = PHPFrame_Base_String::limitChars($row->lastname, 10);
             echo PHPFrame_User_Helper::fullname_format($row->firstname, $shortlastname);
             ?>
        
    	</a>
    </div>

</div>
<?php endforeach; ?>

<?php else : ?>
<?php echo PHPFrame_Base_String::html(_LANG_NO_ENTRIES); ?>
<?php endif; ?>