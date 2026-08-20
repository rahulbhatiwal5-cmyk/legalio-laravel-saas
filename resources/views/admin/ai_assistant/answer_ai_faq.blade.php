@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">{{ isset($faq) ? 'Edit' : 'Add' }} FAQ</h4>
            </div>
        </div>
     
        <div class="container-fluid">
            <form action="{{ route('admin.dashboard.store.ai.FAQ.answer') }}" method="post" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="chat_id" value={{ $chat->id }}>

                <div class="row main_section">
                    <div class="col-md-8 left_content">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">

                                <!-- Question Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="question"><b><h5>Question</h5></b></label>
                                        <input type="text" class="form-control form-control-lg" id="question" name="question" value="{{ old('question', $chat->question ?? '') }}">
                                        @error('question')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Answer Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="answer"><b><h5>Answer</h5></b></label>
                                        <textarea name="answer" id="answer" cols="30" rows="10" class="form-control form-control-lg">{{ old('answer') }}</textarea>
                                        @error('answer')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Status Field (active/inactive) -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="status"><b><h5>Status</h5></b></label>
                                        <select name="status" id="status" class="form-control form-control-lg">
                                            <option value="1" > Active</option>
                                            <option value="0" > Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="tags"><h5><b>Tags</b></h5></label>

                                    <select name="tags[]" id="tags" class="form-control form-control-lg select2" multiple="multiple">
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}" >
                                                {{-- @if (in_array($tag->id, old('tags', isset($selectedTagIds) ? $selectedTagIds : []))) selected @endif> --}}
                                                {{ $tag->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tags')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                                <hr>

                                <!-- Submit Button -->
                                <div class="col-md-12">
                                    <div class="nk-block-head-content">
                                        <div class="up-btn mbsc-form-group">
                                            <button class="btn btn-primary" type="submit"> Add FAQ </button>
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