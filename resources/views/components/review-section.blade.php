<div class="container">
    <style>
        .inner-new-btn-wrp {
            flex-direction: column;
        }

        .new-con_btn_div .cta_light_cont {
            padding: 12px 24px;
            font-size: 14px;
            margin-top: 15px;
        }

        .new-con_btn_div .cta_light_cont:hover {
            background-color: #012555;
            border: 1px solid #012555;
            color: #fff;
        }
    </style>

    <div class="row">
        <div class="col-md-4">
            <div class="clientes_data size20">
                <h2>{{ $data['reviews_heading'] ?? '' }}</h2>
                <p>{{ $data['reviews_sub_heading'] ?? '' }}</p>
            </div>
            <div class="btn-wrap inner-new-btn-wrp">
                <div class="btn-wrap">
                    <button class="prev-btn">
                        <img src="{{ asset('assets/img/Vector1.png') }}" alt="">
                    </button>
                    <button class="next-btn">
                        <img src="{{ asset('assets/img/Vector2.png') }}" alt="">
                    </button>
                </div>

                {{-- <div class="new-con_btn_div">
                    <a href="" class="cta_light_cont ">Create Document Now</a>
                </div> --}}
            </div>
        </div>
        <div class="col-md-8">
            <div class="client-slider slick-list">
                @if (isset($reviews) && $reviews != null)

                @foreach ($reviews as $review)
                <div class="control_box">
                    <div class="d-flex">
                        <div class="slider-img">

                            @if ($review->user && $review->user != null)
                            {{-- {{ dd($review->user ) }} --}}
                            @if ($review->user->file_path && $review->is_show)
                            <img src="{{ asset($review->user->file_path) }}" alt="User Image"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                            @else
                            <img src="{{ dimage() }}" alt="User Image"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                            @endif
                            @else
                            <img src="{{ dimage() }}" alt="User Image"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                            {{--
                                <?php // $initials = strtoupper(substr($review->first_name, 0, 1)) . strtoupper(substr($review->last_name, 0, 1)); 
                                ?>
                                <span>{{ $initials ?? '' }}</span>
                            --}}
                            @endif
                        </div>
                        @if ($review->type == 'custom')
                        <div class="txt_slider">
                            <h6>{{ $review->first_name ?? '' }} {{ $review->last_name ?? '' }}</h6>
                            <span>{{ $review->city ?? $review->user->addresses->first()->city ?? ''}}</span>
                        </div>
                        @elseif($review->type == 'user')
                        <div class="txt_slider">

                            @if ($review->user->public_name && $review->user->public_name != null)
                            <h6>{{ $review->user->public_name ?? '' }}</h6>
                            <span>{{ $review->city ?? $review->user->addresses->first()->city ?? '' }}</span>
                            @else
                            <h6>{{ $review->user->first_name ?? '' }}
                                {{ $review->user->last_name ?? '' }}
                            </h6>
                            <span>{{ $review->city ?? $review->user->addresses->first()->city ?? '' }}</span>
                            @endif

                        </div>
                        @elseif($review->type == 'custom' && isset($review->user) && $review->user)
                        <div class="txt_slider">
                            <h6>{{ $review->user->first_name ?? '' }} {{ $review->user->last_name ?? '' }}
                            </h6>
                            <span>{{ $review->user->city ?? '' }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="star_tme_cnt">
                        <div class="tab_star_li">
                            @if (!empty($review->rating))

                            @for ($i = 1; $i <= $review->rating; $i++)
                                <span class="rating-on rate-{{ $i }}" data-rating="{{ $i }}"></span>
                                @endfor
                                @endIf
                        </div>
                        <span>
                            <!-- {{ $review->created_at ? \Carbon\Carbon::parse($review->created_at)->diffForHumans() : '' }} -->
                            <!-- {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}    -->
                            {{ $review->created_at->locale('en')->diffForHumans() }}
                        </span>
                    </div>
                    <p>{{ $review->description ?? '' }}</p>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>