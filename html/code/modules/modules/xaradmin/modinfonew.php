<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Module System
 * @link http://xaraya.com/index.php/release/1.html
 */
/**
 * View complete module information/details
 * function passes the data to the template
 * opens in new window when browser is javascript enabled
 * @author Xaraya Development Team
 * @access public
 * @param none
 * @returns array
 * @todo some facelift
 */
function modules_admin_modinfonew()
{
    $data = array();
    
    if (!xarVarFetch('id', 'notempty', $id)) {return;}

    // obtain maximum information about module
    $modinfo = xarMod::getInfo($id);
    
    // data vars for template
    $data['modid']              = xarVarPrepForDisplay($id);
    $data['modname']            = xarVarPrepForDisplay($modinfo['name']);
    $data['moddescr']           = xarVarPrepForDisplay($modinfo['description']);
    $data['moddispname']        = xarVarPrepForDisplay($modinfo['displayname']);
    $data['moddispdesc']        = xarVarPrepForDisplay($modinfo['displaydescription']);
    $data['modlisturl']         = xarModURL('modules', 'admin', 'list');

    $data['modiconurl'] = sys::code() . 'modules/modules/xarimages/xarmodicon_96px.gif';

    $aliasesMap = xarConfigVars::get(null,'System.ModuleAliases');
    $aliases = array();
    foreach ($aliasesMap as $key => $value) {
        if ($value == $data['modname']) $aliases[] = $key;
    }
    $data['aliases']            = !empty($aliases) ? implode(', ', $aliases) : xarML('None');
    $data['moddir']             = sys::code(). 'modules/' . xarVarPrepForDisplay($modinfo['directory']);
    $data['modclass']           = xarVarPrepForDisplay($modinfo['class']);
    $data['modcat']             = xarVarPrepForDisplay($modinfo['category']);
    $data['modver']             = xarVarPrepForDisplay($modinfo['version']);
    $data['modauthor']          = xarVarPrepForDisplay($modinfo['author']);
    $data['modcontact']         = xarVarPrepForDisplay($modinfo['contact']);
    if(!empty($modinfo['dependencyinfo'])){
        $dependencies = array();
        foreach ($modinfo['dependencyinfo'] as $key => $value) {
            $dependencies[] = $value . ' (' . $key . ')';
            $data['moddependencies'] = implode(', ', $dependencies);
        }
    } else {
        $data['moddependencies']             = xarML('None');
    }
    
    return $data;
}

?>