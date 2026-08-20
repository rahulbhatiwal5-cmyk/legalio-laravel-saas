@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="components-preview wide-md">
                         <div class="nk-block nk-block-lg">
                              <div class="nk-block-head">
                                   <div class="d-flex justify-content-between p-2">
                                        <div class="nk-block-head-content">
                                             <h4 class="nk-block-title">Plans</h4>
                                        </div>
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group orange-btn">
                                                  <a href="{{ route('admin.add.plans') }}" class="btn btn-primary">Add Plan</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table">
                                             <!-- Remove nowrap -->
                                             <thead>
                                                  <tr>
                                                       <th scope="col">Months</th>
                                                       <th scope="col">Price</th>
                                                       <th scope="col">Currency</th>
                                                       <th scope="col">Status</th>
                                                       <th scope="col">Trial Days</th>
                                                       <th>Action</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  @foreach($plans as $plan)
                                                  <tr>
                                                       <td><a href="{{ route('admin.add.plans', ['id' => $plan->id ?? '']) }}">{{ $plan->number_of_months ?? '' }}</a></td>
                                                       <td>{{ $plan->price ?? '' }}</td>
                                                       <td>{{ $plan->currency ?? '' }}</td>
                                                       <td>{{ $plan->status ?? '' }}</td>
                                                       <td>{{ $plan->trial_days ?? '' }}</td>
                                                       <td class="tb-tnx-action text-center">
                                                            <div class="dropdown">
                                                                 <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                 <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                                      <ul class="link-list-plain">
                                                                           <li><a href="{{ route('admin.add.plans', ['id' => $plan->id ?? '']) }}"><i class="icon ni ni-edit"></i>Edit</a></li>
                                                                           <li><a href="{{ route('admin.delete.plan', ['id' => $plan->id ?? '' ]) }}"><i class="icon ni ni-trash"></i>Delete</a></li>
                                                                      </ul>
                                                                 </div>
                                                            </div>
                                                       </td>
                                                  </tr>
                                                  @endforeach
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