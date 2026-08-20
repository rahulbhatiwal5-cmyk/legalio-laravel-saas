@extends('admin_layout.master')
@section('content')

     <div class="nk-content">
          <div class="container-fluid">
               <div class="nk-content-inner">
                    <div class="nk-content wide">
                         <div class="nk-block">
                              <div class="card card-bordered">
                                   <div class="card-inner">
                                        <div class="card-head">
                                             <h5 class="card-title">Output</h5>
                                        </div>
                                        <div class="card-body">
                                             <div class="row g-gs">
                                                  <div class="col-md-12">
                                                       <div class="form-group">
                                                            <textarea class="form-control" id="ai_response" name="ai_response" rows="50" readonly>{{ json_decode($ai_response ?? '') }}</textarea>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
         

@endsection