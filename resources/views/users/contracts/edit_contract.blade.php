@extends('users_layout.custom_header')
@section('title','Editar - Legalio.mx')
@section('content')

<section class="privacy-sec contract-edit">
     <div class="container">
          <div class="contract-header">
               <div class="row document_align">
                    <div class="col-md-12">
                         <div class="contract_heading_div">
                              <h2>Editar Documento</h2>
                              <form method="POST" action="">
                                   @csrf
                                   <textarea id="documentEditor" name="content">{!! html_entity_decode($contract_html) !!}</textarea>

                                   <div class="m-4">
                                        <button type="button" class="pre" onclick="cancelEdit()">
                                             Cancelar
                                        </button>
                                        <button type="button" class="nxt save_text" onclick="saveEditText()">
                                             Guardar
                                        </button>
                                   </div>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</section>


<script src="https://cdn.tiny.cloud/1/d4kd58kh0vycett0ss6di0xsxtihwj0udukkix9dts7o489k/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>


<script>
     tinymce.init({
          selector: 'textarea#documentEditor', 
          height: 400,
          menubar: false,
          plugins: 'lists link image code',
          toolbar: [],
          content_style: "body {font-family: Montserrat, sans-serif; font-size: 14px; overflow-x: hidden; color: #002655}"
     });
</script>

<script>
     function saveEditText() {
          let content = tinymce.get('documentEditor').getContent();  

          if(content !== null && content !== undefined && content !== ''){
               let user_id = "{{ Auth()->user()->id ?? '' }}";
               let document_id = "{{ $id ?? '' }}";
               let order_id = "{{ $order_id ?? '' }}";

               var data = {
                    html: content,
                    user_id: user_id,
                    document_id: document_id,
                    order_id: order_id,
                    type: 'edit_text',
                    _token: "{{ csrf_token() }}"
               }

               $.ajax({
                    url: "{{ route('edit.contract.procc') }}",
                    type: "post",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                         if(response.code == "200"){
                              console.log(response);
                         
                              let url = response.redirect_url;
                              window.location.href = url;
                              
                         }
                    }
               })
          }
     }



     function cancelEdit(){
          // window.location.href = "{{ url('user-dashboard/purchased') }}";
          window.location.href = "{{ url('account/purchased') }}";
     }
</script>


@endsection