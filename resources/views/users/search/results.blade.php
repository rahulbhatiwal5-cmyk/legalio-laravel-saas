@extends('users_layout.master')
<style>
.sear-result-page {
    padding: 122px 0 0 0 !important;
}
</style>
@section('content')

<section class="tab_sec_ot privacy-sec sear-result-page">
    <div class="container">
        <div class="row">
            <div class="heading_sec_tabs" style="text-align:center; padding: 50px 0 30px;">
                <h2 class="doc_h">
                    @if($query)
                        Search Results for &ldquo;{{ $query }}&rdquo;
                    @else
                        All Legal Documents 
                    @endif
                </h2>
                <p class="size18 light">
                    {{ $totalCount }} document{{ $totalCount !== 1 ? 's' : '' }} found
                    @if($query) for your search @endif
                </p>
            </div>
        </div>
    </div>
</section>

<section class="tab_sec_ot p_120 popul-docu-spc" style="padding-top: 0px;">
    <div class="container">
        @if($documents->isEmpty())
            <div class="row justify-content-center" style="padding: 60px 0;">
                <div class="col-md-6 text-center">
                    <i class="fa fa-search" style="font-size:48px; color:#ccc; margin-bottom:20px; display:block;"></i>
                    <h4>No documents found</h4>
                    @if($query)
                        {{-- <p class="text-muted">Try a different search term or browse all documents below.</p> --}}
                        <a href="{{ route('user.all__documents') }}" class="cta_org" style="display:inline-block; margin-top:15px;">
                            Browse All Documents
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="row categry-rw">
                @foreach($documents as $document)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="inside_box_b" style="height:100%;">
                            <div class="inside_box_tab" style="height:100%; display:flex; flex-direction:column;">
                                <a href="{{ route('get.document', ['slug' => $document->slug]) }}" class="contract_link">
                                    <div class="img_tab_sec">
                                            <img src="{{ $document->document_image }}">
                                    </div>
                                </a>
                                <div class="cont_tab_ot" style="flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                                    <a href="{{ route('get.document', ['slug' => $document->slug]) }}" class="contract_link">
                                        <div class="tab_text">
                                            <h5 class="size20">{{ $document->title }}</h5>
                                            @if(($avgRating = $document->getavgRating()) !== false)
                                                <x-rating-component :rating="$avgRating" />
                                            @else
                                                <x-rating-component :rating="5" />
                                            @endif
                                        </div>
                                    </a>
                                    <div class="tab_btn" style="margin-top:12px;">
                                        <a href="{{ route('get.document', ['slug' => $document->slug]) }}" class="cta_org" style="display:block; text-align:center;">
                                            Create
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
</section>
@endsection
