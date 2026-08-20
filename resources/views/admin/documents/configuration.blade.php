@extends('admin_layout.master')
@section('content')


@php 

     $languages = [
          'English',
          'Spanish',
          'French',
          'German',
          'Mandarin',
          'Hindi',
          'Arabic',
          'Portuguese',
          'Russian',
          'Japanese',
          'Korean',
          'Italian',
          'Dutch',
          'Swedish',
          'Turkish',
          'Vietnamese',
          'Thai',
          'Bengali',
          'Persian',
     ];

     $countries = [
          'United States',
          'Mexico',
          'France',
          'Germany',
          'China',
          'India',
          'Saudi Arabia',
          'Brazil',
          'Russia',
          'Japan',
          'South Korea',
          'Italy',
          'Netherlands',
          'Sweden',
          'Turkey',
          'Vietnam',
          'Thailand',
          'Bangladesh',
          'Iran',
     ]
@endphp

<div class="nk-content">
     <div class="nk-block-head">
          <div class="nk-block-head-content">
               <h4 class="nk-block-title">Configuration</h4>
          </div>
     </div>

     <div class="container-fluid">
          <form action="{{ route('admin.add.global.configuration') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="type" value="">

               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="language">Language</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2" id="language" name="language">
                                             <option value="">Select</option>
                                             @foreach($languages as $lang)
                                                  <option value="{{ $lang }}" {{ (isset($data['language']) && $data['language'] === $lang) ? 'selected' : '' }}>
                                                       {{ $lang }}
                                                  </option>
                                             @endforeach
                                        </select>
                                   </div>
                              </div>
                         </div>
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="country">Country</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2" id="country" name="country">
                                             <option value="">Select</option>
                                             @foreach($countries as $country)
                                                  <option value="{{ $country ?? '' }}" {{ (isset($data['country']) && $data['country'] === $country) ? 'selected' : '' }}>
                                                       {{ $country ?? '' }}
                                                  </option>
                                            @endforeach
                                        </select>
                                   </div>
                              </div>
                         </div>

                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="country_currency_symbol">Currency Symbol</label>
                                   <div class="form-control-wrap">
                                        <input 
                                             type="text" 
                                             class="form-control" 
                                             name="country_currency_symbol" 
                                             id="country_currency_symbol" 
                                             placeholder="Enter currency symbol"
                                             value="{{ $data['country_currency_symbol'] ?? '' }}"
                                        >
                                   </div>
                              </div>
                         </div>

                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="currency_separator">Currency Separator</label>
                                   <div class="form-control-wrap">
                                        <input type="text" 
                                             class="form-control" 
                                             name="currency_separator" 
                                             id="currency_separator" 
                                             placeholder="Enter Currency Separator"
                                             value="{{ $data['currency_separator'] ?? '' }}"
                                        >
                                   </div>
                              </div>
                         </div>

                         {{-- <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="minimum_requirements">Minimum Requirements</label>
                                   <textarea class="form-control" name="minimum_requirements" id="minimum_requirements">{{ $data['minimum_requirements'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="validation_rules">Validation Rules</label>
                                   <textarea class="form-control" name="validation_rules" id="validation_rules">{{ $data['validation_rules'] ?? '' }}</textarea>
                              </div>
                         </div> --}}
                    </div>
               </div>
               <div class="mt-3">
                    <button class="btn btn-primary" type="submit" id="saveSettingsBtn">Save</button>
               </div>
          </form>
     </div>
</div>

@endsection