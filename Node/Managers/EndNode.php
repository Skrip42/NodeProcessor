<?php

namespace Skrip42\NodeProcessor\Node\Managers;

use Skrip42\NodeProcessor\Node\NodeAbstract;
use Skrip42\NodeProcessor\Exception\NodeException;

/**
 * End node
 *
 *          ┌───────────────┐
 *    in1  ─┤               │
 *          │               │
 *    in2  ─┤               │
 *          │    EndNode    │
 *    ...  ─┤               │
 *          │               │
 *    in$n ─┤               │
 *          └───────────────┘
 *
 * End node collect all input value
 * and map it to proces result array.
 * Input names map to result array key
 */
class EndNode extends NodeAbstract
{
    protected static $scheme = [
        'user_input' => []
    ];

    public function eval()
    {
        return;
    }

    /**
     * Return array of all inputs
     * input names map to result array key
     *
     * @return array
     */
    public function getResult() : array
    {
        $result = [];
        foreach ($this->input as $key => $data) {
            $result[$key] = $data['data'];
        }
        return $result;
    }
}
