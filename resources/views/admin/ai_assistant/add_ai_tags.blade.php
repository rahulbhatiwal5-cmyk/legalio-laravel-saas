@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">{{ isset($tag) ? 'Edit' : 'Add' }} FAQ</h4>
                {{-- <h4 class="nk-block-title">Add Tag</h4> --}}
            </div>
        </div>
     
        <div class="container-fluid">
            {{-- <form action="{{ route('admin.dashboard.store.ai.FAQ') }}" method="post" enctype="multipart/form-data"> --}}
            <form action="{{ route('admin.dashboard.store.ai.tag') }}" method="post" enctype="multipart/form-data">
                @csrf
                <!-- If it's an update, pass the FAQ ID as a hidden field -->
                @if(isset($tag))
                    <input type="hidden" name="tag_id" value="{{ $tag->id }}">
                @endif

                <div class="row main_section">
                    <div class="col-md-8 left_content">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">

                                <!-- Tag Name -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="name"><b><h5>Name</h5></b></label>
                                        <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name', $tag->name ?? '') }}">
                                        {{-- <input type="text" class="form-control form-control-lg" id="question" name="question" value="{{ old('question', $faq->question ?? '') }}"> --}}
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12">
                                    <div class="nk-block-head-content">
                                        <div class="up-btn mbsc-form-group">
                                            {{-- <button class="btn btn-primary" type="submit">Add Tag</button> --}}
                                            <button class="btn btn-primary" type="submit">{{ isset($tag) ? 'Update' : 'Add' }} Tag</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection