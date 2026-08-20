
{{--
<div class="tab_ul">
    <div class="tab_star_li">
        <span class="rating-on rate-1" data-rating="1"></span>
        <span class="rating-on rate-2" data-rating="2"></span>
        <span class="rating-on rate-3" data-rating="3"></span>
        <span class="rating-on rate-4" data-rating="4"></span>
        <span class="rating-on rate-5" data-rating="5"></span>
    </div>
    <div class="cont_rate">4.6</div> | --}}

    @props([
        'rating' => 0,
        'class' => '',
        'ratingClass' => '',
        'showDescription' => false,
        'showReviews' => collect(),
        'document' => null,
        'ratingText' => null,
        'reviewHaceText' => null,
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
        <div class="{{ $ratingClass }}">{{ number_format((float)$rating, 1) }}</div> |




        @if($showDescription)
        <div class="rating-description">
            {{ $ratingText ?? 'Basado en todos los documentos' }}
        </div>
    </div>
    @else
    <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#exampleModalCenter"
        onclick="event.preventDefault();">
        <li class="cont_li review_opinion">{{ $showReviews ? $showReviews->count() : 0 }} reviews</li>
    </a>
    <div class="modal fade review-modal-main" id="exampleModalCenter" tabindex="-1" aria-modal="true"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal_inner_bx">
            <div class="modal-content">
                <!-- Close Button -->
                <div class="close-btn-wrp">
                    <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>


                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="modal-hd-lft">
                        <h5 class="modal-title color-blue" id="exampleModalCenterTitle">{{ $document->title ?? '' }}
                        </h5>
                        @if ($showReviews->isNotEmpty())
                            <!-- Star Ratings -->
                            <div class="all_rating">
                                <ul class="star-rate-div d-flex p-0">
                                    <li class="drop_cont_li">
                                        <div class="select_ul">
                                            <div class="tab_ul {{ $class }}">
                                                <div class="tab_star_li">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="{{ $i <= $roundedRating ? 'rating-on' : 'rating-off' }} rate-{{ $i }}" data-rating="{{ $i }}"></span>
                                                    @endfor
                                                </div>
                                                <div class="{{ $ratingClass }}">{{ number_format((float)$rating, 1) }}</div>

                                    <span>|</span>
                                    <li class="opinion">{{ $showReviews ? $showReviews->count() : 0 }} opiniones</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-rgt">
                        <!-- Open Review Modal Button -->
                        <button type="button" class="btn ad_rvw">
                            Escribir una opinión
                        </button>

                    </div>
                </div>
                <div class="review">
                    <!-- Modal Body - User Reviews -->
                    <div class="modal-body">
                        {{-- <div class="re_view"> --}}
                            <div class="scroll-div" id="all_rev">
                                @if ($showReviews->isNotEmpty())
                                    <div class="user-review-wrp">
                                        <div class="user-review-hd">
                                            @php $count = 0; @endphp
                                            @foreach ($showReviews as $review)
                                                <div class="body-cmt-sec d-flex ">
                                                    <?php
                                                    if ($review->user_id == null) {
                                                        $first_name = $review->first_name;
                                                        $last_name = $review->last_name;
                                                        $initials = strtoupper(substr($first_name, 0, 1)) . strtoupper(substr($last_name, 0, 1));
                                                    } else {
                                                        $first_name = $review->user->first_name;
                                                        $last_name = $review->user->last_name;
                                                        $public_name = $review->user->public_name;
                                                        $initials = strtoupper(substr($review->user->public_name, 0, 1));
                                                    }
                                                    ?>
                                                    <span class="person-profile">{{ $initials ?? '' }}</span>
                                                    <div class="imtext">
                                                        <h4 class="color-blue m-0">
                                                            <h4>{{ $public_name ?? '' }}</h4>
                                                        </h4>
                                                        <p class="m-0 loaction"><i
                                                                class="fa-solid fa-location-dot"></i>{{ $review->city ?? '' }}
                                                        </p>

                                                        <!-- Star Ratings -->
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
                                                                <!-- {{ $reviewHaceText ?? 'Hace' }} -->
                                                                {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}   
                                                            </p>

                                                        </div>
                                                        <p class="comnt_cnt m-0">{{ $review->description ?? '' }}</p>
                                                    </div>
                                                </div>

                                                @php $count++ ; @endphp
                                            @endforeach
                                        </div>
                                        @if ($count >= 10)
                                            <div class="user-review-btm">
                                                <button class="view-more-cta d-flex"><i
                                                        class="fa-solid fa-chevron-down"></i> View
                                                    More</button>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    Comentarios no encontrados.
                                @endif
                            </div>
                        {{-- </div> --}}
                        <div class="form-scroll-wrap">
                            @if (auth()->check())
                                <div class="scroll-div">
                                    <div class="write-review-form">
                                        <div class="write-profile">
                                            <form class="review-frm" action="{{ route('add.review', $document->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="sec-wrap">
                                                    <div class="person-txt d-flex gp-12">
                                                        <?php
                                                            if (auth()->user()->public_name) {
                                                                
                                                                $initials1 = strtoupper(substr(auth()->user()->public_name, 0, 1));
                                                            } else {
                                                               
                                                                $initials1 = strtoupper(substr(auth()->user()->first_name, 0, 1)) . strtoupper(substr(auth()->user()->last_name, 0, 1));
                                                            }
                                                        ?>
                                                        <span class="person-profile">{{ $initials1 }}</span>
                                                        <div class="imtext">
                                                            <h4 class="color-blue d-flex edit-hd m-0">
                                                                <div class="rvw_username_div">
                                                                    @if (auth()->user()->public_name)                            
                                                                        <h4>{{ auth()->user()->public_name ?? '' }}</h4>
                                                                    @else 
                                                                        <h4>{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</h4>
                                                                        
                                                                    @endif
                                                                    <div class="edit append_name_fields">
                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                    </div>
                                                                </div>
                                                            </h4>
                                                            <div class="cmnt_loc_div">
                                                                <p class="m-0 loaction"><i
                                                                        class="fa-solid fa-location-dot"></i>{{ auth()->user()->city ?? 'Monterrey N.L' }}
                                                                </p>
                                                                <p class="comnt_cnt m-0">Se mostrará públicamente</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="model-section">
                                                        <div class="star_Av d-flex align-items-center">
                                                            <div class="ratings review-modalrating">
                                                                <label for="rating1">
                                                                    <i rate="1"
                                                                        class="star fa fa-star rating-color"></i>
                                                                </label>
                                                                <input type="checkbox" name="rating" id="rating1"
                                                                    class="chkbox" style="display:none;" value="1">
                                                                <label for="rating2">
                                                                    <i rate="2"
                                                                        class="star fa fa-star rating-color"></i>
                                                                </label>
                                                                <input type="checkbox" name="rating" id="rating2"
                                                                    class="chkbox" style="display:none;" value="2">
                                                                <label for="rating3">
                                                                    <i rate="3"
                                                                        class="star fa fa-star rating-color"></i>
                                                                </label>
                                                                <input type="checkbox" name="rating" id="rating3"
                                                                    class="chkbox" style="display:none;" value="3">
                                                                <label for="rating4">
                                                                    <i rate="4"
                                                                        class="star fa fa-star rating-color"></i>
                                                                </label>
                                                                <input type="checkbox" name="rating" id="rating4"
                                                                    class="chkbox" style="display:none;" value="4">
                                                                <label for="rating5">
                                                                    <i rate="5"
                                                                        class="star fa fa-star rating-color"></i>
                                                                </label>
                                                                <input type="checkbox" name="rating" id="rating5"
                                                                    class="chkbox" style="display:none;" value="5"
                                                                    checked>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="name_fields" style="display:none;">
                                                                <div class="col-lg-12">
                                                                    <div class="review-frm-inr">
                                                                        <input type="text"
                                                                            placeholder="Nombre público" id="public_name"
                                                                            name="public_name">
                                                                    </div>
                                                                </div>
                                                                <!--<div class="col-lg-6">
                                                                    <div class="review-frm-inr">
                                                                        <input type="text"
                                                                            placeholder="Apellido publicado"
                                                                            id="last_name" name="last_name">
                                                                    </div>
                                                                </div>-->
                                                            </div>
                                                            <!-- <div class="col-lg-12">
                                                                <div class="review-frm-inr">
                                                                    <input type="text" placeholder="Ciudad/Municipio"
                                                                        id="city" name="city">
                                                                </div>
                                                            </div> -->
                                                            <div class="col-lg-12">
                                                                <div class="review-frm-inr">
                                                                    <textarea rows="4" placeholder="Comparte tu opinión sobre este documento" id="description" name="description"
                                                                        required="true"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-btns d-flex justify-content-end gp-12">
                                                    <div class="cancel-btn">
                                                        <button class="cta-white" id="cancel_btn">Cancelar</button>

                                                    </div>
                                                    <div class="submit-btn">
                                                        <button class="cta-blue" type="submit">Publicar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span>Por favor <a href="{{ route('login.user', ['redirecturl' => url()->current()]) }}">ingresa</a>
                                    a tu cuenta para opinar sobre este documento.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif



