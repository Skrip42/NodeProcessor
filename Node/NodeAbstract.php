<?php

namespace Skrip42\NodeProcessor\Node;

use Skrip42\NodeProcessor\Exception\NodeException;
use Skrip42\NodeProcessor\Process;

/**
 * Base node class
 */
abstract class NodeAbstract
{
    /* node id */
    private $id;

    protected $runCounter = 0;

    protected $process;

    /**
     * apply node scheme
     */
    public function __construct()
    {
        $this->id = uniqid();
        if (!empty(static::$scheme['input'])) { //apply imput scheme
            foreach (static::$scheme['input'] as $input => $property) {
                $property = array_merge(
                    [
                        'data'     => null,
                        'setCount' => 0
                    ],
                    $property
                );
                $this->input[$input] = $property;
            }
        }
        if (!empty(static::$scheme['output'])) { //apply output scheme
            foreach (static::$scheme['output'] as $output => $property) {
                $property = array_merge(
                    [
                        'data'      => null,
                        'emitCount' => 0
                    ],
                    $property
                );
                $this->output[$output] = $property;
            }
        }
    }

    /**
     * Link node to parent process
     *
     * @param Process $process
     *
     * @return null
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
    }

    /**
     * Send request to client application
     *
     * @param mixed $data
     * @param mixed $id
     *
     * @return null
     */
    public function sendRequest($data, $id = null)
    {
        if (empty($id)) {
            $id = uniqid();
        }
        $this->process->addRequest($id, $this, $data);
    }
    
    /**
     * Sent response to node
     *
     * @param mixed $id
     * @param mixed $data
     *
     * @return null
     */
    public function setResponse($id, $data)
    {
        foreach ($data as $key => $value) { //default map response to input and reeval
            if (isset($this->input[$key])) {
                $this->input[$key]['data'] = $value;
                $this->incSetCounter($key);
            }
            $this->eval();
        }
    }

    /**
     * Return node id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    protected static $scheme = [
    ];

    protected $input = [
    ];

    protected $output = [
    ];

    /**
     * Return valid input from scheme
     *
     * @return array
     */
    public static function getValidInputName() : array
    {
        $inputNames = [];

        if (!empty(static::$scheme['input'])) {
            $inputNames = array_keys(static::$scheme['input']);
        }

        return $inputNames;
    }

    /**
     * Return valid output from scheme
     *
     * @return array
     */
    public static function getValidOutputName() : array
    {
        $outputNames = [];

        if (!empty(static::$scheme['output'])) {
            $outputNames = array_keys(static::$scheme['output']);
        }

        return $outputNames;
    }
    
    /**
     * Return node scheme
     *
     * @return array
     */
    public function getScheme() : array
    {
        return static::$scheme;
    }

    /**
     * Reset node state
     *
     * @return null
     */
    public function reset()
    {
        $this->runCounter = 0;
        foreach ($this->input as $input => &$property) {
            if (empty(static::$scheme['input'])
                || !isset(static::$scheme['input'][$input])
            ) {
                if (!isset(static::$scheme['user_input'])) {
                    throw new NodeException('unknow input name: ' . $input);
                }
                if (!isset(static::$scheme['user_input']['property'])
                    || !isset(static::$scheme['user_input']['property']['data'])
                ) {
                    $property['data'] = null;
                } else {
                    $property['data'] = static::$scheme['user_input']['property']['data'];
                }
            } else {
                if (!isset(static::$scheme['input'][$input]['data'])) {
                    $property['data'] = null;
                } else {
                    $property['data'] = static::$scheme['input'][$input]['data'];
                }
            }
            $property['setCount'] = 0;
        }
        foreach ($this->output as $output => &$property) {
            if (empty(static::$scheme['output'])
                || !isset(static::$scheme['output'][$output])
            ) {
                if (!isset(static::$scheme['user_output'])) {
                    throw new NodeException('unknow output name: ' . $output);
                }
                if (!isset(static::$scheme['user_output']['property'])
                    || !isset(static::$scheme['user_output']['property']['data'])
                ) {
                    $property['data'] = null;
                } else {
                    $property['data'] = static::$scheme['user_output']['property']['data'];
                }
            } else {
                if (!isset(static::$scheme['output'][$output]['data'])) {
                    $property['data'] = null;
                } else {
                    $property['data'] = static::$scheme['output'][$output]['data'];
                }
            }
        }
    }

    /**
     * Return coutn of node run
     *
     * @return int
     */
    public function getRunCounter() : int
    {
        return $this->runCounter;
    }
    
    /**
     * Return current node state
     *
     * @return array
     */
    public function getState() : array
    {
        $inputs = [];
        $outputs = [];
        foreach ($this->input as $key => $data) {
            if (!isset($data['setCount'])) {
                $data['setCount'] = 0;
            }
            $inputs[$key . '(' . $data['setCount'] . ')'] = $data['data'];
        }
        foreach ($this->output as $key => $data) {
            if (!isset($data['emitCount'])) {
                $data['emitCount'] = 0;
            }
            $outputs[$key . '(' . $data['emitCount'] . ')'] = $data['data'];
        }
        return [
            'inputs'  => $inputs,
            'outputs' => $outputs
        ];
    }

    /**
     * Bind input to $node
     *
     * @param string       $in
     * @param NodeAbstract $node
     *
     * @return null
     */
    public function bindInput(string $in, NodeAbstract $node)
    {
        if (!isset($this->input[$in])) {
            if (isset(static::$scheme['user_input'])) {
                $property = [
                    'data'     => null,
                    'setCount' => 0
                ];
                if (!empty(static::$scheme['user_input']['property'])) {
                    $property = array_merge($property, static::$scheme['user_input']['property']);
                }
                if (!empty(static::$scheme['user_input']['pattern'])
                    && !preg_match(static::$scheme['user_input']['pattern'], $in)
                ) {
                    throw new NodeException(
                        'input property must named as "' . static::$scheme['user_input']['pattern'] . '"'
                    );
                }
                $this->input[$in] = $property;
            } else {
                throw new NodeException(
                    'Invalid input property name in node ' . $this->getId() . '_' . get_class($this) . ' .'
                    . ' Valid input name: ' . implode(',', static::getValidInputName())
                );
            }
        }
        $this->input[$in]['node'] = $node;
    }

    /**
     * Bind output to $node
     *
     * @param string       $out
     * @param string       $in
     * @param NodeAbstract $node
     *
     * @return null
     */
    public function bindOutput(string $out, string $in, NodeAbstract $node)
    {
        if (!isset($this->output[$out])) {
            if (isset(static::$scheme['user_output'])) {
                $property = [
                    'data'      => null,
                    'emitCount' => 0
                ];
                if (!empty(static::$scheme['user_output']['property'])) {
                    $property = array_merge($property, static::$scheme['user_output']['property']);
                }
                if (!empty(static::$scheme['user_output']['pattern'])
                    && !preg_match(static::$scheme['user_output']['pattern'], $out)
                ) {
                    throw new NodeException(
                        'output property must named as "' . static::$scheme['user_output']['pattern'] . '"'
                    );
                }
                $this->output[$out] = $property;
            } else {
                throw new NodeException(
                    'Invalid output property name in node ' . $this->getId() . '_' .get_class($this) . ' .'
                    . ' Valid output name: ' . implode(',', static::getValidOutputName())
                );
            }
        }
        $this->output[$out]['node'] = $node;
        $this->output[$out]['nodeInput'] = $in;
    }

    /**
     * Increment input set counter
     *
     * @param string $in
     *
     * @return null
     */
    protected function incSetCounter(string $in)
    {
        if (!isset($this->input[$in]['setCount'])) {
            $this->input[$in]['setCount'] = 0;
        }
        $this->input[$in]['setCount']++;
    }

    /**
     * Set data to node input
     *
     * @param string $in
     * @param mixed  $data
     *
     * @return null
     */
    public function set(string $in, $data)
    {
        if (empty($this->input[$in])) {
            throw new NodeException(
                'Invalid input property name in node ' . $this->getId() . '_' . get_class($this) . ' .'
                . ' Valid input name: ' . implode(',', static::getValidInputName())
            );
        }
        $this->incSetCounter($in);
        $this->input[$in]['data'] = $data;
        if ($this->validInput()) {
            $this->runCounter++;
            $this->eval();
        }
    }

    /**
     * Increment output emit counter
     *
     * @param string $out
     *
     * @return null
     */
    protected function incEmitCounter(string $out)
    {
        if (empty($this->output[$out]['emitCount'])) {
            $this->output[$out]['emitCount'] = 0;
        }
        $this->output[$out]['emitCount']++;
    }

    /**
     * Emit data from target output
     *
     * @param string $out
     * @param mixed  $data
     *
     * @return null
     */
    public function emit(string $out, $data = null)
    {
        if (isset($data)) {
            $this->output[$out]['data'] = $data;
        }
        if (!isset($this->output[$out]['data'])) {
            throw new NodeException('cannot emit empty data');
        }
        $this->incEmitCounter($out);
        if (!empty($this->output[$out]['node'])) {
            $this->output[$out]['node']->set(
                $this->output[$out]['nodeInput'],
                $this->output[$out]['data']
            );
        }
    }

    /**
     * Validate requeired input
     *
     * @return bool
     */
    protected function validInput() : bool
    {
        foreach ($this->input as $input) {
            if (empty($input['required'])) {
                continue;
            }
            if (!isset($input['data'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Excecute node
     *
     * @return null
     */
    abstract public function eval();
}
