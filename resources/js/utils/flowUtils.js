export function generateFlowData(questionsData) {
     const nodes = [];
     const edges = [];
     const addedNodes = new Set();
   
     const nodeSpacing = 150;
     let maxY = 0;
   
     questionsData.forEach((q, index) => {
          const questionType = q.type || 'textbox';
          const shapeType = getShapeType(questionType);
          const nodeId = `Q-${q.id}`;
          const yPos = 50 + index * nodeSpacing;
     
          if (!addedNodes.has(nodeId)) {
               nodes.push({
                    id: nodeId,
                    type: shapeType,
                    data: {},
                    position: { y: yPos, x: 50 },
               });
               addedNodes.add(nodeId);
          }
   
          if (yPos > maxY) maxY = yPos;
     });
   
     const checkoutNodeId = 'END-CHECKOUT';
     nodes.push({
          id: checkoutNodeId,
          data: { label: 'Checkout' },
          position: { y: maxY + nodeSpacing + 100, x: 50 }, 
          style: {
               background: '#ffeeba',
               border: '1px solid #ffc107',
               padding: 10,
               borderRadius: 5,
          },
     });
   
     questionsData.forEach((q, index) => {
          const nodeId = `Q-${q.id}`;
          const isToCheckout = !q.question_data?.next_question_id;

          edges.push({
               id: `e-${q.id}-${isToCheckout ? 'checkout' : q.question_data.next_question_id}`,
               source: nodeId,
               target: isToCheckout ? checkoutNodeId : `Q-${q.question_data.next_question_id}`,
               label: '',
               animated: true,
          });

   
          q.options?.forEach((opt, i) => {
               const isToCheckout = !opt.next_question_id;
          
               edges.push({
                    id: `opt-${q.id}-${isToCheckout ? 'checkout' : opt.next_question_id}-${i}`,
                    source: nodeId,
                    target: isToCheckout ? checkoutNodeId : `Q-${opt.next_question_id}`,
                    label: opt.option_label || '',
                    animated: true,
               });
          });
   
          q.conditions?.forEach((cond, i) => {
               if (cond.condition_type === 'go_to_step_condition') {
                    const isToCheckout = !q.question_data?.conditional_go_to_step;
          
                    edges.push({
                         id: `cond-${q.id}-${isToCheckout ? 'checkout' : q.question_data.conditional_go_to_step}-${i}`,
                         source: nodeId,
                         target: isToCheckout ? checkoutNodeId : `Q-${q.question_data.conditional_go_to_step}`,
                         label: cond.conditional_question_value || 'GoTo',
                         animated: true,
                         type: 'smoothstep',
                    });
               }
   
               if (cond.condition_type === 'question_label_condition') {
                    const nodeToUpdate = nodes.find(n => n.id === nodeId);
                    if (nodeToUpdate) {
                         nodeToUpdate.data.label = cond.question_label || nodeToUpdate.data.label;
                    }
          
                    const isToCheckout = !q.question_data?.next_question_id;
          
                    edges.push({
                         id: `cond-label-${q.id}-${isToCheckout ? 'checkout' : q.question_data.next_question_id}-${i}`,
                         source: nodeId,
                         target: isToCheckout ? checkoutNodeId : `Q-${q.question_data.next_question_id}`,
                         label: cond.conditional_question_value || 'Yes',
                         animated: true,
                         type: 'step',
                    });
               }
          });
     });
   
     return { nodes, edges };
}
   
   

function getShapeType(type) {

     switch (type) {
          case 'textbox':
               return 'rectangle';
          case 'textarea':
               return 'rectangleWide';
          case 'dropdown':
               return 'diamond';
          case 'radio-button':
               return 'diamond';
          case 'date-field':
               return 'parallelogram';
          case 'pricebox':
               return 'diamond';
          case 'number-field':
               return 'ellipse';
          case 'percentage-box':
               return 'octagon';
          case 'dropdown-link':
               return 'hexagonOutline';
          default:
               return 'rectangle';
     }
}
 