<?php
function main_220()
{
    $data['upgrade']['message'] = xarML('The upgrade to version 2.2.0 was successfully completed');
    $data['upgrade']['tasks'] = array();
    
    $upgrades = array(
                        'sql_220_01',
                        'sql_220_02',
                    );
    foreach ($upgrades as $upgrade) {
        if (!Upgrader::loadFile('upgrades/220/database/' . $upgrade . '.php')) {
            $data['upgrade']['errormessage'] = Upgrader::$errormessage;
            return $data;
        }
        $result = $upgrade();
        $data['upgrade']['tasks'][] = array(
                            'reply' => $result['reply'],
                            'description' => $result['task'],
                            'reference' => $upgrade,
                            'success' => $result['success'],
                            );        
        if (!$result['success']) {
            $data['upgrade']['errormessage'] = xarML('Some parts of the upgrade failed. Check the reference(s) above to determine the cause.');
//            break;
        }
    }
    return $data;
}
?>