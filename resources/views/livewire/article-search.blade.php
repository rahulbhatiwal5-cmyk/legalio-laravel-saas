<div class="{{ $class ?? '' }}">
    <div class="search_bar">

        <div class="wrap">
            <div class="search">
                <!-- Automatically calls searchDocuments() when the input changes -->
                <span class="search_text"><i class="fa fa-search"></i></span>
                <input type="text" class="searchTerm" placeholder="Search documents…"
                    wire:model.live.debounce.500ms="search">

                <button type="submit" class="searchButton">
                    <i wire:loading.remove class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
    </div>
    @if(!empty(trim($search)))
    <div class="search-results-dd">
        @if($articles->isEmpty())
            <ul class="google_search">
                <li>
                    <span class="iconContainer d-flex align-items-center">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        <span class="inner_search">No se encontraron artículos.</span>
                    </span>
                </li>
            </ul>
        @else
            <ul class="google_search">
                @foreach($articles as $article)
                    <li>
                        <span class="iconContainer d-flex align-items-center">
                            <i class="fa fa-search me-2"></i>
                            <a class="inner_search" href="{{ route('knowledgebase.article', $article->slug) }}">
                                {{ $article->title }}
                            </a>
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

</div>
