<?php
/**
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata
 * @link http://xaraya.com/index.php/release/182.html
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * Include the base class
 */
sys::import('modules.base.xarproperties.dropdown');

/**
 * Handle the object property
 *
 * Options available to user selection
 * ===================================
 * Options take the form:
 *   option-type:option-value;
 * option-types:
 *   static:true - add modules to the list
 */
class ObjectProperty extends SelectProperty
{
    public $id         = 24;
    public $name       = 'object';
    public $desc       = 'Object';
    public $reqmodules = array('dynamicdata');

    function __construct($args)
    {
        parent::__construct($args);
        $this->filepath   = 'modules/dynamicdata/xarproperties';

        if (!empty($this->validation)) {
            foreach(preg_split('/(?<!\\\);/', $this->validation) as $option) {
                // Semi-colons can be escaped with a '\' prefix.
                $option = str_replace('\;', ';', $option);
                // An option comes in two parts: option-type:option-value
                if (strchr($option, ':')) {
                    list($option_type, $option_value) = explode(':', $option, 2);
                    if ($option_type == 'static' && $option_value == 1) {
                        $includestatics = true;
                        $modlist = xarModAPIFunc('modules',
                                         'admin',
                                         'GetList');
                        foreach ($modlist as $modinfo) {
                            $this->options[] = array('id' => $modinfo['regid'], 'name' => $modinfo['displayname']);
                        }
                    }
                }
            }
        }
//        if (count($this->options) == 0) {
            $objects =& Dynamic_Object_Master::getObjects();
            if (!isset($objects)) {
                $objects = array();
            }
            foreach ($objects as $objectid => $object) {
                if (!empty($includestatics)) {
                    $ancestors = xarModAPIFunc('dynamicdata','user','getancestors',array('objectid' => $objectid, 'top' => false));
                    $name ="";
                    foreach ($ancestors as $parent) $name .= $parent['name'] . ".";
                    $this->options[] = array('id' => '182.' . $objectid, 'name' => $name . $object['name']);
                } else {
                    $this->options[] = array('id' => $objectid, 'name' => $object['name']);
                }
            }
//        }
    }
}

?>