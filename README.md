# NodeProcessor
## Description
## Installing
## Documentation

- [Node process](https://github.com/Skrip42/NodeProcessor#node-process)
  - [Base usage](https://github.com/Skrip42/NodeProcessor#base-usage)
    - [Create process](#create-process)
    - [Running process and debug](#running-process-and-debug)
    - [Serialize/deserialize process](https://github.com/Skrip42/NodeProcessor#serializedeserialize-process)
  - [Process builder](#process-builder)
  - [Request/Response](#requestresponse)
<!--- [Nodes](#nodes)-->
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
The module provides a number of base nodes:
- [ComonNodes](https://github.com/Skrip42/NodeProcessor#comonnodes)
- [Logical nodes](https://github.com/Skrip42/NodeProcessor#logical-nodes)
- [Arithmetical nodes](https://github.com/Skrip42/NodeProcessor#arithmetical-nodes)
- [String nodes](https://github.com/Skrip42/NodeProcessor#string-nodes)
- [Other nodes](https://github.com/Skrip42/NodeProcessor#other-nodes)
### ComonNodes
#### Manager nodes
##### BeginNode
###### Description:
Start node, executed when the process starts
###### Parametrs:
- mixed value = true
###### Grapical:
     ┌─────────────┐
     │             ├─ out1
     │             │
     │             │
     │             ├─ out2
     │  BeginNode  │
     │             │
     │             ├─ ...
     │             │
     │             │
     │             ├─ out$n
     └─────────────┘
###### Inputs:
none
###### Outputs:
- one or more outputs by pattern out{number}
###### Requests:
none
###### Logic:
emit $value from all outputs

##### EndNode
###### Description:
End node, collect result of process
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
    in1  ─┤               │
          │               │
    in2  ─┤               │
          │    EndNode    │
    ...  ─┤               │
          │               │
    in$n ─┤               │
          └───────────────┘
###### Inputs:
- some count of any value with any names
###### Outputs:
none
###### Requests:
none
###### Logic:
Collect all input value and map it to process result array.
Input can haw any names, this names map to result array key.

##### SplitterNode
###### Description:
Forked process trates
###### Parametrs:
none
###### Grapical:
         ┌───────────────┐
         │               ├─ out1
         │               │
         │               ├─ out2
    in  ─┤  SplitterNode │
         │               ├─ ...
         │               │
         │               ├─ out$n
         └───────────────┘
###### Inputs:
- in - required any type
###### Outputs:
- eny count of value with name like pattern: out{number}
###### Requests:
none
###### Logic:
emit input value to all outputs
out1 = out2 = ... = out$n = in

##### SingleNode
###### Description:
singlify input signals
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
    in    ─┤               │
           │   SingleNode  ├─ out
    reset ─┤               │
           └───────────────┘
###### Inputs:
- in  - required any type
- reset  - any value to reset input lock
###### Outputs:
- out - equal first of in
###### Requests:
none
###### Logic:
emit output signal only for first time input signal

##### WaitingNode
###### Description:
Waiting emit signal for continue
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
    in   ─┤               │
          │  WaitingNode  ├─ out
    emit ─┤               │
          └───────────────┘
###### Inputs:
- in  - required any type
- reset  - required bool 
###### Outputs:
- out - equal in
###### Requests:
none
###### Logic:
emit outputs only if 'emit' signal = true

##### TriggerNode
###### Description:
emit additional signals when input signal received
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
          │               ├─ before
          │               │
    in   ─┤  TriggerNode  ├─ out
          │               │
          │               ├─ after
          └───────────────┘
###### Inputs:
- in  - required any type
###### Outputs:
- before - true
- out - equal in
- after - true
###### Requests:
none
###### Logic:
emit 'before'=true output when input received, before output emitted
emit 'out' = 'in'
emit 'after'=true output when input received, after output emitted

##### IterateNode
###### Description:
iterate array for each 'emit' signal received
###### Parametrs:
none
###### Grapical:
            ┌───────────────┐
    array  ─┤               ├─ out
            │               │
    emit   ─┤  IterateNode  ├─ complete
            │               │
    reset  ─┤               ├─ count
            └───────────────┘
###### Inputs:
- array - required array
- emit - required any type
- reset - reset control
###### Outputs:
- out - any type value (emit for each elements of 'array')
- complete - true (emit when array is end)
- count - count of 'array' elements (emit when 'array' signal received)
###### Requests:
none
###### Logic:
emit 'count' = count(array) signal when 'array' signal received
emit 'out' = array[$n] for each 'emit' signal received
emit 'complete' = true when 'array' is end

##### PauseNode
###### Description:
Stop current thread until a response is received 
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
          │               │
     in  ─┤   PauseNode   ├─ out
          │               │
          └───────────────┘
###### Inputs:
- in - required any type
###### Outputs:
- out - any type value equal 'in'
###### Requests:
- 'pause': waiting eny response
###### Logic:
Stop current thread until a response is received then forward input to output

##### RepeatNode
###### Description:
Repeat 'in' sume time
###### Parametrs:
$value - count of repeat time
###### Grapical:
          ┌───────────────┐
          │               ├─ out
     in  ─┤   RepeatNode  │
          │               ├─ complete
          └───────────────┘
###### Inputs:
- in - required any type
###### Outputs:
- out - any type value equal 'in'
- complete - true (emit when repeat end)
###### Requests:
none
###### Logic:
Forward input to output and repeat it '$value' time.
Emit complete = true when repeat time is end

##### SubprocessNode
###### Description:
Run subprocess (another instance of Process) inside node
###### Parametrs:
$subprocessScheme like [process builder](https://github.com/Skrip42/NodeProcessor#process-builder)
###### Grapical:
           ┌─────────────────────┐
     in1  ─┤                     ├─ out1
           │                     │
     in2  ─┤                     ├─ out2
           │    SubProcessNode   │
     ...  ─┤                     ├─ ...
           │                     │
     in$n ─┤                     ├─ ount$n
           └─────────────────────┘
###### Inputs:
Depending on the scheme
Map inputs to scheme parameters
###### Outputs:
Depending on the scheme
Map subprocess output to node output
###### Requests:
Forward request from subprocess
Forward response to subprocess
###### Logic:
Run subprocess (instance of Process) inside SubProcessNode,
Map inputs to subprocess scheme parameters and map output of subprocess to current output
Forward subprocess request and response
Emit all output when subprocess status is Process::STATUS_COMPLETE

##### MultiSubProcessNode
###### Description:
Run sume fork of subprocess (another instance of Process) inside node
###### Parametrs:
$subprocessScheme like [process builder](https://github.com/Skrip42/NodeProcessor#process-builder)
###### Grapical:
           ┌─────────────────────┐
     in1  ─┤                     ├─ out1
           │                     │
     in2  ─┤                     ├─ out2
           │ MultiSubProcessNode │
     ...  ─┤                     ├─ ...
           │                     │
     in$n ─┤                     ├─ ount$n
           └─────────────────────┘
###### Inputs:
Depending on the scheme
Map simple inputs to scheme parameters
Map each element of array inputs to appropriate number of subprocess
###### Outputs:
Depending on the scheme
Map collected subprocesses outputs (as array)
###### Requests:
Forward request from subprocesses
Forward response to subprocesses
###### Logic:
Run subprocess instance for each array element of greatest input value
Collect all subprocesses outputs as appropriate arrays and map it to output
forward all subprocesses request to node request
forward all node response to subprocesses response
emit all output when all subprocesses status is Process::STATUS_COMPLETE

#### Logical nodes
##### AndNode
###### Description:
Logical AND node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               ├─ out
     in2  ─┤               │
           │    AndNode    │
     ...  ─┤               │
           │               ├─ iout
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required boolean
###### Outputs:
- out - logical AND of all inputs
- iout - inverted out
###### Requests:
none
###### Logic:
'out' = 'in1' && 'in2' && ... && 'in$n'
'iout' = !'out'

##### NotNode
###### Description:
Logical NOT node
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
          │               │
     in  ─┤   CountNode   ├─ out
          │               │
          └───────────────┘
###### Inputs:
- in - required boolean
###### Outputs:
- out - logical NOT of input
###### Requests:
none
###### Logic:
'out' = !'in'

##### OrNode
###### Description:
Logical OR node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               ├─ out
     in2  ─┤               │
           │     OrNode    │
     ...  ─┤               │
           │               ├─ iout
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required boolean
###### Outputs:
- out - logical AND of all inputs
- iout - inverted out
###### Requests:
none
###### Logic:
'out' = 'in1' || 'in2' || ... || 'in$n'

##### XorNode
###### Description:
Logical XOR node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               ├─ out
     in2  ─┤               │
           │    XorNode    │
     ...  ─┤               │
           │               ├─ iout
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required boolean
###### Outputs:
- out - logical XOR of all inputs
- iout - inverted out
###### Requests:
none
###### Logic:
out = (in1 || in2 || .. || in$n) && !(in1 && in2) && !(in2 && in3) && !(in1 && in3) .....
only one value must be true
iout = !out

##### CompareNode
###### Description:
Logical XOR node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
           │               ├─ more
     in1  ─┤               │
           │               │
           │  CompareNode  ├─ less
           │               │
     in2  ─┤               │
           │               ├─ equal
           └───────────────┘
###### Inputs:
- in1 - required some compared value
- in2 - required some compared value
###### Outputs:
- more - true when in1 > in2
- less - true when in1 < in2
- equal - true when in1 == in2
###### Requests:
none
###### Logic:
more  = in1 > in2
less  = in1 < in2
equal = in1 == in2

#### Arithmetical nodes
##### DivideNode
###### Description:
Ariphmetic devide node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               │
     in2  ─┤               │
           │   DivideNode  ├─ out
     ...  ─┤               │
           │               │
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required number
###### Outputs:
- out - divide in1 to all in$n inputs
###### Requests:
none
###### Logic:
out = in1 / in2 / .. / in$n

##### ModNode
###### Description:
Ariphmetic mod node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               │
     in2  ─┤               │
           │    ModNode    ├─ out
     ...  ─┤               │
           │               │
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required number
###### Outputs:
- out - mod of divide in1 to all in$n inputs
###### Requests:
none
###### Logic:
out = in1 % in2 % .. % in$n

##### MultiptyNode
###### Description:
Ariphmetic multiply node
###### Parametrs:
none
###### Grapical:
           ┌──────────────┐
     in1  ─┤              │
           │              │
     in2  ─┤              │
           │ MultiplyNode ├─ out
     ...  ─┤              │
           │              │
     in$n ─┤              │
           └──────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required number
###### Outputs:
- out - multiply all in$n inputs
###### Requests:
none
###### Logic:
out = in1 * in2 * .. * in$n

##### SubstractNode
###### Description:
Ariphmetic substract node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               │
     in2  ─┤               │
           │ SubstractNode ├─ out
     ...  ─┤               │
           │               │
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required number
###### Outputs:
- out - substract in1 and all in$n inputs
###### Requests:
none
###### Logic:
out = in1 - in2 - .. - in$n

##### SumNode
###### Description:
Ariphmetic sum node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               │
     in2  ─┤               │
           │    SumNode    ├─ out
     ...  ─┤               │
           │               │
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required number
###### Outputs:
- out - sum of all inputs
###### Requests:
none
###### Logic:
out = in1 + in2 + .. + in$n

#### String nodes
##### ConcatNode
###### Description:
String concatination node
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               │
     in2  ─┤               │
           │   ConcatNode  ├─ out
     ...  ─┤               │
           │               │
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required string
###### Outputs:
- out - concatination of all inputs 
###### Requests:
none
###### Logic:
'out' = in1 . in2 . ... . in$n

##### MatchNode
###### Description:
String match node
###### Parametrs:
$pattern - regexp string for matching
###### Grapical:
          ┌───────────────┐
          │               ├─ out
     in  ─┤   MatchNode   │
          │               ├─ iout
          └───────────────┘
###### Inputs:
- in - required string
###### Outputs:
- out - true if match success 
- iout - inverted out
###### Requests:
none
###### Logic:
Emit 'out' if 'in' match pattern
Emit 'iout' if 'in' don't match pattern

##### ReplaceNode
###### Description:
Replace string or string part
###### Parametrs:
$pattern - regexp string for matching
$replacement - string for replace
###### Grapical:
          ┌───────────────┐
          │               │
     in  ─┤  ReplaceNode  ├─ out
          │               │
          └───────────────┘
###### Inputs:
- in - required string
###### Outputs:
- out - result string
###### Requests:
none
###### Logic:
equal preg_replace($pattern, $replacement, 'in');

#### Array nodes
##### CollectNode
###### Description:
Collect input signals to array
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in   ─┤               │
           │  CollectNode  ├─ out
     emit ─┤               │
           └───────────────┘
###### Inputs:
- in - required mixed
- emit - required true
###### Outputs:
- out - array of input values 
###### Requests:
none
###### Logic:
Collect all 'in' signals to array
emit this array when 'emit' received

##### CombineNode
###### Description:
Collect all inputs to single array
###### Parametrs:
none
###### Grapical:
           ┌───────────────┐
     in1  ─┤               │
           │               │
     in2  ─┤               │
           │  CombineNode  ├─ out
     ...  ─┤               │
           │               │
     in$n ─┤               │
           └───────────────┘
###### Inputs:
- some inputs names like pattern 'in{$number}' - required mixed
###### Outputs:
- out - array of inputs values 
###### Requests:
none
###### Logic:
Combine all inputs to array like ['in1' => $valueOfIn1, 'in2' => $valueOfIn2 , .....]

##### CountNode
###### Description:
Collect input signals to array
###### Parametrs:
none
###### Grapical:
            ┌───────────────┐
            │               │
     array ─┤   CountNode   ├─ out
            │               │
            └───────────────┘
###### Inputs:
- array - required array
###### Outputs:
- out - count of array values 
###### Requests:
none
###### Logic:
emit count of 'array' elements

##### EachNode
###### Description:
Emit output for each element of input array
###### Parametrs:
none
###### Grapical:
            ┌──────────────┐
            │              ├─ out
            │              │
     array ─┤   EachNode   ├─ key
            │              │
            │              ├─ complete
            └──────────────┘
###### Inputs:
- array - required array
###### Outputs:
- out - single value of 'array'
- key - single key of 'arra'
- complete - true when array end
###### Requests:
none
###### Logic:
Emit 'out' and 'key' for each 'array' elements
Emit 'complete' when array is end

##### ExtractNode
###### Description:
Exctract single value from array by key
###### Parametrs:
$key - key
###### Grapical:
            ┌───────────────┐
            │               │
     array ─┤  ExtractNode  ├─ out
            │               │
            └───────────────┘
###### Inputs:
- array - required array
###### Outputs:
- out - emit single value of array
###### Requests:
none
###### Logic:
Emit array[$key]

#### Other nodes
##### CounterNode
###### Description:
Calculate count of input signals
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
          │               │
     in  ─┤  CounterNode  ├─ out
          │               │
          └───────────────┘
###### Inputs:
- in - required mixed
###### Outputs:
- out - emit count of input signals
###### Requests:
none
###### Logic:
calculate input signal count
emit 'out' everytime when 'in' received

##### DumpNode
###### Description:
Dump input values
###### Parametrs:
none
###### Grapical:
          ┌───────────────┐
          │               │
     in  ─┤    DumpNode   ├─ out
          │               │
          └───────────────┘
###### Inputs:
- in - required mixed
###### Outputs:
- out - equal 'in'
###### Requests:
none
###### Logic:
forward input to output
call dump('in') if dump function available
call var_export if running in cli mode
call var_dump if running in fpm mode

##### RandNode
###### Description:
Random value emiter
###### Parametrs:
$min - min value of random
$max - max value of random
###### Grapical:
           ┌───────────────┐
           │               │
     emit ─┤    RandNode   ├─ out
           │               │
           └───────────────┘
###### Inputs:
- emit - required mixed
###### Outputs:
- out - random number value 
###### Requests:
none
###### Logic:
when 'emit' received emit random($min=0, $max=1) value to 'out'

##### RangeNode
###### Description:
Range generator
###### Parametrs:
$start - start valut of range
$end - end value of range
$step - step of range
###### Grapical:
           ┌───────────────┐
           │               │
     emit ─┤    RageNode   ├─ out
           │               │
           └───────────────┘
###### Inputs:
- emit - required mixed
###### Outputs:
- out - array 
###### Requests:
none
###### Logic:
equal range($start, $end, $step=1)
emit array when 'emit' received

##### ValueNode
###### Description:
value emiter
###### Parametrs:
$value - emitted value
###### Grapical:
           ┌───────────────┐
           │               │
     emit ─┤   ValueNode   ├─ out
           │               │
           └───────────────┘
###### Inputs:
- emit - required mixed
###### Outputs:
- out - value 
###### Requests:
none
###### Logic:
emit value when 'out' received

### Creating user node
#### Input/output defenition
#### Read input/output, emit signals
#### Request/response processing


