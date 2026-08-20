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
                                <h4 class="nk-block-title">All Prompts</h4>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner p-0">
                                <table class="table">
                                    @if(isset($prompt) && $prompt->isNotEmpty())
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Location</th>
                                            <th scope="col">Description</th>
                                            <th scope="col"></th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($prompt as $prom)

                                        <tr>

                                            <td><a href="{{ route('edit.prompt',['id'=>$prom->id]) }}">{{ $prom->name ?? ''}}</a></td>
                                            <td>{{ $prom->location ?? ''}}</td>
                                            <td>{{ $prom->description ?? ''}}</td>

                                            <td class="tb-tnx-action text-center">
                                                <div class="dropdown">
                                                    <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                        <ul class="link-list-plain">
                                                            <li><a href="{{ route('edit.prompt',['id'=>$prom->id]) }}"><i class="icon ni ni-edit"></i>Edit</a></li>
                                                            {{-- <form id="deleteForm{{ $prom->id }}" action="{{ route('delete.prompt', ['id' => $prom->id]) }}" method="POST">
                                                                @csrf

                                                                <li>
                                                                    <a href="javascript:void(0);" onclick="document.getElementById('deleteForm{{ $prom->id }}').submit();">
                                                                        <i class="icon ni ni-trash"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </form> --}}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        @endforeach

                                    </tbody>
                                @else
                                <p>No Prompt yet.</p>
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

@endsection
