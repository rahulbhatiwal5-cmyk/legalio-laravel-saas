@extends('admin_layout.master')
@section('content')


<style>
#diagramArea {
    position: relative;
    width: 100%;
    height: 3000px;
    background: #f9f9f9;
}


.node {
    width: 240px;
    min-height: 80px;
    background:rgb(225, 227, 227);
    padding: 12px;
    color: black;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 0 5px rgba(0,0,0,0.15);
}


.shape-diamond {
    transform: rotate(45deg);
    width: 160px;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.shape-diamond .inner {
    transform: rotate(-45deg);
}

.arrow-label {
    font-size: 12px;
    background: white;
    padding: 2px 4px;
    border-radius: 4px;
    box-shadow: 0 0 2px rgba(0,0,0,0.2);
    font-weight: 500;
}


</style>

<div class="nk-content">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Graphical Interface</h4>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row main_section mt-4">
            <div class="card-inner">
                <div id="diagramArea" class="drawflow" style="height: 1000vh; width: 1000vh;">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="feedback_div">
            <button class="btn btn-primary">Feedback</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/jsplumb@2.15.6/dist/js/jsplumb.min.js"></script>

<script>
    const questions = @json($document_questions);

    $(document).ready(function () {
        const container = document.getElementById('diagramArea');

        jsPlumb.ready(function () {
            jsPlumb.setContainer(container);

            const spacingX = 300; 
            const spacingY = 160;

            const positions = {};         
            const placed = new Set();     
            const branchOffset = {};     

            let col = 0;

            function placeNode(id, x = 0, y = 0) {
                const key = `Q-${id}`;
                if (placed.has(id)) return;

                // Avoid overlapping by incrementing Y only when needed
                if (!branchOffset[x]) branchOffset[x] = y;
                else if (branchOffset[x] < y) branchOffset[x] = y;
                else branchOffset[x] += spacingY;

                const finalY = branchOffset[x];

                positions[key] = { top: finalY, left: x * spacingX };
                placed.add(id);

                const node = questions.find(q => q.id === id);
                if (!node) return;

                if(node.question_data?.next_question_id){
                    placeNode(node.question_data.next_question_id, x, finalY + spacingY);
                }

                if (node.options?.length) {
                    node.options.forEach((opt, i) => {
                        if (opt.next_question_id) {
                            const childX = x + i + 1; 
                            placeNode(opt.next_question_id, childX, 0);
                        }
                    });
                }

                if (node.conditions?.length) {
                    node.conditions.forEach((cond, i) => {
                        if (cond.condition_type === 'go_to_step_condition' && node.question_data?.conditional_go_to_step) {
                            const childX = x + i + 1 + node.options?.length; 
                            placeNode(node.question_data.conditional_go_to_step, childX, 0);
                        }
                    });
                }
            }

            questions.forEach(q => {
                placeNode(q.id);
            });

            questions.forEach(q => {
                const div = document.createElement('div');
                const id = `Q-${q.id}`;
                div.id = id;
                div.classList.add('node', `shape-${q.shape}`);
                div.style.position = 'absolute';

                const pos = positions[id] || { top: 0, left: 0 };
                div.style.top = `${pos.top}px`;
                div.style.left = `${pos.left}px`;

                div.innerHTML = (q.shape === 'diamond')
                    ? `<div class="inner">${q.question_data?.question_label || ''}</div>`
                    : q.question_data?.question_label || '';

                container.appendChild(div);
                jsPlumb.draggable(div, { containment: 'parent' });
            });

            function connectNodes(fromId, toId, label = '', anchor = ["Bottom", "Top"]) {
                jsPlumb.connect({
                    source: `Q-${fromId}`,
                    target: `Q-${toId}`,
                    anchors: anchor,
                    overlays: [
                        ["Arrow", {
                            width: 14,
                            length: 18,
                            location: 1,
                            foldback: 0.7
                        }],
                        ["Label", {
                            label: label,
                            location: 0.5,
                            cssClass: "arrow-label"
                        }]
                    ],
                    connector: ["Flowchart", { stub: 30, gap: 5, cornerRadius: 5, alwaysRespectStubs: true }]
                });
            }


           
            questions.forEach(q => {
                if (q.question_data?.next_question_id) {
                    connectNodes(q.id, q.question_data.next_question_id);
                } else {
                    // Create a unique end node ID for this question
                    const endNodeId = `END-${q.id}`;

                    // Check if node already exists
                    if (!document.getElementById(endNodeId)) {
                        const endDiv = document.createElement('div');
                        endDiv.id = endNodeId;
                        endDiv.classList.add('node', 'end-node');
                        endDiv.style.position = 'absolute';
                        endDiv.innerHTML = 'Checkout'; 

                     
                        const sourcePos = positions[`Q-${q.id}`] || { top: 0, left: 0 };

                        // Offset placement to the right and a bit below
                        const endLeft = sourcePos.left + 200;
                        const endTop = sourcePos.top + 100;

                        endDiv.style.left = `${endLeft}px`;
                        endDiv.style.top = `${endTop}px`;

                        container.appendChild(endDiv);
                        jsPlumb.draggable(endDiv, { containment: 'parent' });
                    }

                    // Connect current node to the end node
                    jsPlumb.connect({
                        source: `Q-${q.id}`,
                        target: endNodeId,
                        anchors: ["Bottom", "Top"],
                        overlays: [
                            ["Arrow", {
                                width: 14,
                                length: 18,
                                location: 1,
                                foldback: 0.7
                            }]
                           
                        ],
                        connector: ["Flowchart", { stub: 30, gap: 5, cornerRadius: 5, alwaysRespectStubs: true }]
                    });
                }

                if (q.options?.length) {
                    q.options.forEach((opt, i) => {
                        const anchor = (i % 2 === 0) ? ["Right", "Left"] : ["Left", "Right"];

                        if (opt.next_question_id) {
                            connectNodes(q.id, opt.next_question_id, opt.option_label || "→", anchor);
                        } else {
                            const endNodeId = `END-${q.id}-${i}`;

                            if (!document.getElementById(endNodeId)) {
                                const endDiv = document.createElement('div');
                                endDiv.id = endNodeId;
                                endDiv.classList.add('node', 'end-node');
                                endDiv.style.position = 'absolute';
                                endDiv.innerHTML = 'Checkout'; 
                              
                                const sourcePos = positions[`Q-${q.id}`];
                                const endLeft = sourcePos.left + (i + 1) * 200;
                                const endTop = sourcePos.top + 100;

                                endDiv.style.left = `${endLeft}px`;
                                endDiv.style.top = `${endTop}px`;

                                container.appendChild(endDiv);
                                jsPlumb.draggable(endDiv, { containment: 'parent' });
                            }

                           
                            jsPlumb.connect({
                                source: `Q-${q.id}`,
                                target: endNodeId,
                                anchors: anchor,
                                overlays: [
                                    ["Arrow", {
                                        width: 14,
                                        length: 18,
                                        location: 1,
                                        foldback: 0.7
                                    }],
                                    ["Label", {
                                        label: opt.option_label || "→",
                                        location: 0.5,
                                        cssClass: "arrow-label"
                                    }]
                                ],
                                connector: ["Flowchart", { stub: 30, gap: 5, cornerRadius: 5, alwaysRespectStubs: true }]
                            });
                        }
                    });
                }

                if (q.conditions?.length) {
                    q.conditions.forEach((cond, i) => {
                        if (cond.condition_type === 'go_to_step_condition' && q.question_data?.conditional_go_to_step) {
                            const anchor = (i % 2 === 0) ? ["Right", "Left"] : ["Left", "Right"];
                            connectNodes(q.id, q.question_data.conditional_go_to_step, cond.condition_value || "GoTo", anchor);
                        }

                        if (cond.condition_type === 'question_label_condition' && q.question_data?.next_question_id) {
                            const anchor = (i % 2 === 0) ? ["Right", "Left"] : ["Left", "Right"];
                            connectNodes(q.id, q.question_data.next_question_id, cond.condition_value || "Yes");
                        }
                    });
                }

            });
        });
    });
</script>




@endsection
