
    <div class="header_search_bar {{ $class ?? '' }}">
        <div class="search_bar">
            <div class="wrap" id="myID" style="display:none;">
                <div class="search">
                    {{-- <input type="text" class="searchTerm" --}}
                        {{-- placeholder="{{ $data['header_search_placeholder'] ?? '¿Qué documento estás buscando?' }}" --}}
                        {{-- placeholder="{{ 'Search documents…' }}"
                        wire:model.live.debounce.500ms="search"> --}}
                        <input type="text"
                        class="searchTerm"
                        placeholder="Search documents…"
                        wire:model.live.debounce.500ms="search"
                        wire:keydown.enter="goToResults">
                    {{-- <button class="btn cta_dark"><i class="fa-solid fa-magnifying-glass"></i></button> --}}
                    <button class="btn cta_dark" wire:click="goToResults">
                            <i class="fa-solid fa-magnifying-glass"></i>
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
    </div>
