<?php
/**
 * Blocks module
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Blocks Module
 * @link http://xaraya.com/index.php/release/13.html
 * @author dracos
 */
/**
 * get an array of block info (id => foo) for use in dropdown lists
 *
 * Can get block instances (optionally filtered by group id), block types, 
 * or block groups
 *
 * @param $args['type'] type to return: instance (default), group, type
 * @param $args['gid'] group id (optional, sets type to instance if set)
 * @param $args['sort'] sort order, accoring to type param
 * @param $args['nullopt'] whether to include an empty option (default false)
 * @returns array
 * @return array of block instancess, block groups, or block types, or false on failure
 */
function blocks_userapi_dropdownlist($args)
{
    extract($args);
    
    if (!isset($type)) {
        $type = 'instance';
    }
    if ($type != 'instance' && $type != 'group' && $type != 'type') {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'type', 'user', 'dropdownlist',
                    'Blocks');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    
    if (isset($gid) && is_numeric($gid)) {
        $group = xarmodAPIFunc('blocks','user','getgroup', array('gid' => $gid));
        
        if (!$group) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                        'gid', 'user', 'dropdownlist',
                        'Blocks');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return false;
        }
        // now set the only relevant $type
        $type = 'instance';
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $block_instances_table = $xartable['block_instances'];
    $block_group_instances_table = $xartable['block_group_instances'];
    $block_types_table = $xartable['block_types'];
    $block_groups_table = $xartable['block_groups'];

    $items = array();

    if (isset($nullopt) && $nullopt == true) {
        $items[0] = '';
    }

    switch ($type) {
        case 'instance':
            if (isset($gid)) {
                if (isset($sort)) {
                    $sorts = explode(',', $sort);
                    $newsort = array();
                    foreach ($sorts as $s) {
                        if ($s == 'id' || $s == 'title' || $s == 'name') {
                            $newsort[] = 'binst.xar_' . $s;
                        } elseif ($s == 'position') {
                            $newsort[] = 'bginst.xar_' . $s;
                        }
                    }
                    $sort = implode(',',$newsort);
                } else {
                    $sort = 'bginst.xar_position';
                }
    
                $query = "SELECT binst.xar_id,
                                 binst.xar_name,
                                 binst.xar_title,
                                 bginst.xar_instance_id as giid,
                                 bginst.xar_group_id as gid,
                                 bginst.xar_position as position
                          FROM $block_instances_table binst
                          LEFT JOIN $block_group_instances_table bginst
                                ON bginst.xar_instance_id = binst.xar_id
                          WHERE bginst.xar_group_id = ?
                          ORDER BY $sort";
    
                $result =& $dbconn->Execute($query,array((int) $gid));

                if (!$result) return $items;

                while (!$result->EOF) {
                    list($id, $name, $title, $giid, $gid, $position) = $result->fields;
                    $items[$id] = $name;
                    $result->MoveNext();
                }

                $result->Close();
            } else {
                if (isset($sort)) {
                    $sorts = explode(',', $sort);
                    $newsort = array();
                    foreach ($sorts as $s) {
                        if ($s == 'id' || $s == 'title' || $s == 'name') {
                            $newsort[] = $s;
                        }
                    }
                    $sort = implode(', ',$newsort);
                } else {
                    $sort = 'xar_name';
                }

                $query = "SELECT 
                            xar_id, 
                            xar_name, 
                            xar_title
                        FROM 
                            $block_instances_table
                        ORDER BY 
                            $sort";

                $result =& $dbconn->Execute($query);
                
                if (!$result) return $items;

                while (!$result->EOF) {
                    list($id, $name, $title) = $result->fields;
                    $items[$id] =  $name;
                    $result->MoveNext();
                }

                $result->Close();
            }
            break;
        case 'group':
            // valid sort values: id (default), name
            if (!isset($sort) || ($sort != 'id' && $sort != 'name')) {
                $sort = 'name';
            }
            $groups = xarModAPIFunc('blocks','user','getallgroups', array('order' => $sort));
            foreach ($groups as $gid => $group) {
                $items[$gid] = $group['name'];
            }
            break;
        case 'type':
            if (isset($sort)) {
                $sorts = explode(',', $sort);
                $newsort = array();
                foreach ($sorts as $s) {
                    if ($s == 'id' || $s == 'module' || $s == 'type') {
                        $newsort[] = $s;
                    }
                }
                $sort = implode(',',$newsorts);
            }
            else {
                $sort = 'module,type';
            }

            $types = xarModAPIFunc('blocks','user','getallblocktypes', array('order' => $sort));
            foreach ($types as $tid => $btype) {
                $items[$tid] = $btype['module'] . '/' . $btype['type'];
            }
            break;
    }

    return $items;
}

?>