import React, { useMemo, useState } from 'react';
import ReactDOM from 'react-dom/client';
import ReactFlow, { MiniMap, Controls, Background } from 'reactflow';
import 'reactflow/dist/style.css';
import { generateFlowData } from '../utils/flowUtils'; 
import {
  CircleNode,
  RectangleNode,
  HexagonNode,
  ParallelogramNode,
  DiamondNode,
  EllipseNode,
  OctagonNode,
  RectangleWide
} from './CustomNodes';
import ContractPopup from './ContractPopup';


const nodeTypes = {
  rectangle: RectangleNode,
  rectangleWide: RectangleWide, 
  circle: CircleNode,
  hexagon: HexagonNode,
  hexagonOutline: HexagonNode, 
  parallelogram: ParallelogramNode,
  diamond: DiamondNode,
  ellipse: EllipseNode,
  octagon: OctagonNode,
};



function FlowChart({ questionsData, contractMap }) {
  const [popupContent, setPopupContent] = useState(null);
  const [isPopupVisible, setIsPopupVisible] = useState(false);
  const { nodes, edges } = useMemo(() => generateFlowData(questionsData), [questionsData]);

  const handleNodeClick = (event, node) => {
    const qid = node.id?.split('-')[1];
    if (!qid || !contractMap || !contractMap[qid]) {
      console.warn(`No contract text found for question ID: ${qid}`);
      return;
    }
  
    setPopupContent(contractMap[qid]);
    setIsPopupVisible(true);
  };
  
  return (
    <div style={{ height: '100vh' }}>
      <ReactFlow
        nodes={nodes}
        edges={edges}
        nodeTypes={nodeTypes}
        fitView
        onNodeClick={handleNodeClick}
      >
        <MiniMap />
        <Controls />
        <Background />
      </ReactFlow>

      <ContractPopup
        content={popupContent}
        isVisible={isPopupVisible}
        onClose={() => setIsPopupVisible(false)}
      />
    </div>
  );
}

export default FlowChart;
