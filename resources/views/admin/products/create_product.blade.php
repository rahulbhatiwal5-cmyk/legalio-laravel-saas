@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          @if(isset($product) && $product != null)
          <form action="{{ url('/admin-dashboard/add-product') }}" method="post" enctype="multipart/form-data">
          @else
          <form action="{{ url('/admin-dashboard/add-product') }}" method="post" enctype="multipart/form-data">
          @endif
               @csrf
               <input type="hidden" name="id" value="{{ $product->id ?? '' }}">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="name"><b><h4>@if(isset($product) && $product != null)Edit Product @else Add New Product @endif</b></h4></label>
                                   <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ $product->name ?? '' }}" placeholder="Product name">
                              </div>
                         </div>
                         <hr>
                         <h5>Product description</h5>
                         <hr>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="description"></label>
                                   <textarea class="form-control" id="description" name="description">{{ $product->product_description ?? '' }}</textarea>
                              </div>
                         </div>
                         <hr>
                         <h5>Aditional Information</h5>
                         <hr>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="additional_information">Aditional Information</label>
                                   <textarea class="form-control" id="additional_information" name="additional_information">{{ $product->additional_information ?? '' }}</textarea>
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="product_category">Product Category</label>
                                   <select class="form-select" id="product_category" name="product_category" tabindex="-1" aria-hidden="true">
                                   @if(isset($category) && $category->isNotEmpty())
                                   <option value="">Select</option>
                                   @foreach($category as $data)
                                        @if(isset($product) && $product != null)
                                             @if($product->category_id == $data->id)
                                                  <option value="{{ $data->id ?? '' }}" selected>{{ $data->title ?? '' }}</option>
                                             @else
                                                  <option value="{{ $data->id ?? '' }}">{{ $data->title ?? '' }}</option>
                                             @endif
                                        @else
                                             <option value="{{ $data->id ?? '' }}">{{ $data->title ?? '' }}</option>
                                        @endif
                                   @endforeach
                                   @endif
                                   </select>
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="regular_price">Regular Price($)</label>
                                   <input type="text" class="form-control" id="regular_price" name="regular_price" value="{{ $product->regular_price ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="sale_price">Sale Price($)</label>
                                   <input type="text" class="form-control" id="sale_price" name="sale_price" value="{{ $product->sale_price ?? '' }}">
                              </div>
                         </div>
                    </div>
               </div>
               <div class="mt-3">
               @if(isset($product) && $product != null)
                    <button class="btn btn-primary" type="submit">Update</button>
               @else
                    <button class="btn btn-primary" type="submit">Save</button>
               @endif
               </div>
          </form>
     </div>
</div>

<script>

     ClassicEditor
     .create( document.querySelector('#description'))
     .catch( error => {
          console.error( error );
     });

     ClassicEditor
     .create( document.querySelector('#additional_information'))
     .catch( error => {
          console.error( error );
     });
     
</script>

@endsection
