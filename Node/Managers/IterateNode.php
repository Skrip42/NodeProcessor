<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * Iterator node
 *
 *            ┌───────────────┐
 *    array  ─┤               ├─ out
 *            │               │
 *    emit   ─┤  IterateNode  ├─ complete
 *            │               │
 *    reset  ─┤               ├─ count
 *            └───────────────┘
 *
 *    iterate array for all emit signale.
 *    reset signal to reset array iterator.
 *    emit out for each array iteration
 *    emit complete when array is end
 *    emit count when array signal
 */
class IterateNode extends NodeAbstract
{
    protected static $scheme = [
        'input' => [
            'array' => [
                'required' => true,
            ],
            'emit' => [
                'required' => true,
            ],
            'reset' => []
        ],
        'output' => [
            'out' => [
            ],
            'complete' => [
                'data' => true
            ],
            'count' => [
            ]
        ]
    ];

    private $iterator = 0;

    private $complete = false;

    public function set(string $in, $data)
    {
        if ($in == 'reset') {
            $this->complete = false;
            $this->iterator = 0;
            $this->incSetCounter($in);
            return;
        }
        if ($in == 'array') {
            if (!is_array($data)) {
                throw new NodeException('Propery must be array');
            }
            $data = array_values($data);
            $this->output['count']['data'] = count($data);
            $this->input['array']['data'] = $data;
            $this->incSetCounter($in);
            $this->emit('count');
            return;
        }
        parent::set($in, $data);
    }

    public function eval()
    {
        if ($this->complete) {
            return;
        }
        if ($this->iterator < $this->output['count']['data']) {
            $this->iterator++;
            $this->emit('out', $this->input['array']['data'][$this->iterator - 1]);
        } else {
            $this->complete = true;
            $this->emit('complete');
        }
    }
}
