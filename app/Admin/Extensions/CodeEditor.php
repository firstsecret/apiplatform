<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/23
 * Time: 16:11
 */

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class CodeEditor extends Field
{
    protected $view = 'admin.widgets.code-editor';

    protected static $css = [
        '/packages/codemirror/lib/codemirror.css',
    ];

    protected static $js = [
        '/packages/codemirror/src/codemirror.js',
        '/packages/codemirror/addon/edit/matchbrackets.js',
        '/packages/codemirror/mode/htmlmixed/htmlmixed.js',
        '/packages/codemirror/mode/xml/xml.js',
        '/packages/codemirror/mode/javascript/javascript.js',
        '/packages/codemirror/mode/css/css.js',
        '/packages/codemirror/mode/clike/clike.js',
//        '/packages/codemirror/mode/php/php.js',
        '/packages/codemirror/mode/nginx/nginx.js',
    ];

    public function render()
    {
        $this->script = <<<EOT

CodeMirror.fromTextArea(document.getElementById("{$this->id}"), {
    lineNumbers: true,
    mode: "nginx",
    extraKeys: {
        "Tab": function(cm){
            cm.replaceSelection("    " , "end");
        }
     }
});

EOT;
        return parent::render();

    }
}