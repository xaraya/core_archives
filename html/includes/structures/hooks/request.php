<?php

class RequestObject extends Object
{
    protected $module;
    protected $type;
    protected $function;

    function __construct($module='base', $type='user', $function='main', $itemtype='All')
    {
        $this->module = $module;
        $this->type = $type;
        $this->function = $function;
        $this->itemtype = $itemtype;
    }
    
    function getmodule() 
    { return $this->module; }
    
    function getitemtype() 
    { return $this->itemtype; }
    
    function gettype() 
    { return $this->type; }
    
    function getfunction() 
    { return $this->function; }

    function register($hookObject='module', $hookAction='', $hookArea='API')
    {
        if (empty($hookAction)) return true;
        xarModRegisterHook($hookObject, $hookAction, $hookArea, $this->getmodule(), $this->gettype(), $this->getfunction());
    }
    function unregister($hookObject='module', $hookAction='', $hookArea='API')
    {
        if (empty($hookAction)) return true;
        xarModUnregisterHook($hookObject, $hookAction, $hookArea, $this->getmodule(), $this->gettype(), $this->getfunction());
    }
}
?>