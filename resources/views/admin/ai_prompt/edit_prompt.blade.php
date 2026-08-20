@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                 <h4 class="nk-block-title">Edit Prompts</h4>
            </div>
        </div>
        <form action="{{route('update.prompt')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $prompt->id ?? '' }}">

            <div class="row main_section">
                <div class="col-md-8 left_content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">

                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="type"><b><h5>ID</b></h5></label>
                                    <?php
                                    $prompts=[
                                    'document'=>'Document',
                                    'article'=>'Article'

                                    ];
                                    ?>

                                    <x-Dropdown name="type" id="type" :option="$prompts" :selected="$prompt->type ?? '' " />
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> -->
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name"><b><h5>ID</b></h5></label>
                                    <?php
                                        $names=[
                                            'Short Description'=>'Short Description',
                                            'Long Description'=>'Long Description',
                                            'Article Section' => 'Article Section',
                                            'Meta Description' => 'Meta Description',
    
                                        ];
                                    ?>

                                    <x-Dropdown name="name" id="name" :option="$names" :selected="$prompt->name ?? '' " />
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="location"><b><h5>Location</b></h5></label>
                                    <?php
                                        $locations=[
                                            'frontpage'=>'Frontpage',
                                            'document'=>'Document'
    
                                        ];
                                    ?>

                                    <x-Dropdown name="location" id="location" :option="$locations" :selected="$prompt->location ?? '' " />
                                    @error('location')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name"><b><h5>Name</b></h5></label>

                                    <input type="text" class="form-control form-control-lg" id="name" name="name" value ="{{ old('name',$prompt->name) }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="description"><b><h5>Description</b></h5></label>

                                    <input type="text" class="form-control form-control-lg" id="description" name="description" value ="{{ old('description',$prompt->description) }}">
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="prompt_ai_model"><b><h5>AI Model</b></h5></label>
                                    <select name="prompt_ai_model" id="prompt_ai_model"  class="form-control form-control-lg" style="appearance: auto;">
                                        @foreach ($aiModelRefs as $ref )
                                        <option value="{{ $ref }}" {{ $prompt->prompt_ai_model == $ref ? 'selected' : ''}}>{{ $ref }}</option>
                                        @endforeach
                                    </select>
                                    @error('prompt_ai_model')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="prompt"><b>
                                            <h5>Prompt
                                        </b></h5></label>
                                    <textarea name="prompt" id="prompt" cols="30" rows="10"
                                        class="form-control form-control-lg">{{ $prompt->updated_prompt ?? '' }}</textarea>
                                    @error('prompt')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="ai_verification_model"><b><h5>AI Verification</b></h5></label>
                                    <select name="ai_verification_model" id="ai_verification_model"  class="form-control form-control-lg" style="appearance: auto;">
                                        <option value="disabled" selected>Disabled</option>
                                        @foreach ($aiModelRefs as $ref )
                                        <option value="{{ $ref ?? '' }}" {{ $prompt->ai_verification_model == $ref ? 'selected' : ''}}>{{ $ref ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="nk-block-head-content">
                                    <div class="up-btn mbsc-form-group">
                                        <button class="btn btn-primary" type="submit">Update</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const aiModelSelect = document.getElementById('prompt_ai_model');
        const verificationModelSelect = document.getElementById('ai_verification_model');

        function updateVerificationOptions() {
            const selectedModel = aiModelSelect.value;

            Array.from(verificationModelSelect.options).forEach(option => {
                option.disabled = false;
            });

            Array.from(verificationModelSelect.options).forEach(option => {
                if (option.value === selectedModel) {
                    option.disabled = true;
                    if (verificationModelSelect.value === selectedModel) {
                        verificationModelSelect.value = "disabled";
                    }
                }
            });
        }

        aiModelSelect.addEventListener('change', updateVerificationOptions);
        updateVerificationOptions(); 
    });
</script>

@endsection
