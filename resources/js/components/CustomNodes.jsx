import React from 'react';
import { Handle } from 'reactflow';

export const RectangleNode = ({ id, data }) => (
  <div style={{ padding: 10, border: '2px solid #555', borderRadius: 4, background: '#fff' }}>
    <Handle type="target" position="top" />
    <div style={{ width: 80, height: 40 }} />
    <Handle type="source" position="bottom" />
  </div>
);

export const RectangleWide = ({ id, data }) => (
  <div style={{ padding: 10, border: '2px solid #555', borderRadius: 4, background: '#fff' }}>
    <Handle type="target" position="top" />
    <div style={{ width: 140, height: 40 }} />
    <Handle type="source" position="bottom" />
  </div>
);

export const CircleNode = ({ id, data }) => (
  <div style={{ width: 60, height: 60, borderRadius: '50%', border: '2px solid #555', background: '#fff' }}>
    <Handle type="target" position="top" />
    <Handle type="source" position="bottom" />
  </div>
);

export const HexagonNode = ({ id, data }) => (
  <div style={{
    width: 70,
    height: 40,
    background: '#fff',
    clipPath: 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)',
    border: '2px solid #555',
  }}>
    <Handle type="target" position="top" />
    <Handle type="source" position="bottom" />
  </div>
);

export const ParallelogramNode = ({ id, data }) => (
  <div style={{
    width: 100,
    height: 40,
    background: '#fff',
    transform: 'skewX(-20deg)',
    border: '2px solid #555',
  }}>
    <Handle type="target" position="top" />
    <Handle type="source" position="bottom" />
  </div>
);

export const DiamondNode = ({ id, data }) => (
  <div style={{
    width: 60,
    height: 60,
    background: '#fff',
    transform: 'rotate(45deg)',
    border: '2px solid #555',
  }}>
    <Handle type="target" position="top" />
    <Handle type="source" position="bottom" />
  </div>
);

export const EllipseNode = ({ id, data }) => (
  <div style={{
    width: 100,
    height: 60,
    background: '#fff',
    borderRadius: '50%',
    border: '2px solid #555',
  }}>
    <Handle type="target" position="top" />
    <Handle type="source" position="bottom" />
  </div>
);

export const OctagonNode = ({ id, data }) => (
  <div style={{
    width: 70,
    height: 70,
    background: '#fff',
    clipPath: 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)',
    border: '2px solid #555',
  }}>
    <Handle type="target" position="top" />
    <Handle type="source" position="bottom" />
  </div>
);
