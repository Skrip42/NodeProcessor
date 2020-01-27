<?php

namespace Skrip42\NodeProcessor\Node\Logical;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Logical ot node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               ├─ out
 *    in2  ─┤               │
 *          │     OrNode    │
 *    ...  ─┤               │
 *          │               ├─ iout
 *    in$n ─┤               │
 *          └───────────────┘
 *    out = in1 || in2 || .. || in$n
 *    iout = !out
 */
class OrNode extends NodeAbstract
{
    protected static $scheme = [
        'user_input' => [
            'pattern' => '~in\d+~'
        ],
        'output' => [
            'out' => [
                'data' => true
            ],
            'iout' => [
                'data' => true
            ]
        ]
    ];

    public function eval()
    {
        $allDeclare = true;
        foreach ($this->input as $in => $data) {
            if (!isset($data['data'])) {
                $allDeclare = false;
                continue;
            }
            if ($data['data']) {
                $this->emit('out');
                return;
            }
        }
        $this->emit('iout');
    }
}
