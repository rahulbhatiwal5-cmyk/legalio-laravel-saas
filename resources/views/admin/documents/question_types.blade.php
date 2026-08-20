@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
          <form action="{{ url('admin-dashboard/add-question-type') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="id" value="{{ $question_type->id ?? '' }}">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="name"><b><h4>@if(isset($question_type) && $question_type != null)Edit Type @else Add Type @endif</b></h4></label>
                                   <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Add Type Name" value="{{ $question_type->name ?? '' }}">
                              </div>
                         </div>
                         <div class="card card-bordered card-preview mt-2">
                              <div class="card-inner">
                                   <div class="row g-3">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="slug">Slug</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <input type="text" class="form-control" id="slug" name="slug" value="{{ $question_type->slug ?? '' }}">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               <div class="mt-3">
               @if(isset($question_type) && $question_type != null)
                    <button class="btn btn-primary" type="submit">Update</button>
               @else
                    <button class="btn btn-primary" type="submit">Save</button>
               @endif
               </div>
          </form>
    </div>
</div>

<script>
     $('#name').on('keyup',function(){
          const name = $(this).val();
          const url = name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g, '');
          $('#slug').val(url);
     })
</script>

@endsection