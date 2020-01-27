<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Repeat node
 *
 *           ┌───────────────┐
 *    in    ─┤               │
 *           │   SingleNode  ├─ out
 *    reset ─┤               │
 *           └───────────────┘
 *
 *    out = in
 *    emit out only first time when in signal
 *    reset singal drom emit lock
 */
class SingleNode extends NodeAbstract
{
    private $emitFlag = false;

    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ],
            'reset' => [
            ]
        ],
        'output' => [
            'out' => [
            ],
        ]
    ];

    public function set(string $in, $data)
    {
        if ($in == 'reset') {
            $this->emitFlag = false;
            $this->incSetCounter($in);
            return;
        }
        parent::set($in, $data);
    }

    public function eval()
    {
        if (!$this->emitFlag) {
            $this->emitFlag = true;
            $this->emit('output', $this->input['in']['data']);
        }
    }
}
