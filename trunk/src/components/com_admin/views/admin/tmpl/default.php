<?php
/**
 * src/components/com_admin/views/admin/tmpl/default.php
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
?>

<!-- add jQuery tabs behaviour -->
<script type="text/javascript">
var $sysadmin_tabs;
var $sysadmin_selected_tab='ui-tabs-30';

$(function() {
    $sysadmin_tabs = $('#sysadmin_tabs');
    $sysadmin_tabs.tabs();
    $sysadmin_tabs.bind('tabsload', function(event, ui) {
        $sysadmin_selected_tab = ui.panel.id;
    });
    $sysadmin_tabs.bind('tabsselect', function(event, ui) {
        $sysadmin_selected_tab = ui.panel.id;
        $sysadmin_unselected_tabs = $(this).find("div[id^='ui-tabs']:not(div#"+$sysadmin_selected_tab+")");
        $sysadmin_unselected_tabs.html('');
    });
});
</script>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<div id="sysadmin_tabs">
    <ul>
        <li>
            <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_admin&action=get_config".$data['tmpl']); ?>">
                Global Config
            </a>
        </li>
        <li>
            <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_admin&action=get_users".$data['tmpl']); ?>">
                Users
            </a>
        </li>
        <li>
            <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_admin&action=get_components".$data['tmpl']); ?>">
                Components
            </a>
        </li>
        <li>
            <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_admin&action=get_modules".$data['tmpl']); ?>">
                Modules
            </a>
        </li>
    </ul>
</div>
