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
                                        <h4 class="nk-block-title">Document Question Types</h4>
                                   </div>
                                   <div class="d-flex justify-content-end p-2">
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group">
                                                  <a href="{{ url('/admin-dashboard/question-type') }}" class="btn btn-light">Add Type</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table">
                                        @if(isset($question_types) && $question_types != null)
                                             <thead>
                                                  <tr>
                                                       <th scope="col">#</th>
                                                       <th scope="col">Type</th>
                                                       <!-- <th scope="col">Document Categories</th> -->
                                                  </tr>
                                             </thead>
                                             <tbody>
                                             <?php $count=1; ?>
                                             @foreach($question_types as $type)
                                                  <tr>
                                                       <th scope="row">{{ $count++ }}</th>
                                                       <td><a href="{{ url('/admin-dashboard/edit-question-type/'.$type->slug) }}">{{ $type->name ?? '' }}</a></td>
                                                  </tr>
                                             @endforeach
                                             </tbody>
                                        @else
                                             No type found.
                                        @endif
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