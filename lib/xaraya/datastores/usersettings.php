<?php
/**
 * Data Store is the user settings (user variables per module) // TODO: integrate user variable handling with DD
 *
 * @package dynamicdata
 * @subpackage datastores
 */

/**
 * User settings datastore
 *
 * @package dynamicdata
 */
class UserSettingsDataStore extends BasicDataStore // Consider inheriting from ModuleVar Datastore
{
    public $modname;

    function __construct($name)
    {
        // invoke the default constructor from our parent class
        parent::__construct($name);

        // keep track of the concerned module for user settings
        // @todo: the concerned module is currently hiding in the third part of the name :)
        list($fixed1,$fixed2,$modid) = explode('_',$name);
        if (empty($modid)) {
            $modid = xarMod::getRegID(xarMod::getName());
        }
        $modinfo = xarMod::getInfo($modid);
        if (!empty($modinfo['name'])) {
            $this->modname = $modinfo['name'];
        }
    }

    function getItem(array $args = array())
    {
        if (empty($args['itemid'])) {
            // default is the current user (if any)
            $itemid = xarUserGetVar('id');
        } else {
            $itemid = $args['itemid'];
        }

        $fieldlist = array_keys($this->fields);
        if (count($fieldlist) < 1) {
            return;
        }

        foreach ($fieldlist as $field) {
            // get the value from the user variables
            $value = xarModUserVars::get($this->modname,$field,$itemid);

            // set the value for this property
            if (isset($value)) {
                $this->fields[$field]->setValue($value);
            //} else {
                // use the equivalent module variable as default
            //    $this->fields[$field]->setValue(xarModVars::get($this->modname,$field));
            }
        }
        return $itemid;
    }

    function createItem(array $args = array())
    {
        // There's no difference with updateItem() here, because xarModUserVars:set() handles that
        return $this->updateItem($args);
    }

    function updateItem(array $args = array())
    {
        if (empty($args['itemid'])) {
            // default is the current user (if any)
            $itemid = xarUserGetVar('id');
        } else {
            $itemid = $args['itemid'];
        }

        $fieldlist = array_keys($this->fields);
        if (count($fieldlist) < 1) {
            return;
        }

        foreach ($fieldlist as $field) {
            // get the value from the corresponding property
            $value = $this->fields[$field]->getValue();
            // skip fields where values aren't set
            if (!isset($value)) {
                continue;
            }
            xarModUserVars::set($this->modname,$field,$value,$itemid);
        }
        return $itemid;
    }

    function deleteItem(array $args = array())
    {
        if (empty($args['itemid'])) {
            // default is the current user (if any)
            $itemid = xarUserGetVar('id');
        } else {
            $itemid = $args['itemid'];
        }

        $fieldlist = array_keys($this->fields);
        if (count($fieldlist) < 1) {
            return;
        }

        foreach ($fieldlist as $field) {
            xarModUserVars::delete($this->modname,$field,$itemid);
        }

        return $itemid;
    }

    function getItems(array $args = array())
    {
        // TODO: not supported by xarMod*UserVar
    }

    function countItems(array $args = array())
    {
        // TODO: not supported by xarMod*UserVar
        return 0;
    }

}

?>