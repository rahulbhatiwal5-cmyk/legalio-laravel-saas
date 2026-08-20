@foreach($reviews as $review)

<div class="body-cmt-sec d-flex">

    @php
        $first_name = $review->first_name ?? '';
        $last_name = $review->last_name ?? '';

        $initials =
            strtoupper(substr($first_name,0,1))
            . strtoupper(substr($last_name,0,1));
    @endphp

    <span class="person-profile">
        {{ $initials }}
    </span>

    <div class="imtext">

        <h4 class="color-blue m-0">
            {{ trim($first_name.' '.$last_name) }}
        </h4>

        <p class="m-0 loaction">
            <i class="fa-solid fa-location-dot"></i>
            {{ $review->city }}
        </p>

        <div class="star_Av">
            <div class="tab_ul cmt_star">
                <div class="tab_star_li">
                    <span class="rating-on rate-1"></span>
                    <span class="rating-on rate-2"></span>
                    <span class="rating-on rate-3"></span>
                    <span class="rating-on rate-4"></span>
                    <span class="rating-on rate-5"></span>
                </div>
            </div>

            <p class="ms-2">
                {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
            </p>
        </div>

        <p class="comnt_cnt m-0">
            {{ $review->description }}
        </p>

    </div>

</div>

@endforeach