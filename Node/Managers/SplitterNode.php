<?php
namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Fork node
 *
 *         ┌───────────────┐
 *         │               ├─ out1
 *         │               │
 *         │               ├─ out2
 *    in  ─┤  SplitterNode │
 *         │               ├─ ...
 *         │               │
 *         │               ├─ out$n
 *         └───────────────┘
 *  out1 = out2 = ... = out$n = in
 */
class SplitterNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ]
        ],
        'user_output' => [
            'pattern' => '~out\d+~'
        ]
    ];

    public function eval()
    {
        foreach ($this->output as $out => $data) {
            $this->emit($out, $this->input['in']['data']);
        }
    }
}
