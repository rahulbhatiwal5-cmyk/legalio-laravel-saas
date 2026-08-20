@extends('admin_layout.master')
@section('content')
<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
               <h4 class="nk-block-title">Configuration</h4>
          </div>
     </div>

     <div class="container-fluid">
          <form action="{{ route('admin.config.update') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="type" value="{{ request()->input('type', 'review') }}">

               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         @foreach($data as $item)
                              <div class="col-md-8 mt-2">
                                   <div class="form-group">
                                        <label class="form-label" for="{{ $item->key }}">{{ $item->name  ?? ""}} </label>
                                        <input type="{{$item->ftype}}" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" value="{{  $item->value  }}">
                                   </div>
                              </div>
                         @endforeach
                    </div>
               </div>
               <div class="mt-3">
                    <button class="btn btn-primary" type="submit" id="saveSettingsBtn" >Save</button>
               </div>
          </form>
     </div>
</div>


@endsection


