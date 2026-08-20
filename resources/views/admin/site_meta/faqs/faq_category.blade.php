@extends('admin_layout.master')
@section('content')

<div class="nk-content">

     <div class="container-fluid">
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="components-preview">
                         <div class="nk-block nk-block-lg">
                              <div class="nk-block-head">
                                   <div class="d-flex justify-content-between p-2">
                                        <div class="nk-block-head-content">
                                             <h4 class="nk-block-title">FAQ Categories</h4>
                                        </div>
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group orange-btn">
                                                  <a href="{{ url('/admin-dashboard/add/faq-category') }}" class="btn btn-primary">Add Category</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">#</th>
                                                       <th scope="col">Name</th>
                                                       <th scope="col">Description</th>
                                                       <th scope="col">Slug</th>
                                                       <th scope="col">Action</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                             @if($faqCategory)
                                             <?php $count = 1; ?>
                                             @foreach($faqCategory as $category)
                                             <tr>
                                                  <td>{{ $count++ }}</td>
                                                  <td><a href="{{ url('/admin-dashboard/edit/faq-category/'.$category->slug ?? '' ) }}">{{ $category->category_name ?? '' }}</td>
                                                  <td>{{ $category->description ?? '----' }}</td>
                                                  <td>{{ $category->slug ?? '' }}</td>
                                                  <td class="tb-tnx-action text-center">
                                                       <div class="dropdown">
                                                           <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em
                                                                   class="icon ni ni-more-h"></em></a>
                                                           <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                               <ul class="link-list-plain">
                                                                   <li>
                                                                      <a href="{{ route('admin.dashboard.edit_faq_category' ,['slug'=>$category->slug]) }}">
                                                                           <i class="icon ni ni-edit"></i>Edit</a>
                                                                   </li>
                                                                   <li>
                                                                      <a href="{{ route('admin.dashboard.delete_faq_category' ,['slug'=>$category->slug]) }}" class="global-delete-link">
                                                                           <i class="icon ni ni-trash"></i>Delete</a></li>
                                                               </ul>
                                                           </div>
                                                       </div>
                                                   </td>
                                             </tr>
                                             @endforeach
                                             </tbody>
                                             @endif
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

@endsection