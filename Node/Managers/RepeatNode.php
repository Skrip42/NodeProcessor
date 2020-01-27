<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Repeat node
 *
 *         ┌───────────────┐
 *         │               ├─ out
 *    in  ─┤   RepeatNode  │
 *         │               ├─ complete
 *         └───────────────┘
 *
 *    emit out every input signal time
 *    emit complete when end
 */
class RepeatNode extends NodeAbstract
{
    private $count;

    public function __construct($value)
    {
        $this->count = $value;
        return parent::__construct();
    }

    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ],
        ],
        'output' => [
            'out' => [
            ],
            'complete' => [
                'data' => true
            ]
        ]
    ];

    public function eval()
    {
        for ($i = 0; $i < $this->count; $i++) {
            $this->emit('out', $this->input['in']['data']);
        }
        $this->emit('complete');
    }
}
