<?php
/**
 * src/components/com_projects/views/people/tmpl/default_list.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/extranetoffice/source/browse
 */
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>

<h2 class="subheading <?php echo strtolower($data['tool']); ?>">
    <a href="<?php echo $data['tool_url']; ?>">
        <?php echo $data['tool']; ?>
    </a>
</h2>


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
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>