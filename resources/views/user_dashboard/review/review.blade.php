@extends('user_dashboard_layout.master')

@section('content')

    <div class="d-flex justify-content-between align-items-center">
        <h1>
            {{-- Mis reseñas --}}
            My Reviews
        </h1>
        <a href="#" class="unq_btn" data-bs-toggle="modal" data-bs-target="#addReviewModal">
            {{-- Escribir una opinión --}}
            Write a Review
        </a>
    </div>
    <div class="scroll_div">
        <div class="cus_review">
            @if ($reviews->isNotEmpty())
                @foreach ($reviews as $data)
                    <div class="part_review">
                        <div class="part_1">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-7 col-es-7">
                                    <div class="inner_1part">
                                        <div class="revies_persn gap-3 d-flex align-items-center justify-content-start">
                                            <div class="user_img_reciew rounded-circle">
                                                @if (isset($data->media->file_name) && $data->media->file_name != null)
                                                    <img src="{{ asset('storage/' . $data->media->file_name) }}"
                                                        alt="">
                                                @else
                                                    @if ($data->type == 'user')
                                                        <?php $initials = strtoupper(substr($data->user->first_name, 0, 1)) . strtoupper(substr($data->user->last_name, 0, 1)); ?>
                                                    @elseif($data->type == 'custom')
                                                        <?php $initials = strtoupper(substr($data->first_name, 0, 1)) . strtoupper(substr($data->last_name, 0, 1)); ?>
                                                    @elseif($data->type == 'custom' && isset($data->user) && $data->user)
                                                        <?php $initials = strtoupper(substr($data->user->first_name, 0, 1)) . strtoupper(substr($data->user->last_name, 0, 1)); ?>
                                                    @endif
                                                    <span><b>{{ $initials ?? '' }}</b></span>
                                                @endif
                                            </div>
                                            <div class="name_star">
                                                <p>{{ $data->document->title ?? 'Document Name Not Available' }}</p>
                                                <div class="start_name">
                                                    @php
                                                        $rating = $data->rating ?? 0; // Use 0 if rating is null
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <div class="star">
                                                            @if ($i <= $rating)
                                                                <img src="{{ asset('assets/img/Star 2.png') }}"
                                                                    alt="Star">
                                                            @endif
                                                        </div>
                                                    @endfor
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-5 col-es-5 text-end">
                                    <p class="time_re">
                                        {{ $data->created_at ? $data->created_at->diffForHumans(now(), ['parts' => 1]) : '1 month ago' }}

                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="part_2">
                            <div class="row">
                                <div class="col-xl-11 col-lg-10 col-md-10 col-sm-10">
                                    <div class="part2_child-1">
                                        <p>{{ $data->description ?? 'Lorem Ipsum es simplemente un texto de relleno.' }}</p>
                                    </div>
                                </div>
                                <div class="col-xl-1 col-lg-2 col-md-2 col-sm-2">
                                    <div class="part2_child-2">
                                        <div class="shr_dt dot">
                                            <span class="elps_icn"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                                            <div class="dropdown-menu_review">
                                                <div class="user_name">
                                                    <p class="text-center">Manage Review</p>
                                                </div>
                                                <div class="dropdown-main">
                                                    <div class="dash-icon">
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#editReviewModal_{{ $data->id }}">
                                                            <i class="fa-solid fa-edit"></i>Edit Review
                                                        </a>
                                                    </div>

                                                    <div class="dash-icon">
                                                        <a class="dropdown-item"
                                                            href="{{ route('reviews.destroy', $data->id) }}"
                                                            onclick="return confirm('Are you sure you want to delete this review?')">
                                                            <i class="fa-solid fa-trash"></i> Delete Review
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p>No reviews found.</p>
            @endif
        </div>


        <div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addReviewModalLabel">
                            @if ($documents->isEmpty())
                            @elseif($documents->count() === 1)
                                {{ $documents->first()->title ?? '' }}
                            @else
                                Escribir una opinión
                            @endif
                        </h5>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                        <div class="close-btn-wrp">
                            <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                    <!-- <div class="modal-body">
                            <div class="write-review-form">
                                <div class="user-info d-flex align-items-center">
                                    <div class="user-avatar">
                                        <span class="person-profile">{{ strtoupper(substr($user->first_name, 0, 1)) . strtoupper(substr($user->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name">
                                            <h4>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }} <i class="fa-solid fa-pen-to-square append_name_fields"></i></h4>
                                        </div>
                                        <div class="user-location">
                                            <p class="m-0 location"><i class="fa-solid fa-location-dot"></i> {{ $user->addresses->first()->city ?? '' }}</p>
                                            <p class="public-note">Se mostrará públicamente</p>
                                        </div>
                                    </div>
                                </div>
                                <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST">
                                    @csrf

                                    {{-- Document selection --}}
                                    <div class="document-selection mt-3 mb-3">
                                        <select name="document_id" id="document" class="form-control">
                                            @foreach ($documents as $document)
    <option value="{{ $document->id }}" {{ old('document_id') == $document->id ? 'selected' : '' }}>
                                                    {{ $document->title }}
                                                </option>
    @endforeach
                                        </select>
                                        @error('document_id')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
                                    </div>

                                    {{-- Star rating --}}
                                    <div class="rating-container">
                                        <div class="star-rating">
                                            <i class="fa fa-star" data-rating="1"></i>
                                            <i class="fa fa-star" data-rating="2"></i>
                                            <i class="fa fa-star" data-rating="3"></i>
                                            <i class="fa fa-star" data-rating="4"></i>
                                            <i class="fa fa-star" data-rating="5"></i>
                                            <input type="hidden" name="rating" id="rating-value" value="{{ old('rating', 5) }}">
                                        </div>
                                        @error('rating')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
                                    </div>

                                    {{-- Optional name fields --}}
                                    <div class="name_fields d-none">
                                        <div class="row mt-3">
                                            <div class="col-lg-6">
                                                <div class="review-frm-inr">
                                                    <input type="text" placeholder="Nombre publicado" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}">
                                                    @error('first_name')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="review-frm-inr">
                                                    <input type="text" placeholder="Apellido publicado" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}">
                                                    @error('last_name')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- City and description --}}
                                    <div class="form-fields mt-3">
                                        <input type="text" name="city" id="city" class="form-control" placeholder="Ciudad/Municipio" value="{{ old('city') }}">
                                        @error('city')
        <span class="text-danger small">{{ $message }}</span>
    @enderror

                                        <textarea name="description" id="description" rows="4" class="form-control mt-3"
                                            placeholder="Comparte tu opinión sobre este documento">{{ old('description') }}</textarea>
                                        @error('description')
        <span class="text-danger small">{{ $message }}</span>
    @enderror
                                    </div>

                                    <div class="form-buttons d-flex justify-content-end mt-3">
                                        <button type="button" class="btn-cancel" id="cancel-btn">Cancelar</button>
                                        <button type="submit" class="btn-publish" id="publish-btn">Publicar</button>
                                    </div>
                                </form>
                            </div>
                        </div> -->

                    <div class="modal-body">
                        @if ($documents->isEmpty())
                            @if ($user->orders()->where('status', 1)->exists())
                                <div class="text-center">
                                    Ya has publicado todas las opiniones posibles para tus documentos.
                                </div>
                            @else
                                <div class="text-center">
                                    Para poder escribir una opinión, primero debes haber comprado un documento.
                                </div>
                            @endif
                        @elseif($documents->count() === 1)
                            <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="document_id" value="{{ $documents->first()->id }}">
                                <div class="rating-container">
                                    <div class="star-rating">
                                        <i class="fa fa-star" data-rating="1"></i>
                                        <i class="fa fa-star" data-rating="2"></i>
                                        <i class="fa fa-star" data-rating="3"></i>
                                        <i class="fa fa-star" data-rating="4"></i>
                                        <i class="fa fa-star" data-rating="5"></i>
                                        <input type="hidden" name="rating" id="rating-value"
                                            value="{{ old('rating', 5) }}">
                                    </div>
                                    @error('rating')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="name_fields d-none">
                                    <div class="row mt-3">
                                        <div class="col-lg-6">
                                            <div class="review-frm-inr">
                                                <input type="text" placeholder="Nombre publicado" id="first_name"
                                                    name="first_name" class="form-control"
                                                    value="{{ old('first_name') }}">
                                                @error('first_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="review-frm-inr">
                                                <input type="text" placeholder="Apellido publicado" id="last_name"
                                                    name="last_name" class="form-control"
                                                    value="{{ old('last_name') }}">
                                                @error('last_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-fields mt-3">
                                    <input type="text" name="city" id="city" class="form-control"
                                        placeholder="Ciudad/Municipio" value="{{ old('city') }}">
                                    @error('city')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror

                                    <textarea name="description" id="description" rows="4" class="form-control mt-3"
                                        placeholder="Comparte tu opinión sobre este documento">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-buttons d-flex justify-content-end mt-3">
                                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn-publish">Publicar</button>
                                </div>

                            </form>
                        @else
                            <h6 class="mb-3">Selecciona un documento</h6>
                            <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST">
                                @csrf

                                <div class="document-selection mb-3">
                                    <select name="document_id" id="document" class="form-control">
                                        @foreach ($documents as $document)
                                            <option value="{{ $document->id }}"
                                                {{ old('document_id') == $document->id ? 'selected' : '' }}>
                                                {{ $document->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('document_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="rating-container">
                                    <div class="star-rating">
                                        <i class="fa fa-star" data-rating="1"></i>
                                        <i class="fa fa-star" data-rating="2"></i>
                                        <i class="fa fa-star" data-rating="3"></i>
                                        <i class="fa fa-star" data-rating="4"></i>
                                        <i class="fa fa-star" data-rating="5"></i>
                                        <input type="hidden" name="rating" id="rating-value"
                                            value="{{ old('rating', 5) }}">
                                    </div>
                                    @error('rating')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="name_fields d-none">
                                    <div class="row mt-3">
                                        <div class="col-lg-6">
                                            <div class="review-frm-inr">
                                                <input type="text" placeholder="Nombre publicado" id="first_name"
                                                    name="first_name" class="form-control"
                                                    value="{{ old('first_name') }}">
                                                @error('first_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="review-frm-inr">
                                                <input type="text" placeholder="Apellido publicado" id="last_name"
                                                    name="last_name" class="form-control"
                                                    value="{{ old('last_name') }}">
                                                @error('last_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-fields mt-3">
                                    <input type="text" name="city" id="city" class="form-control"
                                        placeholder="Ciudad/Municipio" value="{{ old('city') }}">
                                    @error('city')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror

                                    <textarea name="description" id="description" rows="4" class="form-control mt-3"
                                        placeholder="Comparte tu opinión sobre este documento">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-buttons d-flex justify-content-end mt-3">
                                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn-publish">Publicar</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>





        @foreach ($reviews as $data)
            <!-- Modal specific to this review -->
            <div class="modal fade" id="editReviewModal_{{ $data->id }}" tabindex="-1"
                aria-labelledby="editReviewModalLabel_{{ $data->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="editReviewModalLabel_{{ $data->id }}">Editar opinión</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                            <div class="write-review-form">
                                <form id="editReviewForm_{{ $data->id }}"
                                    action="{{ route('reviews.update', $data->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="review_id" value="{{ $data->id }}">

                                    <!-- User Info -->
                                    <div class="user-info d-flex align-items-center">
                                        <div class="user-avatar-profile">
                                            <span
                                                class="person-profile">{{ strtoupper(substr($data->user->first_name, 0, 1)) . strtoupper(substr($data->user->last_name, 0, 1)) }}</span>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">
                                                <h4>{{ $data->user->first_name }} {{ $data->user->last_name }}</h4>
                                            </div>
                                            <div class="user-location">
                                                <p class="m-0 location"><i class="fa-solid fa-location-dot"></i>
                                                    {{ $data->user->addresses->first()->city ?? '' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Document dropdown -->
                                    @php
                                        $documentTitle =
                                            \App\Models\Document::find($data->document_id)?->title ?? 'N/A';
                                    @endphp
                                    <input type="text" class="form-control mt-3" value="{{ $documentTitle }}"
                                        readonly>
                                    <input type="hidden" name="document_id" value="{{ $data->document_id }}">
                                    @error('document_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror

                                    <!-- Star Rating -->
                                    <div class="rating-container">
                                        <div class="star-rating edit-stars-{{ $data->id }}"
                                            data-id="{{ $data->id }}">
                                            <i class="fa fa-star {{ $data->rating >= 1 ? 'active' : '' }}"
                                                data-rating="1"></i>
                                            <i class="fa fa-star {{ $data->rating >= 2 ? 'active' : '' }}"
                                                data-rating="2"></i>
                                            <i class="fa fa-star {{ $data->rating >= 3 ? 'active' : '' }}"
                                                data-rating="3"></i>
                                            <i class="fa fa-star {{ $data->rating >= 4 ? 'active' : '' }}"
                                                data-rating="4"></i>
                                            <i class="fa fa-star {{ $data->rating >= 5 ? 'active' : '' }}"
                                                data-rating="5"></i>
                                            <input type="hidden" name="rating" id="rating-value-{{ $data->id }}"
                                                value="{{ old('rating', $data->rating) }}">
                                        </div>
                                        @error('rating')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- City -->
                                    <input type="text" name="city" class="form-control mt-3"
                                        value="{{ old('city', $data->city) }}">
                                    @error('city')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror

                                    <!-- Description -->
                                    <textarea name="description" rows="4" class="form-control mt-3">{{ old('description', $data->description) }}</textarea>
                                    @error('description')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror

                                    <div class="form-buttons d-flex justify-content-end mt-3">
                                        <button type="button" class="btn-cancel"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn-publish">Actualizar</button>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <script>
        $(document).ready(function() {
            // Star rating functionality for edit modal
            $(document).on('click', '.star-rating i', function() {
                var rating = $(this).data('rating');
                var reviewId = $(this).closest('.star-rating').data('id'); // Get review ID dynamically

                // Update the hidden rating input value
                $('#rating-value-' + reviewId).val(rating);

                // Update stars visually
                $(this).closest('.star-rating').find('i').each(function() {
                    if ($(this).data('rating') <= rating) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-review-btn', function() {
                var reviewId = $(this).data('id');

                // Fetch review data using AJAX
                $.ajax({
                    url: '/reviews/' + reviewId + '/edit',
                    type: 'GET',
                    success: function(response) {
                        // Fill the form with existing data
                        $('#edit_review_id').val(response.id);
                        $('#edit_document').val(response.document_id);
                        $('#edit_document_id').val(response.document_id);
                        $('#edit_city').val(response.city);
                        $('#edit_description').val(response.description);

                        // Set rating stars dynamically for edit modal
                        var rating = response.rating;
                        $('#rating-value-' + reviewId).val(rating);
                        $('.star-rating[data-id="' + reviewId + '"] i').each(function() {
                            if ($(this).data('rating') <= rating) {
                                $(this).addClass('active');
                            } else {
                                $(this).removeClass('active');
                            }
                        });

                        // Open the modal
                        $('#editReviewModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Error fetching review data:', error);
                        alert('Error al cargar los datos de la opinión');
                    }
                });
            });

            // Toggle name fields in edit form
            $('#editReviewModal .append_name_fields').on('click', function() {
                $('#editReviewModal .name_fields').toggleClass('d-none');
            });
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-rating i');
            const ratingInput = document.getElementById('rating-value');

            function setInitialRating() {
                const defaultRating = 5;
                ratingInput.value = defaultRating;
                stars.forEach(star => {
                    if (parseInt(star.getAttribute('data-rating')) <= defaultRating) {
                        star.classList.add('active');
                    }
                });
            }

            setInitialRating();

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;

                    stars.forEach(s => {
                        s.classList.toggle('active', parseInt(s.getAttribute(
                            'data-rating')) <= rating);
                    });
                });

                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    stars.forEach(s => {
                        s.classList.toggle('hover', parseInt(s.getAttribute(
                            'data-rating')) <= rating);
                    });
                });
            });

            const ratingContainer = document.querySelector('.star-rating');
            ratingContainer.addEventListener('mouseleave', function() {
                stars.forEach(star => star.classList.remove('hover'));
            });

            // Cancel button logic
            const cancelBtn = document.getElementById('cancel-btn');
            cancelBtn?.addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addReviewModal'));
                modal?.hide();
            });

            // Publish button logic
            const publishBtn = document.getElementById('publish-btn');
            publishBtn?.addEventListener('click', function() {
                const documentId = document.getElementById('document').value;
                const rating = document.getElementById('rating-value').value;
                const city = document.getElementById('city').value;
                const description = document.getElementById('description').value;

                console.log({
                    documentId,
                    rating,
                    city,
                    description
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById('addReviewModal'));
                modal?.hide();
            });
        });
    </script>


    <script>
        // $(document).ready(function () {
        //     // Toggle dropdown on three dots icon
        //     $(document).on("click", ".elps_icn", function (event) {
        //         event.stopPropagation();
        //         $(".dropdown-menu_review").not($(this).next()).removeClass("show");
        //         $(this).next(".dropdown-menu_review").toggleClass("show");
        //     });

        //     // Close dropdown if clicking outside
        //     $(document).on("click", function (event) {
        //         if (!$(event.target).closest(".dropdown-menu_review, .elps_icn").length) {
        //             $(".dropdown-menu_review").removeClass("show");
        //         }
        //     });

        //     // ✅ Toggle name fields on edit icon click
        //     $(document).on("click", ".append_name_fields", function () {
        //         $(".name_fields").toggleClass("d-none");
        //     });
        // });

        $(document).ready(function() {
            $(document).on("click", ".elps_icn", function(event) {
                event.stopPropagation();
                $(".dropdown-menu_review").not($(this).next()).removeClass("show");
                $(this).next(".dropdown-menu_review").toggleClass("show");
            });

            $(document).on("click", function(event) {
                if (!$(event.target).closest(".dropdown-menu_review, .elps_icn").length) {
                    $(".dropdown-menu_review").removeClass("show");
                }
            });

            $(document).on("click", ".append_name_fields", function() {
                $(".name_fields").toggleClass("d-none");
            });

            $(document).on("mouseleave", ".shr_dt", function() {
                $(this).find(".dropdown-menu_review").removeClass("show");
            });
        });
    </script>






@endsection
