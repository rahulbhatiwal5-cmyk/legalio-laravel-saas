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
                                <h4 class="nk-block-title">All FAQs</h4>
                            </div>
                        </div>

                        <!-- Add FAQ Button -->
                        <div class="nk-block-head-content mb-4">
                            <a href="{{ route('admin.dashboard.add.ai.FAQ', ['id' => null]) }}" class="btn btn-primary">Add FAQ</a>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner p-0">
                                <table class="table">
                                    @if(isset($faqs) && $faqs->isNotEmpty())
                                    <thead>
                                        <tr>
                                            <th scope="col">Question</th>
                                            <th scope="col">Answer</th>
                                            <th scope="col">Status</th>
                                            {{-- <th scope="col">Tags</th> --}}
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($faqs as $faq)
                                        <tr>
                                            <td><a href="{{ route('admin.dashboard.add.ai.FAQ', ['id' => $faq->id]) }}">{{ $faq->question ?? '' }}</a></td>
                                            <td>{{ \Str::limit($faq->answer, 100) ?? '' }}...</td>
                                            <td>{{ $faq->status == 1 ? 'Active' : 'Inactive' }}</td>
                                            {{-- <td>
                                                @foreach ($faq->tags as $tag)
                                                <span class="badge bg-dark text-light">{{ $tag->name }}</span>
                                                @endforeach
                                            </td> --}}

                                            <td class="tb-tnx-action text-center">
                                                <div class="dropdown">
                                                    <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                        <ul class="link-list-plain">
                                                            <!-- Edit Link -->
                                                            <li><a href="{{ route('admin.dashboard.add.ai.FAQ', ['id' => $faq->id]) }}"><i class="icon ni ni-edit"></i>Edit</a></li>
                                                            <!-- Delete Link -->
                                                            <li>
                                                                <a href="{{ route('admin.dashboard.ai.FAQ.destroy', ['id' => $faq->id]) }}" onclick="return confirm('Are you sure you want to delete this FAQ?')">
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
                                        <td colspan="4">No FAQs found.</td>
                                    </tr>
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