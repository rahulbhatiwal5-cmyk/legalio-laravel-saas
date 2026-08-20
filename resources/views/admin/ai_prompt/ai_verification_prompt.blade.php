@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <div class="nk-block-head">
               <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Prompt Verification</h4>
               </div>
          </div>
          <form action="{{ route('prompt.verification.add') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="id" value="{{ $prompt_verification->id ?? '' }}">
               <div class="row main_section">
                    <div class="col-md-8 left_content">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="add_box">
                                        <b><h5>AI Verification</h5></b>
                                        <hr>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="ai_prompt">Prompt</label>
                                                  <textarea class="form-control form-control-lg" id="ai_prompt" name="ai_prompt">{{ old('ai_prompt', $prompt_verification->ai_prompt ?? '') }}</textarea>
                                             </div>
                                        </div>
                                        <b><h5>Conflict Resolution</h5></b>
                                        <hr>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="conflict_prompt">Prompt</label>
                                                  <textarea class="form-control form-control-lg" id="conflict_prompt" name="conflict_prompt">{{ old('conflict_prompt', $prompt_verification->conflict_prompt ?? '') }}</textarea>
                                             </div>
                                        </div>
                                   </div>
                                   <br>
                                   <div class="col-md-12">
                                        <div class="nk-block-head-content">
                                             <div class="up-btn mbsc-form-group">
                                                  <button class="btn btn-primary" type="submit">Save</button>
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

@endsection