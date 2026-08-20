@extends('user_dashboard_layout.master')
@section('content')
<div class="user_info">
    <div class="uer_nm">
        <h2>
            {{-- Mi perfil --}}
            My Profile
        </h2>
    </div>
    <div class="scroll_div">
        <div class="user_pernl_info">
            <div class="img_cambair d-flex justify-content-start align-items-center">
                <div class="per_img rounded-circle position-relative">
                    <img class="finalUploadedImage" src="{{ optional(auth()->user())->profile_image ?? dimage() }}">
                    <div class="img_logo  rounded-circle position-absolute d-flex justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/img_logo.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
                <form id="imageUploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="imageInput" name="image" style="display: none;" accept="image/*">

                    <button type="button" class="unq_btn" id="uploadTrigger">
                        {{-- Cambiar foto de perfil --}}
                        Change Profile Photo
                    </button>
                </form>
            </div>
            <!-- Modal for cropping -->
            <div class="crop-modal-overlay" style="display:none !important;">
                <div class="crop-modal">

                    <div class="crop-modal-header">
                        <h3>Crop Your Profile Picture</h3>
                        <button type="button" class="modal-close-btn" id="cropCancel">×</button>
                    </div>

                    <div class="crop-container">
                        <img id="cropImage" src="" alt="Image to crop">
                    </div>

                    <div class="crop-modal-footer">
                        <div class="crop-instructions">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="2" />
                            </svg>
                            Drag to move, use corners to resize your image
                        </div>
                        <div class="crop-actions">
                            <button type="button" class="btn btn-cancel" id="cropCancelFooter">Cancel</button>
                            <button type="button" class="btn-save" id="cropConfirm">Crop & Upload</button>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Display Section -->
            <div class="pernl_data">
                <div class="data_take text-white d-flex justify-content-between align-items-center">
                    <span>
                        {{-- Datos personales --}}
                        Personal Information
                    </span>
                    <i id="editIcon" class="fa-solid fa-pencil" style="font-size:medium; cursor: pointer;"></i>
                </div>
            </div>

            <!-- User Info Section -->
            <div id="infoSection" class="p-3 mb-3 border rounded bg-light">
                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Nombre: --}}
                            First Name:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $user->first_name }}</span>
                </div>
                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Apellido: --}}
                            Last Name:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $user->last_name }}</span>
                </div>
                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Nombre público: --}}
                            Display Name:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $user->public_name }}</span>
                </div>
                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Empresa: --}}
                            Company:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $billingAddress->company ?? '' }}</span>
                </div>
                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Correo Electrónico: --}}
                            Email:
                        </b>
                    </p>
                    <span class="d-block">{{ $user->email }}</span>
                </div>
            </div>
            <div id="editFormSection" style="display: none;">
                <form id="profileForm" action="{{ route('user.profile.update', Auth::user()->id) }}" method="POST" class="body_form">
                    @csrf
                    @method('PUT')
                    <div class="info d-flex flex-wrap justify-content-center align-items-center">
                        <!-- First Name -->
                        <div class="input_fuild d-flex flex-column">
                            {{-- <label class="mt-0" for="Nombre">Nombre</label>
                                <input id="Nombre" type="text" placeholder="John" name="first_name"
                                    value="{{ old('first_name', $user->first_name) }}"> --}}

                            <x-google-input type="text" name="first_name" id="Nombre" label="Name" :value="old('first_name', $user->first_name ?? '')" />


                            <span class="text error-msg" id="fname-error" style="display:none;">El nombre es obligatorio.</span>
                        </div>
                        <!-- Last Name -->
                        <div class="input_fuild d-flex flex-column">
                            {{-- <label class="mt-0" for="Apellido">Apellido</label>
                                <input id="Apellido" type="text" placeholder="Doe" name="last_name"
                                    value="{{ old('last_name', $user->last_name) }}"> --}}

                            <x-google-input type="text" name="last_name" id="Apellido" label="Last name" :value="old('last_name', $user->last_name ?? '')" />

                            <span class="text error-msg" id="lname-error" style="display:none;">El apellido es obligatorio.</span>
                        </div>
                        <!-- Email -->
                        <div class="input_fuild input_fuild_full flex-column d-flex">
                            {{-- <label class="mt-0" for="electrónico">Correo electrónico</label>
                                <input type="text" id="electrónico" placeholder="loren123@gmail.com" name="email"
                                    value="{{ old('email', $user->email) }}"> --}}

                            <x-google-input type="text" name="email" id="electrónico" label="Email" :value="old('email', $user->email ?? '')" />


                            <span class="text error-msg" id="email-error" style="display:none;">El correo electrónico es obligatorio.</span>
                        </div>
                        <!-- RFC -->
                        <div class="input_fuild d-flex flex-column">
                            {{-- <label class="mt-0" for="Company">Empresa</label>
                                <input id="Company" type="text" name="company" placeholder="Empresa"
                                    value="{{ old('company', $billingAddress->company ?? '') }}"> --}}

                            <x-google-input type="text" name="company" id="Company" label="Company" :value="old('company', $billingAddress->company ?? '')" />


                            <span class="text error-msg" id="company-error" style="display:none;">La empresa es obligatoria.</span>
                        </div>
                        <!-- Public Name -->
                        <div class="input_fuild d-flex flex-column">

                            <x-google-input type="text" name="public_name" id="Nombre_público" label="Public name" :value="old('public_name', $user->public_name ?? '')" />


                            <span class="text error-msg" id="pname-error" style="display:none;">El nombre público es obligatorio.</span>
                        </div>
                        <div class="input_fuild flex-column d-flex">

                        </div>
                        <hr>
                        <div class="input_fuild input_fuild_full flex-column d-flex">

                        </div>



                        <div class="gr_btn text-end">
                            <button type="button" class="user_link unq_btn update_profile">Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <hr>
            <div class="pernl_data">
                <div class="data_take text-white d-flex justify-content-between align-items-center">
                    <span>
                        {{-- Domicilio --}}
                        Address
                    </span>
                    <i id="billingEditIcon" class="fa-solid fa-pencil" style="font-size:medium; cursor: pointer;"></i>
                </div>
            </div>

            <!-- Billing Address Display Section -->
            <div id="billingInfoSection" class="p-3 mb-3 border rounded bg-light">


                <!-- <p class="mb-2"><b>Other Company:</b></p>
                    <span class="d-block mb-2">{{ $billingAddress->company_2 ?? '' }}</span> -->
                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Dirección: --}}
                            Street Address:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $billingAddress->address ?? '' }}</span>
                </div>

                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Ciudad: --}}
                            City:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $billingAddress->city ?? '' }}</span>
                </div>

                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            Postal Code:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $billingAddress->postal_code ?? '' }}</span>
                </div>

                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- Estado: --}}
                            State:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $billingAddress->state ?? '' }}</span>
                </div>

                <div class="frm-row-txt">
                    <p class="mb-2">
                        <b>
                            {{-- País: --}}
                            Country:
                        </b>
                    </p>
                    <span class="d-block mb-2">{{ $billingAddress->country ?? '' }}</span>
                </div>
            </div>
            <!-- Hidden Form Section -->
            <div id="billingEditFormSection" style="display: none;">
                <form id="billingForm" action="{{ route('user.billing.update', Auth::user()->id) }}" method="POST" class="body_form">
                    @csrf
                    @method('PUT')
                    <div class="info d-flex flex-wrap justify-content-center align-items-center">
                        <!-- First Name -->

                        <!-- <div class="input_fuild d-flex flex-column">
                                <label class="mt-0" for="Other_Company">Other Company *</label>
                                <input id="Other_Company" type="text" name="company_2"
                                placeholder="Empresa" value="{{ old('company_2', $billingAddress->company_2 ?? '') }}">
                                <span class="text text-danger" id="company2-error" style="display:none;">La empresa es obligatoria.</span>
                            </div> -->
                        <!-- Email -->
                        <div class="input_fuild input_fuild_full flex-column d-flex">
                            {{-- <label class="mt-0" for="Address">Dirección</label>
                                <input type="text" id="Address" placeholder="Dirección" name="address"
                                    value="{{ old('address', $billingAddress->address ?? '') }}"> --}}

                            <x-google-input type="text" name="address" id="Address" label="Address" :value="old('address', $billingAddress->address ?? '')" />


                            <span class="text error-msg" id="address-error" style="display:none;">La dirección es obligatoria.</span>
                        </div>
                        <!-- RFC -->
                        <div class="input_fuild input_fuild_full flex-column d-flex">
                            {{-- <label for="city" class="City">Ciudad</label>
                                <input type="text" id="City" name="city" placeholder="Ciudad"
                                    value="{{ old('city', $billingAddress->city ?? '') }}"> --}}

                            <x-google-input type="text" name="city" id="City" label="City" :value="old('city', $billingAddress->city ?? '')" />


                            <span class="text error-msg" id="city-error" style="display:none;">La ciudad es obligatoria.</span>
                        </div>

                        <div class="input_fuild input_fuild_full flex-column d-flex">
                            {{-- <label for="postal_code" class="City">Código Postal</label>
                                <input type="text" id="postal_code" name="postal_code" placeholder="Código Postal"
                                    value="{{ old('postal_code', $billingAddress->postal_code ?? '') }}">
                            --}}

                            <x-google-input type="text" name="postal_code" id="postal_code" label="Zip code" :value="old('postal_code', $billingAddress->postal_code ?? '')" />

                            <span class="text error-msg" id="postal-error" style="display:none;">El estado es obligatorio.</span>
                        </div>
                        <div class="input_fuild input_fuild_full flex-column d-flex">
                            {{-- <label for="state" class="state">Estado</label>
                                <input type="text" id="state" name="state" placeholder="Estado"
                                    value="{{ old('state', $billingAddress->state ?? '') }}"> --}}

                            <x-google-input type="text" name="state" id="state" label="State" :value="old('state', $billingAddress->state ?? '')" />


                            <span class="text error-msg" id="state-error" style="display:none;">El código postal es obligatorio.</span>
                        </div>
                        <div class="input_fuild input_fuild_full flex-column d-flex">
                            {{-- <label for="country" class="country">País</label>
                                <select id="country" name="country" placeholder="País"> --}}
                            @php
                            $countries = [
                            "United States" => "United States",
                            "México" => "México",
                            "Argentina" => "Argentina",
                            "Colombia" => "Colombia",
                            "Chile" => "Chile",
                            "Perú" => "Perú",
                            "Ecuador" => "Ecuador",
                            "Venezuela" => "Venezuela",
                            "Bolivia" => "Bolivia",
                            "Paraguay" => "Paraguay",
                            "Uruguay" => "Uruguay",
                            "España" => "España"
                            ];
                            @endphp

                            {{-- <option value="" disabled selected>Select</option>
                                    @foreach($countries as $key => $value)
                                        <option value="{{ $key }}" {{ old('country', $billingAddress->country ?? '') == $key ? 'selected' : '' }}>
                            {{ $value }}
                            </option>
                            @endforeach --}}

                            <x-google-input type="select" name="country" id="country" label="Country" :options="$countries" :value="old('country', $billingAddress->country ?? 'United States')" />
                            {{-- </select> --}}
                            <span class="text error-msg" id="country-error" style="display:none;">El país es obligatorio.</span>
                        </div>

                        {{-- <hr> --}}

                        <div class="input_fuild input_fuild_full flex-column d-flex">

                        </div>
                        <div class="input_fuild input_fuild_full flex-column d-flex">

                        </div>
                        <div class="gr_btn">
                            <button type="button" class="user_link unq_btn update_billing">Update</button>
                        </div>
                    </div>
                </form>
            </div>


        </div>

        {{-- Delete account option  --}}
        <div class="eliminar_cuenta">
            <h2 class="m-0">
                {{-- Eliminar cuenta --}}
                Delete Account
            </h2>
            <p>
                Are you sure you want to delete your account? This action is irreversible, and all associated
                data will be permanently removed. Make sure to back up any important files before continuing.
            </p>
            <p class="elim_cun">
                {{-- <a href="{{ route('user.destroy', Auth::user()->id) }}" onclick="event.preventDefault(); document.getElementById('delete-form').submit();">
                    Delete Account
                </a> --}}
                <a href="#" id="deleteAccountBtn">
                    Delete Account  
                </a>
            </p>

            <form id="delete-form" action="{{ route('user.destroy', Auth::user()->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>


        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!--<script>
    $(document).ready(function() {
        // Open file selector when clicking button
        $('#uploadTrigger').click(function() {

            $('#imageInput').click();
        });

        // Handle image selection
        $('#imageInput').change(function() {
            var formData = new FormData();
            formData.append('image', $('#imageInput')[0].files[0]);

            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: "{{ url('user-dashboard/profile/upload-image') }}", // Ensure this route exists
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // console.log(response);

                    if (response.success) {
                        $('#uploadedImage').attr('src', response.image);
                        alert('Profile image updated successfully!');
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Error uploading image!');
                }
            });
        });
    });
</script>-->

<script>
    $(document).ready(function() {
        let cropper;
        let uploadedFile;

        // Open file selector when clicking button
        $('#uploadTrigger').click(function() {
            $('#imageInput').click();
        });

        // Handle image selection
        $('#imageInput').change(function() {
            if (this.files && this.files[0]) {
                uploadedFile = this.files[0];
                const url = URL.createObjectURL(uploadedFile);
                $('#cropImage').attr('src', url);

                // Show modal
                $('.crop-modal-overlay').css('display', 'flex');

                // Destroy previous cropper if exists
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(document.getElementById('cropImage'), {
                    viewMode: 1
                    , zoomable: true
                    , aspectRatio: 1, // square crop
                    movable: true
                    , scalable: true
                    , cropBoxResizable: true
                , });
            }
        });

        // Cancel cropping - both top and footer cancel buttons
        $('#cropCancel, #cropCancelFooter').click(function() {
            $('.crop-modal-overlay').hide();
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            $('#imageInput').val('');
        });

        // Confirm crop and upload
        $('#cropConfirm').click(function() {
            if (!cropper) return;

            cropper.getCroppedCanvas().toBlob(function(blob) {
                const formData = new FormData();
                formData.append('image', blob, uploadedFile.name);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // url: "{{ url('user-dashboard/profile/upload-image') }}", // Make sure this route is correct
                $.ajax({
                    url: "{{ url('account/profile/upload-image') }}", // Make sure this route is correct
                    type: "POST"
                    , data: formData
                    , contentType: false
                    , processData: false
                    , success: function(response) {
                        if (response.success) {
                            $('.finalUploadedImage').attr('src', response.image);
                            Swal.fire({
                                icon: 'success'
                                , title: 'Success!'
                                , text: 'Profile image updated successfully!'
                                , confirmButtonText: 'OK'
                            });
                            // alert('Profile image updated successfully!');
                            // window.location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error'
                                , title: 'Oops!'
                                , text: response.message
                                , confirmButtonText: 'OK'
                            });
                        }
                    }
                    , error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error'
                            , title: 'Oops!'
                            , text: 'Error uploading image!'
                            , confirmButtonText: 'OK'
                        });
                        //alert('Error uploading image!');
                    }
                    , complete: function() {
                        // Cleanup
                        $('.crop-modal-overlay').hide();
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                        $('#imageInput').val('');
                    }
                });
            }, uploadedFile.type || 'image/png');
        });
    });

</script>
<script>
    // document.addEventListener("DOMContentLoaded", function() {
    //     let editIcon = document.getElementById("editIcon");
    //     let infoSection = document.getElementById("infoSection");
    //     let editFormSection = document.getElementById("editFormSection");

    //     editIcon.addEventListener("click", function() {
    //         if (editFormSection.style.display === "none") {
    //             // Show form, hide text section
    //             editFormSection.style.display = "block";
    //             infoSection.style.display = "none";

    //             // Change edit icon to close icon
    //             editIcon.classList.remove("fa-pencil");
    //             editIcon.classList.add("fa-times"); // X icon
    //         } else {
    //             // Hide form, show text section
    //             editFormSection.style.display = "none";
    //             infoSection.style.display = "block";

    //             // Change back to pencil icon
    //             editIcon.classList.remove("fa-times");
    //             editIcon.classList.add("fa-pencil");
    //         }
    //     });
    // });
    document.addEventListener("DOMContentLoaded", function() {
    let editIcon = document.getElementById("editIcon");
    let infoSection = document.getElementById("infoSection");
    let editFormSection = document.getElementById("editFormSection");

    let billingInfoSection = document.getElementById("billingInfoSection");
    let billingEditFormSection = document.getElementById("billingEditFormSection");
    let billingEditIcon = document.getElementById("billingEditIcon");

    editIcon.addEventListener("click", function() {

        // CLOSE billing form if open
        billingEditFormSection.style.display = "none";
        billingInfoSection.style.display = "block";
        billingEditIcon.classList.remove("fa-times");
        billingEditIcon.classList.add("fa-pencil");

        if (editFormSection.style.display === "none") {
            editFormSection.style.display = "block";
            infoSection.style.display = "none";

            editIcon.classList.remove("fa-pencil");
            editIcon.classList.add("fa-times");
        } else {
            editFormSection.style.display = "none";
            infoSection.style.display = "block";

            editIcon.classList.remove("fa-times");
            editIcon.classList.add("fa-pencil");
        }
    });
});

</script>

<script>
    // $(document).ready(function() {
    //     $("#billingEditIcon").click(function() {
    //         let billingInfoSection = $("#billingInfoSection");
    //         let billingEditFormSection = $("#billingEditFormSection");

    //         if (billingEditFormSection.is(":hidden")) {
    //             billingEditFormSection.show();
    //             billingInfoSection.hide();
    //             $(this).removeClass("fa-pencil").addClass("fa-times");
    //         } else {
    //             billingEditFormSection.hide();
    //             billingInfoSection.show();
    //             $(this).removeClass("fa-times").addClass("fa-pencil");
    //         }
    //     });
    // });
    $(document).ready(function() {
    $("#billingEditIcon").click(function() {

        let billingInfoSection = $("#billingInfoSection");
        let billingEditFormSection = $("#billingEditFormSection");

        let profileInfoSection = $("#infoSection");
        let profileEditFormSection = $("#editFormSection");
        let profileEditIcon = $("#editIcon");

        // 👉 CLOSE profile form if open
        profileEditFormSection.hide();
        profileInfoSection.show();
        profileEditIcon.removeClass("fa-times").addClass("fa-pencil");

        if (billingEditFormSection.is(":hidden")) {
            billingEditFormSection.show();
            billingInfoSection.hide();
            $(this).removeClass("fa-pencil").addClass("fa-times");
        } else {
            billingEditFormSection.hide();
            billingInfoSection.show();
            $(this).removeClass("fa-times").addClass("fa-pencil");
        }
    });
});

</script>


<script>
    $(document).ready(function() {

        // validate billing address //
        $('.update_billing').click(function(e) {
            e.preventDefault();


            var other_company = $('#Other_Company').val();
            var address = $('#Address').val();
            var city = $('#City').val();
            var postal_code = $('#postal_code').val();
            var state = $('#state').val();
            var country = $('#country').val();
            var isValid = true;

            // if(!other_company){
            //    $('#company2-error').show();
            //    isValid = false;
            // }else {
            //    $('#company2-error').hide();
            // }

            $('#Address').on('input', function() {
                $(this).removeClass('invalid');
                $('#address-error').hide();
            });

            $('#City').on('input', function() {
                $(this).removeClass('invalid');
                $('#city-error').hide();
            });

            $('#postal_code').on('input', function() {
                $(this).removeClass('invalid');
                $('#postal-error').hide();
            });

            $('#state').on('input', function() {
                $(this).removeClass('invalid');
                $('#state-error').hide();
            });

            $('#country').on('input', function() {
                $(this).removeClass('invalid');
                $('#country-error').hide();
            });

            if (!address) {
                $('#address-error').show();
                $('#Address').addClass('invalid');
                isValid = false;
            } else {
                $('#address-error').hide();
                $('#Address').removeClass('invalid');
            }

            if (!city) {
                $('#city-error').show();
                $('#City').addClass('invalid');
                isValid = false;
            } else {
                $('#city-error').hide();
                $('#City').removeClass('invalid');
            }

            if (!postal_code) {
                $('#postal-error').show();
                $('#postal_code').addClass('invalid');
                isValid = false;
            } else {
                $('#postal-error').hide();
                $('#postal_code').removeClass('invalid');
            }

            if (!state) {
                $('#state-error').show();
                $('#state').addClass('invalid');
                isValid = false;
            } else {
                $('#state-error').hide();
                $('#state').removeClass('invalid');
            }

            if (!country) {
                $('#country-error').show();
                $('#country').addClass('invalid');
                isValid = false;
            } else {
                $('#country-error').hide();
                $('#country').removeClass('invalid');
            }

            if (isValid == true) {
                $('#billingForm').submit();
            }

        })


        // validate profile update

        $('.update_profile').click(function(e) {
            e.preventDefault();

            var first_name = $('#Nombre').val();
            var last_name = $('#Apellido').val();
            var company = $('#Company').val();
            var electrónico = $('#electrónico').val();
            var nombre_público = $('#Nombre_público').val();
            var isValid = true;


            $('#Nombre').on('input', function() {
                $(this).removeClass('invalid');
                $('#fname-error').hide();
            });

            $('#Apellido').on('input', function() {
                $(this).removeClass('invalid');
                $('#lname-error').hide();
            });

            $('#Company').on('input', function() {
                $(this).removeClass('invalid');
                $('#company-error').hide();
            });

            $('#electrónico').on('input', function() {
                $(this).removeClass('invalid');
                $('#email-error').hide();
            });

            $('#Nombre_público').on('input', function() {
                $(this).removeClass('invalid');
                $('#pname-error').hide();
            });

            if (!first_name) {
                $('#fname-error').show();
                $('#Nombre').addClass('invalid');
                isValid = false;
            } else {
                $('#fname-error').hide();
                $('#Nombre').removeClass('invalid');
            }

            if (!last_name) {
                $('#lname-error').show();
                $('#Apellido').addClass('invalid');
                isValid = false;
            } else {
                $('#lname-error').hide();
                $('#Apellido').removeClass('invalid');
            }

            if (!company) {
                $('#company-error').show();
                $('#Company').addClass('invalid');
                isValid = false;
            } else {
                $('#company-error').hide();
                $('#Company').removeClass('invalid');
            }

            if (!electrónico) {
                $('#email-error').show();
                $('#electrónico').addClass('invalid');
                isValid = false;
            } else {
                $('#email-error').hide();
                $('#electrónico').removeClass('invalid');
            }

            if (!nombre_público) {
                $('#pname-error').show();
                $('#Nombre_público').addClass('invalid');
                isValid = false;
            } else {
                $('#pname-error').hide();
            }

            if (isValid == true) {
                $('#profileForm').submit();
            }


        })
    })

</script>

<script>
$(document).ready(function() {

    $('#deleteAccountBtn').on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Your account will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });

    });

});
</script>