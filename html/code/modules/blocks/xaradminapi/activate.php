<?php
/**
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Blocks module
 * @link http://xaraya.com/index.php/release/13.html
 */
/**
 * activate a block
 * @author Jim McDonald, Paul Rosania
 * @param $args['bid'] the ID of the block to activate
 * @returns bool
 * @return true on success, false on failure
 */
function blocks_adminapi_activate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($bid) || !is_numeric($bid)) throw new BadParameterException('bid');

    // Security
    if(!xarSecurityCheck('CommentBlock',1,'Block',"::$bid")) {return;}

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $blockstable = $xartable['block_instances'];

    // Activate
    $query = "UPDATE $blockstable SET state=? WHERE id=?";
    $dbconn->Execute($query,array(2,$bid));
    return true;
}

?>