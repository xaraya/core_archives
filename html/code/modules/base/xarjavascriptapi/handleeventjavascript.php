<?php
/**
 * Base JavaScript management functions
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Base module
 * @link http://xaraya.com/index.php/release/68.html
 */

/**
 * Handle render javascript form field tags
 * Handle <xar:base-trigger-javascript ...> form field tags
 * Format : <xar:place-javascript definition="$definition"/> with $definition an array
 *       or <xar:place-javascript position="head|body|whatever|" type="code|src|whatever|"/>
 * Default position is ''; default type is ''.
 * Typical use in the head section is: <xar:place-javascript position="head"/>
 *
 * @author Jason Judge
 * @param $args array containing the form field definition or the type, position, ...
 * @returns string
 * @return empty string
 */ 
function base_javascriptapi_handleeventjavascript($args)
{
    extract($args);

    // The whole lot can be passed in as an array.
    if (isset($definition) && is_array($definition)) {
        extract($definition);
    }

    // Position and type are mandatory.
    // 'position' is the name or ID of the tag ('body', 'mytag', etc.).
    // 'type' is the type of event ('onload', 'onmouseup', etc.)
    if (empty($position)) {
        $position = '';
    } else {
        $position = addslashes($position);
    }
    if (empty($type)) {
        $type = '';
    } else {
        $type = addslashes($type);
    }

    // Concatenate the JavaScript trigger code fragments.
    // Only pick up the event type JavaScript.

    return "
        echo htmlspecialchars(xarMod::apiFunc('base', 'javascript', 'geteventjs', array('position'=>'$position', 'type'=>'$type')));
    ";
}

?>