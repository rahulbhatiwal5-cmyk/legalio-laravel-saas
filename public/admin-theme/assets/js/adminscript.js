function isNotView(){
     Swal.fire({
         icon: 'error',
         title: 'View Not Found',
         text: 'This document cannot be viewed because it has not been published yet.'
     });
 }