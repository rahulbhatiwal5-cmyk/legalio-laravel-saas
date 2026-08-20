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
                                        <h4 class="nk-block-title">Products</h4>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table" id="catg_table">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">#</th>
                                                       <th scope="col">Name</th>
                                                       <th scope="col">Price</th>
                                                       <th scope="col">Categories</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                             @if(isset($products) && $products->isNotEmpty())
                                                  <?php 
                                                       $count = 1;
                                                  ?>
                                                  @foreach($products as $data)
                                                  <tr>
                                                       <th scope="row">{{ $count ?? '' }}</th>
                                                       <td class="name"><a href="{{ url('/admin-dashboard/product/'.$data->id) }}">{{ $data->name ?? '' }}</a></td>
                                                       <td>${{ $data->regular_price ?? '-' }}</td>
                                                       <td>{{ $data->category->title ?? '' }}</td>
                                                  </tr>
                                                  <?php $count++; ?>
                                                  @endforeach
                                             @endif
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