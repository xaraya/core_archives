<?php
/**
 * Main admin function
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authsystem module
 * @link http://xaraya.com/index.php/release/42.html
 */
/**
 * the main administration function
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function authsystem_admin_main()
{
    if (!xarSecurityCheck('EditAuthsystem')) return;
   
    $refererinfo = xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info = xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
        return xarTplModule('authsystem','admin','overview');
    } else {
        xarResponse::redirect(xarModURL('authsystem', 'admin', 'modifyconfig'));
        return true;
    }
}
?>