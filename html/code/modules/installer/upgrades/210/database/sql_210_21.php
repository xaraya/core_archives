<?php

function sql_210_21()
{
    // Define parameters
    $table['modules'] = xarDB::getPrefix() . '_modules';
    $table['block_types'] = xarDB::getPrefix() . '_block_types';
    $table['block_instances'] = xarDB::getPrefix() . '_block_instances';
    $table['block_groups'] = xarDB::getPrefix() . '_block_groups';
    $table['block_group_instances'] = xarDB::getPrefix() . '_block_group_instances';

    // Define the task and result
    $data['success'] = true;
    $data['task'] = xarML("
        Changing the blocks of type 'html', 'php' and 'text' to 'content'
    ");
    $data['reply'] = xarML("
        Done!
    ");

    // Run the query
    $dbconn = xarDB::getConn();
    try {
        $dbconn->begin();
        $data['sql'] = "
        UPDATE $table[block_instances] SET type_id = 
            (SELECT id FROM $table[block_types] WHERE name = 'content') WHERE type_id = 
            (SELECT id FROM $table[block_types] WHERE name = 'html');
        ";
        $dbconn->Execute($data['sql']);
        $data['sql'] = "
        UPDATE $table[block_instances] SET type_id = 
            (SELECT id FROM $table[block_types] WHERE name = 'content') WHERE type_id = 
            (SELECT id FROM $table[block_types] WHERE name = 'php');
        ";
        $dbconn->Execute($data['sql']);
        $data['sql'] = "
        UPDATE $table[block_instances] SET type_id = 
            (SELECT id FROM $table[block_types] WHERE name = 'content') WHERE type_id = 
            (SELECT id FROM $table[block_types] WHERE name = 'text');
        ";
        $dbconn->Execute($data['sql']);
        $dbconn->commit();
    } catch (Exception $e) {
        // Damn
        $dbconn->rollback();
        $data['success'] = false;
        $data['reply'] = xarML("
        Failed!
        ");
    }
    return $data;
}
?>