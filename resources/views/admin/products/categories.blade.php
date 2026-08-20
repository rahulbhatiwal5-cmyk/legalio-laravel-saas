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
                                        <h4 class="nk-block-title">Product Categories</h4>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table" id="catg_table">
                                             <thead>
                                                  <tr>
                                                       <th scope="col"></th>
                                                       <th scope="col">Image</th>
                                                       <th scope="col">Name</th>
                                                       <th scope="col">Description</th>
                                                       <th scope="col">Slug</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                             @if(isset($categories) && $categories->isNotEmpty())
                                                  <?php 
                                                       $count = 1;
                                                  ?>
                                                  @foreach($categories as $data)
                                                  <tr>
                                                       <th scope="row">{{ $count ?? '' }}</th>
                                                       <td>
                                                            @if(isset($data->media) && $data->media != null)
                                                                 <img src="{{ asset('storage/'.$data->media->file_name) }}" alt="img" height="80px" width="80px">
                                                            @endif
                                                       </td>
                                                       <td class="title"><a href="{{ url('/admin-dashboard/product-categories/'.$data->slug) }}">{{ $data->title ?? '' }}</a></td>
                                                       <td>{{ $data->description ?? '-' }}</td>
                                                       <td>{{ $data->slug ?? '' }}</td>
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