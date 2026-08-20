@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="components-preview wide-md mx-auto">
                         <div class="nk-block nk-block-lg">
                              <div class="nk-block-head">
                                   <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Document Questions</h4>
                                   </div>
                                   <div class="d-flex justify-content-end p-2">
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group">
                                                  <a href="{{ url('/admin-dashboard/document-questions') }}" class="btn btn-light">Add Document Question</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table" id="catg_table">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">#</th>
                                                       <th scope="col">Title</th>
                                                       <!-- <th scope="col">Document Categories</th> -->
                                                  </tr>
                                             </thead>
                                             <tbody>
                                             <tr>
                                                  <th scope="row"></th>
                                                  <td><a href=""></a></td>
                                                  <td>
                                                  </td>
                                             </tr>
                                             </tbody>
                                        </table>
                                   </div>
                              </div><!-- .card-preview -->
                         </div><!-- .nk-block -->
                    </div><!-- .components-preview -->
               </div>
          </div>
     </div>
</div>

@endsection