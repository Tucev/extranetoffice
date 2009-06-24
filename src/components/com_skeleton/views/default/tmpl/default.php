<?php
/**
 * src/components/com_skeleton/views/default/tmpl/default.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_skeleton
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */
?>

<ul>
<?php foreach ($data['rows'] as $row) : ?>
    <li><?php echo $row->firstname; ?> <?php echo $row->lastname; ?></li>
<?php  endforeach; ?>
</ul>
