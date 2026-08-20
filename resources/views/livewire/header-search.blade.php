<div class="{{ $class }}">
    <div class="dropdown_content">
        <div class="inside_dropdown_cont">
            <div class="header_drop_inpt">
                <div class="inside_text">
                    <input type="text" placeholder="{{$data['header_document_search_placeholder'] ?? 'Nombre del documento'}}" wire:model.live.debounce.500ms="search" >
                </div>
                <div class="drop_serach_btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
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
                        <span class="inner_search">{{$data['header_document_search_message'] ?? 'No se encontraron documentos.'}}</span>
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
