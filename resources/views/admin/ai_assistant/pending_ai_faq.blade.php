@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md ">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Pending FAQs</h4>
                            </div>
                        </div>

                        <!-- Add Tags Button -->
                        {{-- <div class="nk-block-head-content mb-4">
                            <a href="{{ route('admin.dashboard.add.ai.tag') }}" class="btn btn-primary">Add Tags</a>
                            
                        </div> --}}

                        <div class="card card-bordered card-preview">
                            <div class="card-inner p-0">
                                <table class="table">
                                    @if(isset($chats) && $chats->isNotEmpty())
                                        <thead>
                                            <tr>
                                                <th scope="col">Questions</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($chats as $chat)
                                            <tr>
                                                <td><a href="{{ route('admin.dashboard.answer.ai.FAQ' , ['id' => $chat->id]) }}" >{{ $chat->question }}</a></td>
                                                {{-- <td>{{$tag->name}}</td> --}}
                                                

                                                <td class="tb-tnx-action text-center">
                                                    <div class="dropdown">
                                                        <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                            <ul class="link-list-plain">
                                                                <!-- Edit Link -->
                                                                <li><a href="{{ route('admin.dashboard.answer.ai.FAQ' , ['id' => $chat->id]) }}"><i class="icon ni ni-edit"></i>Answer</a></li>
                                                                <!-- Delete Link -->
                                                                <li>
                                                                    <a href="{{ route('admin.dashboard.ai.pending.FAQ.destroy', ['id' => $chat->id]) }}" onclick="return confirm('Are you sure you want to delete this question?')">
                                                                        <i class="icon ni ni-trash"></i> Delete
                                                                    </a>                                                                    
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            @endforeach

                                        </tbody>
                                    @else
                                    <tr>
                                        <td colspan="4">No Question found.</td>
                                    </tr>
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