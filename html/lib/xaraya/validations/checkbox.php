<?php
/**
 * Short description of purpose of file
 *
 * @package validation
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
*/

/**
 * Checkbox Validation Class
 *
 * @throws VariableValidationException
 */
sys::import('xaraya.validations');
class CheckBoxValidation extends ValueValidations
{
    function validate(&$subject, Array $parameters)
    {
        if (empty($subject) || is_null($subject)) {
            $subject = 0;
        } elseif (is_string($subject)) {
            $subject = 1;
        } else {
            $msg = 'Not a checkbox value';
            throw new VariableValidationException(null,$msg);
        }
        return true;
    }
}
?>