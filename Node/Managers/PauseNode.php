<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Pause node
 *
 *         ┌───────────────┐
 *         │               │
 *    in  ─┤   PauseNode   ├─ out
 *         │               │
 *         └───────────────┘
 *    stop process and send request 'pause' before output
 *    out = in
 */
class PauseNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'in' => [
                'required' => true
            ]
        ],
        'output' => [
            'out' => [
            ]
        ]
    ];

    public function eval()
    {
        $this->sendRequest('pause');
    }

    public function setResponse($id, $data)
    {
        $this->emit('out', $this->input['in']['data']);
    }
}
