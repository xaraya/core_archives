<?php
/**
 * Exception Handling System
 *
 * @package exceptions
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

sys::import('xaraya.exceptions.exception');

/**
 * Class for exception type SystemMessage
 *
 * @package exceptions
 */
class SystemMessage extends xarException
{
    var $link;

    function SystemMessage($msg = '', $link = NULL)
    {
        parent::xarException();
        $this->msg = $msg;
        $this->link = $link;
    }

    function load($id)
    {
        if (array_key_exists($id, $this->defaults)) parent::load($id);
        else {
            $this->title = $id;
            $this->short = "No further information available";
        }
    }

    function toHTML()
    {
        $str = "<pre>\n" . htmlspecialchars($this->msg) . "\n</pre><br/>";
        if ($this->link) {
            $str .= '<a href="'.$this->link[1].'">'.$this->link[0].'</a><br/>';
        }
        return $str;
    }

}

?>