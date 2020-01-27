# NodeProcessor
## Description
## Installing
## Documentation

- [Node process](https://github.com/Skrip42/NodeProcessor#nodeprocess)
- [Base usage](https://github.com/Skrip42/NodeProcessor#base-usage)
- [Create process](#create-process)
- [Running process and debug](#running-process-and-debug)
- [Serialize/deserialize process](https://github.com/Skrip42/NodeProcessor#serializedeserialize-process)
- [Process builder](#process-builder)
- [Request/Response](#requestresponse)
- [Nodes](#nodes)
- [ComonNodes](https://github.com/Skrip42/NodeProcessor#comonnodes)
- [Manager nodes](https://github.com/Skrip42/NodeProcessor#manager-nodes)
- [BeginNode](https://github.com/Skrip42/NodeProcessor#beginnode)
- [EndNode](https://github.com/Skrip42/NodeProcessor#endnode)
- [SplitterNode](https://github.com/Skrip42/NodeProcessor#splitterNode)
- [SingleNode](https://github.com/Skrip42/NodeProcessor#singleNode)
- [WaitingNode](https://github.com/Skrip42/NodeProcessor#waitingNode)
- [TriggerNode](https://github.com/Skrip42/NodeProcessor#triggerNode)
- [IterateNode](https://github.com/Skrip42/NodeProcessor#iterateNode)
- [PauseNode](https://github.com/Skrip42/NodeProcessor#pausenode)
- [RepeatNode](https://github.com/Skrip42/NodeProcessor#repeatnode)
- [SubprocessNode](https://github.com/Skrip42/NodeProcessor#subprocessnode)
- [MultiSubProcessNode](https://github.com/Skrip42/NodeProcessor#multisubprocessnode)
- [Logical nodes](https://github.com/Skrip42/NodeProcessor#logical-nodes)
- [AndNode](https://github.com/Skrip42/NodeProcessor#andnode)
- [NotNode](https://github.com/Skrip42/NodeProcessor#notnode)
- [OrNode](https://github.com/Skrip42/NodeProcessor#ornode)
- [XorNode](https://github.com/Skrip42/NodeProcessor#xornode)
- [CompareNode](https://github.com/Skrip42/NodeProcessor#comparenode)
- [Arithmetical nodes](https://github.com/Skrip42/NodeProcessor#arithmetical-nodes)
- [DivideNode](https://github.com/Skrip42/NodeProcessor#dividenode)
- [ModNode](https://github.com/Skrip42/NodeProcessor#modnode)
- [MultiptyNode](https://github.com/Skrip42/NodeProcessor#multiptynode)
- [SubstractNode](https://github.com/Skrip42/NodeProcessor#substractnode)
- [SumNode](https://github.com/Skrip42/NodeProcessor#sumnode)
- [String nodes](https://github.com/Skrip42/NodeProcessor#string-nodes)
- [ConcatNode](https://github.com/Skrip42/NodeProcessor#concatnode)
- [MatchNode](https://github.com/Skrip42/NodeProcessor#matchnode)
- [ReplaceNode](https://github.com/Skrip42/NodeProcessor#replacenode)
- [Other nodes](https://github.com/Skrip42/NodeProcessor#other-nodes)
- [CounterNode](https://github.com/Skrip42/NodeProcessor#counternode)
- [DumpNode](https://github.com/Skrip42/NodeProcessor#dumpNode)
- [RandNode](https://github.com/Skrip42/NodeProcessor#randNode)
- [RangeNode](https://github.com/Skrip42/NodeProcessor#rangeNode)
- [ValueNode](https://github.com/Skrip42/NodeProcessor#valueNode)
- [Creating user node](https://github.com/Skrip42/NodeProcessor#creating-user-node)
- [Input/output defenition](https://github.com/Skrip42/NodeProcessor#inputoutput-defenition)
- [Read input/output, emit signals](https://github.com/Skrip42/NodeProcessor#read-inputoutput-emit-signals)
- [Request/response processing](https://github.com/Skrip42/NodeProcessor#requestresponse-processing)

### Node Process
Node Process is a small procedural like program executable on top of php.
The process consists of nodes and the connections between them, as well as the state of these nodes
An important consequence of this: At any time, the process can be stopped, saved with the current state and continue to execute from the same point.
Processes can be dynamically created using PCP, serialize, deserialize.
#### Base usage
Add 
```php
use Skrip42\NodeProcessor\Process;
```
on top of your file.
You can also add all the nodes that you intend to use:
```php
use Skrip42\NodeProcessor\Node\Other\ValueNode;
use Skrip42\NodeProcessor\Node\Logical\CompareNode;
```
##### Create process
This section is presented to understand the processes, the recommended way to create processes is [process builder](https://github.com/Skrip42/NodeProcessor#process-builder).
```php
$process = new Process; //create new process;

//create all necessary nodes:
// as syntax: process->createNode($nodeClassName, ...$values) : NodeAbstract
$valNode1 = $process->createNode(ValueNode::class, 1); //you can pass start parameters to the node if required 
$valNode2 = $process->createNode(ValueNode::class, 5);
$compareNode = $process->createNode(ValueNode::class);
//You do not need to create a start and end node, they are already included in the process

//link nodes:
// as syntax: process->linkNode($outputNodeName, $outputNode, $inputNodeName, $inputNode)
$process->linkNode('out1', $process->getBeginNode(), 'emit', $valNode1); // you can get begin and end node
                                                                        // from getBeginNode and getEndNode methods
$process->linkNode('out2', $process->getBeginNode(), 'emit', $valNode2);
$process->linkNode('out', $valNode1, 'in1', $compareNode);
$process->linkNode('out', $valNode2, 'in2', $compareNode);
$process->linkNode('more', $compareNode, 'more', $process->getEndNode()); // end node has dynamically input name
$process->linkNode('less', $compareNode, 'less', $process->getEndNode());
//You can always leave output nodes empty; can input be left blank determined by node policy
```
The resulting process can be represented graphically:

                            ┌───────────────┐         
                      ┌─emit┤ ValueNode = 1 ├out─┐    ┌─────────────┐            ┌─────────┐
    ┌───────────┐     │     └───────────────┘    └─in1┤             ├more────more┤         │
    │           ├out1─┘                               │             │            │ EndNode │
    │ BeginNode │                                     │ CompareNode ├less────less┤         │
    │           ├out2─┐                               │             │            └─────────┘
    └───────────┘     │     ┌───────────────┐    ┌─in2┤             ├equal─X
                      └─emit┤ ValueNode = 5 ├out─┘    └─────────────┘
                            └───────────────┘         

Most nodes have one or more inputs and outputs. The names of the inputs and outputs of standard bonds can be found in the [documentation](https://github.com/Skrip42/NodeProcessor#comonnodes)

##### Running process and debug
For start process:
```php
$result = $process->run(); // running process
$state = $process->getState(); // you also can get current process state for debug you script
```
You will get array of the form:
```php
[
    'status' => currentProcessStatus,
    'output' => [ .. array of end node inputs .. ]
    'requests' =>  [ .. array of requests .. ]
]
```
status is number indicating the current state of the process
- Process::STATUS_REDY - redy to run
- Process::STATUS_RUNN - running
- Process::STATUS_ERROR - complete with error
- Process::STATUS_WAIT_RESPONSE - wait response (see [Request/Response](#requestresponse))
- Process::STATUS_COMPLETE - complete

output is array of EndNode inputs. Input name map to array key.

requests is array of request (see [Request/Response](#requestresponse))

$process->getState() - return current state of process, all process nodes, all nodes inputs and outputs
After the node identifier and the names of its inputs and outputs, you can see the number of starts of the nodes, how many times each input received a signal and how many times each output emitted a signal

##### Serialize/deserialize process
You can serialize and deserialize process as default php tools:
```php
$serialized = serialize($process);
$process = unserialize($serialized);
```
**You should not include other objects in the nodes or pass from as parameters if you do not want to serialize them!**

#### Process builder
A more convenient way to create a process is through the process builder.
The previous process could be created like this:
```php
$process = Process::build(
    [
        'node' => [
            'vn1' => [ValueNode::class, 1],
            'vn2' => [ValueNode::class, 5],
            'cn' => [CompareNode::class],
        ],
        'link' => [
            ['out1', 'begin', 'emit', 'vn1'], //begin is the predefined start node name
            ['out2', 'begin', 'emit', 'vn2'],
            ['out', 'vn1', 'in1', 'cn'],
            ['out', 'vn2', 'in2', 'cn'],
            ['less', 'cn', 'less', 'end'], //end is the predefined end node name
            ['more', 'vn2', 'more', 'end'], 
        ]
    ]
);
```
using the builder you can also parameterize your process:
```php
$process = Process::build(
    [
        'node' => [
            'vn1' => [ValueNode::class, '{$val1}'], // template syntax: {$valueName}
            'vn2' => [ValueNode::class, '{$val2}'],
            'cn' => [CompareNode::class],
        ],
        'link' => [
            ['out1', 'begin', 'emit', 'vn1'], //begin is the predefined start node name
            ['out2', 'begin', 'emit', 'vn2'],
            ['out', 'vn1', 'in1', 'cn'],
            ['out', 'vn2', 'in2', 'cn'],
            ['less', 'cn', 'less', 'end'], //end is the predefined end node name
            ['more', 'vn2', 'more', 'end'], 
        ]
    ],
    [ //value list
        'val1' => 1,
        'val2' => 5
    ]
);
```

#### Request/Response
To communicate external code in the processes provided request/response system
Some node can send request to process.  Then the process will return with the status “waiting for a response” and as a result there will be a list of requests of the form:
```php
[
    ['uniqIdOfRequest' => 'request data'],
    .......
]
```
You can send an answer like:
```php
$process->setResponse('uniqIdOfRequest', 'response data');
```
Then the process will continue to execute from the node that sent the request (from node "setResponse" method)

### Nodes
### ComonNodes
#### Manager nodes
##### BeginNode
##### EndNode
##### SplitterNode
##### SingleNode
##### WaitingNode
##### TriggerNode
##### IterateNode
##### PauseNode
##### RepeatNode
##### SubprocessNode
##### MultiSubProcessNode
#### Logical nodes
##### AndNode
##### NotNode
##### OrNode
##### XorNode
##### CompareNode
#### Arithmetical nodes
##### DivideNode
##### ModNode
##### MultiptyNode
##### SubstractNode
##### SumNode
#### String nodes
##### ConcatNode
##### MatchNode
##### ReplaceNode
#### Other nodes
##### CounterNode
##### DumpNode
##### RandNode
##### RangeNode
##### ValueNode
### Creating user node
#### Input/output defenition
#### Read input/output, emit signals
#### Request/response processing

