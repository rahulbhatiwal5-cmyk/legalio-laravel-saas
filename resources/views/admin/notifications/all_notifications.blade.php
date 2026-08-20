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
                                    <h4 class="nk-block-title">Notifications</h4>
                                </div>
                                <div class="nk-block-head-content">
                                    <div class="mbsc-form-group orange-btn">
                                        <a href="{{ route('admin.dashboard.Notifications') }}" class="btn btn-primary">Add Notifications</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table id="data_order_table" class="datatable-init table">
                                    <!-- Remove nowrap -->

                                    @if(isset($notifications) && $notifications->isNotEmpty())
                                    <thead>
                                        <tr>
                                            {{-- <th scope="col">#</th> --}}
                                            <th scope="col">Title</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Content</th>
                                            <th scope="col">Action</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                                  $count = 1;
                                             ?>
                                        @foreach($notifications as $notification)
                                        <tr>
                                            {{-- <th scope="row">{{ $count ?? '' }}</th> --}}
                                            <td><a href="{{ route('admin.notifications.edit', $notification->id) }}">{{$notification->title ?? '' }}</a></td>
                                            <td>{{ $notification->type ? ucwords(str_replace('_', ' ', $notification->type)) : '' }}</td>
                                           <td>{{$notification->content ?? '' }}</td>

                                            <td class="tb-tnx-action text-center">
                                                <div class="dropdown">
                                                    <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em
                                                            class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                        <ul class="link-list-plain">
                                                            <li>
                                                                <a href="{{ route('admin.notifications.edit', $notification->id) }}">
                                                                    <i class="icon ni ni-edit"></i>Edit</a>
                                                            </li>
                                                            <!--<li>

                                                                <a href=""
                                                                    target="_blank"><i class="icon ni ni-eye"></i>View</a>

                                                            </li>-->
                                                            <form id="delete-form" method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <li><a href="javascript:void(0);"
                                                                onclick="submitDelete('{{ route('admin.notifications.destroy', $notification->id) }}')"
                                                                class="text-danger">
                                                                <i class="icon ni ni-trash"></i> Delete
                                                             </a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                            <td></td>

                                        </tr>
                                        <?php $count++; ?>
                                        @endforeach
                                    </tbody>
                                    @else
                                    No data found.
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
<script>
    function submitDelete(url) {
        const form = document.getElementById('delete-form');
        form.action = url;
        form.submit();
    }
</script>
@endsection

