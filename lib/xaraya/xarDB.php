<?php
/**
 * Database Abstraction Layer API Helpers
 * 
 * @package database
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage database
 * @author Marco Canini
*/

/**
 * Initializes the database connection.
 *
 * This function loads up the db abstraction layer  and starts the database
 * connection using the required parameters then it sets
 * the table prefixes and xartables up and returns true
 * 
 * @access protected
 * @global array xarDB_systemArgs
 * @global object dbconn database connection object
 * @param string args[databaseType] database type to use
 * @param string args[databaseHost] database hostname
 * @param string args[databaseName] database name
 * @param string args[userName] database username
 * @param string args[password] database password
 * @param bool args[persistent] flag to say we want persistent connections (optional)
 * @param string args[systemTablePrefix] system table prefix
 * @param string args[siteTablePrefix] site table prefix
 * @param bool   args[doConnect] on inialisation, also connect, defaults to true if not specified
 * @param integer whatElseIsGoingLoaded
 * @return bool true on success, false on failure
 * @todo <marco> move template tag table definition somewhere else?
 * @todo get rid of GLOBALS
 */
function xarDB_init(array &$args, $whatElseIsGoingLoaded)
{
    if(!isset($args['doConnect'])) $args['doConnect'] = true;
    $GLOBALS['xarDB_systemArgs'] = $args;
    
    sys::import('xaraya.xarCreole');
    // Register postgres driver, since Creole uses a slightly different alias
    // We do this here so we can remove customisation from creole lib.
    xarDB::registerDriver('postgres','creole.drivers.pgsql.PgSQLConnection');
    
    if($args['doConnect']) 
        $dbconn =& xarDBNewConn();
    $systemPrefix = $args['systemTablePrefix'];
    $sitePrefix   = $args['siteTablePrefix'];

    // BlockLayout Template Engine Tables
    // FIXME: this doesnt belong here
    // Not trivial to move out though
    $table['template_tags'] = $systemPrefix . '_template_tags';
    xarDB::importTables($table);
    return true;
}

/**
 * Get a database connection
 *
 * @access public
 * @return object database connection object
 */
function &xarDBGetConn($index=0)
{
    // we only want to return the first connection here
    // perhaps we'll add linked list capabilities to this soon
    $tmp =& xarDB::getConn($index);
    return $tmp;
}

/**
 * Initialise a new db connection
 *
 * Create a new connection based on the supplied parameters
 * 
 * @access public
 * @todo   do we need the global?
 */
function &xarDBNewConn(array $args = null)
{
    if (!isset($args)) {
        $args =  $GLOBALS['xarDB_systemArgs'];
    }
    // Get database parameters
    $dbType  = $args['databaseType'];
    $dbHost  = $args['databaseHost'];
    $dbName  = $args['databaseName'];
    $dbUname = $args['userName'];
    $dbPass  = $args['password'];
    $persistent = !empty($args['persistent']) ? true : false;

    $dsn = array('phptype'   => $dbType,
                 'hostspec'  => $dbHost,
                 'username'  => $dbUname,
                 'password'  => $dbPass,
                 'database'  => $dbName);
    // Set flags
    $flags = 0;
    if($persistent) $flags |= xarDB::PERSISTENT;
    $conn = null;
    $conn = xarDB::getConnection($dsn,$flags);
    
    // if code uses assoc fetching and makes a mess of column names, correct
    // this by forcing returns to be lowercase
    // <mrb> : this is not for nothing a COMPAT flag. the problem still lies
    //         in creating the database schema case sensitive in the first 
    //         place. Unfortunately, that is just not portable.
    $flags |= xarDB::COMPAT_ASSOC_LOWER;
    
    $conn = null;
    $conn = xarDB::getConnection($dsn,$flags); // cached on dsn hash, so no worries
    xarLogMessage("New connection created, now serving " . count(xarDB::$count) . " connections");
    return $conn;
}

/**
 * Get an array of database tables
 *
 * @access public
 * @return array array of database tables
 * @todo we should figure something out so we dont have to do the getTables stuff, it should be transparent
 */
function &xarDBGetTables()
{
    $tmp =& xarDB::getTables();
    return $tmp;
}

/**
 * Load the Table Maintenance API
 * @access public
 * @return true
 * @todo <johnny> change to protected or private?
 * @todo <mrb> Insane function name
 * @todo <mrb> This needs to be replaced by datadict functionality
 * @todo <mrb> kinda useless now
 */
function xarDBLoadTableMaintenanceAPI()
{
    return sys::import('xaraya.xarTableDDL');
}

/**
 * Create a data dictionary object
 *
 * This function will load the appropriate classes and instantiate
 * a data dictionary object for the specified mode. The default mode
 * is 'READONLY', which just provides methods for reading the data
 * dictionary. Mode 'METADATA' will return the meta data object.
 * Mode 'ALTERTABLE' will provide methods for altering schemas
 * (creating, removing and changing tables, indexes, constraints, etc).
 * Mode 'ALTERDATABASE' will provide the highest level of commands
 * for creating, dropping and changing databases.
 *
 * NOTE: until the data dictionary is split into separate classes
 * all modes except METADATA will return the ALTERDATABASE object.
 *
 * @access public
 * @return Object     dictionary object (specifics depend on mode)
 * @param  Connection $dbconn database connection object
 * @param  string     $mode the mode in which the data dictionary will be used; default READONLY
 * @todo   fully implement the mode, by layering the classes into separate files of readonly and amend methods
 * @todo   xarMetaData class needs to accept the database connection object
 * @todo   make xarMetaData the base class for the data dictionary
 * @todo   move these comments off to some proper document
 */
function &xarDBNewDataDict(Connection &$dbconn, $mode = 'READONLY')
{
    // Load the data dictionary source.
    // Depending on the mode, there may be one or more files to load.
    sys::import('xaraya.xarDataDict');

    // Decide which class to use.
    if ($mode == 'METADATA') {
        $class = 'xarMetaData';
    } else {
        // 'READONLY', 'ALTERTABLE', 'ALTERDATABASE' or other.
        $class = 'xarDataDict';
    }

    // Instantiate the object.
    $dict = new $class($dbconn);

    return $dict;
}

/**
 * Get the database host
 *
 * @access public
 * @return string
 * @todo no added value
 */
function xarDBGetHost()
{
    return xarDB::getHost();
}

/**
 * Get the database type
 *
 * @access public
 * @return string
 * @todo no added value
 */
function xarDBGetType()
{
    return xarDB::getType();
}

/**
 * Get the database name
 *
 * @access public
 * @return string
 * @todo no added value
 */
function xarDBGetName()
{
    return xarDB::getName();
}

/**
 * Get the system table prefix
 *
 * @access public
 * @return string
 * @todo no added value
 */
function xarDBGetSystemTablePrefix()
{
    return $GLOBALS['xarDB_systemArgs']['systemTablePrefix'];
}

/**
 * Get the site table prefix
 *
 * @access public
 * @return string
 * @todo change it back to return site table prefix
 *       when we decide how to store site information
 */
function xarDBGetSiteTablePrefix()
{
    //return $GLOBALS['xarDB_systemArgs']['siteTablePrefix'];
    return xarDBGetSystemTablePrefix();
}
?>