<?php

namespace Skrip42\NodeProcessor\Node\Other;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Dump node
 *
 *         ┌───────────────┐
 *         │               │
 *    in  ─┤    DumpNode   ├─ out
 *         │               │
 *         └───────────────┘
 *    out = in
 *    dump(in)   if dump function available
 *    var_export if cli mode
 *    var_dump   if fpm mode
 */
class DumpNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ]
        ],
        'output' => [
            'out' => []
        ]
    ];

    public function eval()
    {
        if (function_exists('dump')) {
            dump($this->input['in']['data']);
        } else if (php_sapi_name() == 'cli') {
            var_export($this->input['in']['data']);
        } else {
            var_dump($this->input['in']['data']);
        }
        if (isset($this->output['out']['node'])) {
            $this->emit('out', $this->input['in']['data']);
        }
    }
}
