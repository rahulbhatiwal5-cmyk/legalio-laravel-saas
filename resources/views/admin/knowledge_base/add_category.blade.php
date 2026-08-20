@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <form action="{{route('knowledge.base.store.category')}}" method="post" enctype="multipart/form-data">
            @csrf
            {{-- <input type="hidden" name="id" value="{{ $register->id ?? '' }}"> --}}
            {{-- <input type="hidden" name="bg_img_id" id="bg_img_id" value=""> --}}
            <div class="row main_section">
                <div class="col-md-8 left_content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name"><b><h4>Category Title</b></h4></label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="image">Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <h6>Short Description</h6>
                            {{-- <hr> --}}

                            <div class="col-md-12">
                                <div class="form-group">

                                    <input type="text" class="form-control form-control-lg" id="description" name="description" value="{{ old('description') }}">
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-4 right-content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">

                            <div class="d-flex justify-content-between align-items-center">



                                <div class="nk-block-head-content butn-cls">
                                    <div class="mbsc-form-group view_btn">

                                         {{-- <a href="{{ route('help.center') }}" target="_blank" class="view_page">View Page</a> --}}

                                    </div>
                               </div>
                               <div class="nk-block-head-content">
                                    <div class="up-btn mbsc-form-group">
                                        <button class="btn btn-primary" type="submit">Add</button>
                                    </div>
                               </div>

                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
