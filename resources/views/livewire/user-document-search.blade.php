<div class="header_search_bar">
    <div class="">
<div class="form">
    {{-- <input type="search" class="search-box" placeholder="Buscar documento legal"  wire:model.live.debounce.500ms="search"> --}}
    <input type="search" class="search-box" placeholder="Search documents…"  wire:model.live.debounce.500ms="search">
    <button class="btn cta_dark active"><i class="fa-solid fa-magnifying-glass"></i></button>
</div>
    </div>
@if(!empty($search))
<div class="search-results-dd">
    <ul class="google_search">
        @foreach($documents as $document)
        <li>
            <span class="iconContainer">  <a href="{{ route('get.document', ['slug' => $document->slug]) }}"><i class="fa fa-search"></i><span> {{ $document->title }} </span></a></span>
        </li>
        @endforeach
    </ul>
</div>
@endif
</div>
