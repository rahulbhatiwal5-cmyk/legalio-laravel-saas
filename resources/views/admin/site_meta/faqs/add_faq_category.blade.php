@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <form action="{{ url('admin-dashboard/add/procc') }}" id="faq-category" method="post" enctype="multipart/form-data">
               @csrf
               <div class="nk-block-head">
                   <div class="nk-block-head-content">
                       <h4 class="nk-block-title">
                           @if(isset($faqCategory)) Edit Category @else Add Category @endif
                       </h4>
                   </div>
               </div>
           
               <input type="hidden" name="id" value="{{ $faqCategory->id ?? '' }}">
           
               <div class="card card-bordered card-preview">
                   <div class="card-inner">
                       {{-- Category Name --}}
                       <div class="col-md-12 pb-2">
                           <div class="form-group">
                               <label class="form-label" for="name">Category Name</label>
                               <input type="text" class="form-control" id="name" name="name"
                                      placeholder="Add Category Name"
                                      value="{{ old('name', $faqCategory->category_name ?? '') }}">
                               @error('name')
                                   <span class="text-danger small d-block mt-1">{{ $message }}</span>
                               @enderror
                           </div>
                       </div>
           
                       {{-- Slug --}}
                       <div class="card card-bordered card-preview mt-2">
                           <div class="card-inner">
                               <div class="row g-3">
                                   <div class="col-lg-2">
                                       <div class="form-group">
                                           <label class="form-label" for="slug">Slug</label>
                                       </div>
                                   </div>
                                   <div class="col-lg-10">
                                       <div class="form-group">
                                           <input type="text" class="form-control" id="slug" name="slug"
                                                  value="{{ old('slug', $faqCategory->slug ?? '') }}"
                                                  @if(isset($faqCategory)) readonly @endif>
                                           @error('slug')
                                               <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                           @enderror
                                       </div>
                                   </div>
                               </div>
           
                               {{-- Description --}}
                               <div class="row g-3 mt-2">
                                   <div class="col-lg-2">
                                       <div class="form-group">
                                           <label class="form-label" for="description">Description</label>
                                       </div>
                                   </div>
                                   <div class="col-lg-10">
                                       <div class="form-group">
                                           <textarea class="form-control" id="description" name="description">{{ old('description', $faqCategory->description ?? '') }}</textarea>
                                           @error('description')
                                               <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                           @enderror
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           
               {{-- Submit Button --}}
               <div class="mt-3">
                   <button class="btn btn-primary" type="submit">
                       @if(isset($faqCategory)) Update @else Save @endif
                   </button>
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