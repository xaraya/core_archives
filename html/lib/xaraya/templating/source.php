<?php
/**
 * Representing blocklayout source templates in Xaraya
 *
 * @package xaraya
 * @copyright see the html/credits.html file in this release
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

sys::import('blocklayout.template.source');

/**
 * Class to model the source template
 *
 * @package blocklayout
 * @todo    decorate this with a Stream object so we can compile anything that is a stream.
**/
class XarayaSourceTemplate extends SourceTemplate
{
    /**
     * compile a source template into templatecode
     *
     * @return string the compiled template code.
    **/
    public function &compile() 
    {
        assert('isset($this->fileName); /* No source to compile from */');
        sys::import('xaraya.templating.compiler');
        $compiler = XarayaCompiler::instance();
        $templateCode = $compiler->compileFile($this->fileName);

        $out = '';
        if(xarTpl_outputPHPCommentBlockInTemplates()) {
            // FIXME: this is weird stuff:
            // theme is irrelevant, date is seen in the filesystem, sourcefile in CACHEKEYS, why? it complicates the system a lot.
            $commentBlock = "<?php\n/*"
                          . "\n * Source:     " . $this->fileName         // redundant
                          . "\n * Theme:      " . xarTplGetThemeName()  // confusing (can be any theme now, its the theme during compilation, which is also shown on the above line)
                          . "\n * Compiled: ~ " . date('Y-m-d H:i:s T') // redundant
                          . "\n */\n?>\n";
            $out .= $commentBlock;
        }
        // Replace useless php context switches.
        // This sometimes seems to improve rendering end speed, dunno, bytecacher dependent?
        // Typical improvement i bench is around 4-5%
        $templateCode = preg_replace(array('/\?>[\s\n]+<\?php/','/<\?php[\s\n]+\?>/','/\?>[\s]+<\?php/','/<\?php[\s]+\?>/'),
                                     array("\n","\n",' ',' '),$templateCode);
        
        $out .= $templateCode;
        return $out;
    }
}
?>