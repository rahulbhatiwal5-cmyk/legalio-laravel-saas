import React from 'react';

function ContractPopup({ content, isVisible, onClose }) {
     if (!isVisible) return null;

     return (
          <div style={{
               position: 'fixed',
               top: '20%',
               left: '30%',
               width: '20%',
               padding: '20px',
               background: '#fff',
               border: '1px solid #fff',
               borderRadius: '8px',
               zIndex: 1000,
               boxShadow: '0 0 10px rgba(0,0,0,0.3)'
               }}>
               <h6>Contract Text</h6>
               <div style={{ maxHeight: '200px', overflowY: 'auto' }}>{content}</div>
               <button onClick={onClose} style={{ marginTop: '10px' }}>Close</button>
          </div>
     );
}

export default ContractPopup;
