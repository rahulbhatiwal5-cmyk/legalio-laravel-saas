
    {{-- <div class="tab_ul {{ $class }}" >
        <div class="tab_star_li">
            <span class="rating-on rate-1" data-rating="1"></span>
            <span class="rating-on rate-2" data-rating="2"></span>
            <span class="rating-on rate-3" data-rating="3"></span>
            <span class="rating-on rate-4" data-rating="4"></span>
            <span class="rating-on rate-5" data-rating="5"></span>

        </div>
        <div class="{{ $rating }}">4.6</div>

    </div> --}}


    @props([
        'rating' => 0,
        'class' => '',
        'ratingClass' => '',
        'ratingText' => '',
        'showDescription' => false
    ])
    
    @php
        $roundedRating = round((float)$rating); // Ensure it's numeric
    @endphp
    
    <div class="tab_ul {{ $class }}">
        <div class="tab_star_li">
            @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $roundedRating ? 'rating-on' : 'rating-off' }} rate-{{ $i }}" data-rating="{{ $i }}"></span>
            @endfor
        </div>
        <div class="{{ $ratingClass }}">{{ number_format((float)$rating, 1) }}</div>
    </div>

    

    