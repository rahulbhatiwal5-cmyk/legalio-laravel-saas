{{-- <div class="{{ $class ?? '' }}">
    <div class="search_bar">

        <div class="wrap">
            <div class="search">
                <span class="search_text"><i class="fa fa-search"></i></span>
                <input type="text" class="searchTerm" placeholder="Search documents…" wire:model.live.debounce.500ms="search">

                <button type="submit" class="searchButton">
                    <span wire:loading.remove>Get started</span>
                </button>
            </div>
        </div>
    </div>
    @if(!empty(trim($search)))
    <div class="search-results-dd">
        @if($documents->isEmpty())
            <ul class="google_search">
                <li>
                    <span class="iconContainer d-flex align-items-center">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        <span class="inner_search">No se encontraron documentos.</span>
                    </span>
                </li>
            </ul>
        @else
            <ul class="google_search">
                @foreach($documents as $document)
                    <li>
                        <span class="iconContainer d-flex align-items-center">
                            <i class="fa fa-search me-2"></i>
                            <a class="inner_search" href="{{ route('get.document', ['slug' => $document->slug]) }}">
                                {{ $document->title }}
                            </a>
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

</div> --}}

<div class="{{ $class ?? '' }}">
    <div class="search_bar">
        <div class="wrap">
            <div class="search">
                <span class="search_text"><i class="fa fa-search"></i></span>
                <input
                    type="text"
                    class="searchTerm"
                    placeholder="{{ $data['header_document_search_placeholder'] ?? 'Search documents…' }}"
                    wire:model.live.debounce.500ms="search"
                    wire:keydown.enter="goToResults"
                >
                <button type="button" class="searchButton" wire:click="goToResults">
                    <span wire:loading.remove wire:target="goToResults">Get started</span>
                    <span wire:loading wire:target="goToResults">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>

    @if(!empty(trim($search)))
        <div class="search-results-dd">
            @if($documents->isEmpty())
                <ul class="google_search">
                    <li>
                        <span class="iconContainer d-flex align-items-center">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            <span class="inner_search">No documents found.</span>
                        </span>
                    </li>
                </ul>
            @else
                <ul class="google_search">
                    @foreach($documents as $document)
                        <li>
                            <span class="iconContainer d-flex align-items-center">
                                <i class="fa fa-search me-2"></i>
                                <a class="inner_search" href="{{ route('get.document', ['slug' => $document->slug]) }}">
                                    {{ $document->title }}
                                </a>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>