<?php
/**
 * Update a users status
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Roles module
 * @link http://xaraya.com/index.php/release/27.html
 */
/**
 * Update a users status
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @param $args['uname'] is the users system name
 * @param $args['state'] is the new state for the user
 * returns bool
 */
function roles_userapi_updatestatus($args)
{
    extract($args);

    if (!isset($uname)) throw new EmptyParameterException('uname');
    if (!isset($state)) throw new EmptyParameterException('state');

    if (!xarSecurityCheck('ViewRoles')) return;

    // Get DB Set-up
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $rolesTable = $xartable['roles'];

    // Update the status
    $query = "UPDATE $rolesTable
              SET valcode = ?, state = ?
              WHERE uname = ?";
    $bindvars = array('',$state,$uname);

    $dbconn->Execute($query,$bindvars);

    return true;
}

?>