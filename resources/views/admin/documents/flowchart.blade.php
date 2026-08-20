@extends('admin_layout.master')
@section('content')

<style>
.feedback_div {
    margin-top: 20px;
    text-align: center; 
}
#feedback_btn {
    padding: 10px 20px;
    font-size: 16px;
}
</style>

<div class="nk-content">
     <div class="container-fluid">
          <div
               id="react-root"
               data-questions='@json($document_questions)'
               data-contracttext='@json($questionContractMap)'>
          </div>

          <div class="feedback_div">
               <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#feedbackBtn">Feedback</button>
          </div>
          <div class="modal fade" tabindex="-1" id="feedbackBtn" aria-modal="true" role="dialog">
               <div class="modal-dialog" role="document">
                    <div class="modal-content">
                         <div class="modal-header">
                              <h5 class="modal-title">Feedback</h5>
                              <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                   <em class="icon ni ni-cross"></em>
                              </a>
                         </div>
                         <div class="modal-body">
                              <form id="feedbackForm" action="{{ route('admin.document.feedback') }}" method="post">
                                   @csrf
                                   <input type="hidden" name="id" value="{{ $_GET['id'] }}">
                                   <div class="form-group"> 
                                        <textarea class="form-control" id="feedback" name="feedback"></textarea>
                                   </div>
                                   <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Send</button>
                                   </div>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

@endsection


