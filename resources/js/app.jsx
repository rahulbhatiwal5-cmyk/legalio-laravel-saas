import React from 'react';
import ReactDOM from 'react-dom/client';
import FlowChart from './components/FlowChart';

const el = document.getElementById('react-root');

if (el) {
  const questionsData = JSON.parse(el.dataset.questions || '[]');
  const contractMap = JSON.parse(el.dataset.contracttext || '{}');
  const root = ReactDOM.createRoot(el);
  root.render(<FlowChart questionsData={questionsData} contractMap={contractMap} />);
}
