@extends('admin_layout.master')
@section('content')

<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Users</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end p-2">
                            <div class="nk-block-head-content">
                                <div class="mbsc-form-group">
                                    <a href="{{ route('add.user') }}" class="btn btn-light">Add User</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            @if(isset($users) && $users->isNotEmpty())
                            <table id="data_order_table" class="datatable-init nowrap table">                                     
                                <thead>
                                    <tr>
                                        <!-- <th>#</th> -->
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $count = 1;
                                    ?>
                                    @foreach($users as $user)
                                    <tr>
                                        <!-- <th scope="row">{{ $count ?? '' }}</th> -->
                                        <td><a href="{{ route('add.user',['id' => $user->id ?? '' ]) }}">{{ $user->first_name ?? '' }}</a></td>
                                        <td>{{ $user->last_name ?? '' }}</td>
                                        <td>{{ $user->email ?? '' }}</td>
                                        <td>
                                            @if($user->is_admin == 1)
                                                Super Admin
                                            @elseif($user->is_admin == 2)
                                                Contract Description Editor
                                            @elseif($user->is_admin == 3)
                                                User Support Agent
                                            @else
                                                User
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '' }}</td>
                                        <td class="tb-tnx-action text-center">
                                            <div class="dropdown">
                                                <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                    <ul class="link-list-plain">
                                                        <li><a href="{{ route('add.user',['id' => $user->id ?? '' ]) }}"><i class="icon ni ni-edit"></i>Edit</a></li>
                                                        <li><a href="{{url('/admin-dashboard/delete-user/'.$user->id)}}"><i class="icon ni ni-trash"></i>Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $count++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <p class="text-center">User Not Found</p>
                            @endif
                        </div>
                    </div>
                </div><!-- .card-preview -->
            </div><!-- .nk-block -->
        </div><!-- .components-preview -->
    </div>
</div>
@endsection