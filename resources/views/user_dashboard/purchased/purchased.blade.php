@extends('user_dashboard_layout.master')
@section('content')
    <div class="uer_nm">
        {{-- <h1>Comprados</h1> --}}
        <h1>My Documents</h1>
    </div>
    <div class="scroll_div">
        <div class="crt_main">
            @php $has_credits = $has_credits; @endphp

            @if($purchased_documents->isNotEmpty())
                @foreach($purchased_documents as $data)
                    @php 
                        $document = $data['document']; 
                        $order_date = $data['order_date'];
                        $order_id = $data['order_id'];
                        $has_subscription = $data['has_subscription'];
                        $edit_count = $data['edit_count'] ?? 0; 
                        $creditsLeft = $data['credits'] ?? 0;
                    @endphp
                    
                    <div class="cart_dv">
                        <div class="crt_lft">
                            <div class="cart_img">
                                @php
                                    $image_path = $document->document_image;
                                @endphp

                                @if(!empty($image_path))
                                    <img src="{{ $image_path }}" class="img-fluid">
                                @endif

                            </div>
                            <div class="cart_text">
                                <h4>{{ $document->title ?? 'Carta de Recomendación Personal' }}</h4>
                                <p><?php $short = Str::limit($document->short_description, 100, '...'); 
                                    print_r($short);
                                ?></p>
                            </div>
                        </div>
                        <div class="crt_ryt">
                            <div class="datt_text">
                                <p class="dt_text">Última edición</p>
                                <p>{{ $order_date ?? '12/03/2024' }}</p>
                            </div>
                            <div class="edt_lnk">
                                <span><a href="{{ route('user.generate_pdf',['id' => $document->id, 'order_id' => $order_id]) }}"><img src="{{ asset('assets/img/dwnd_img.png') }}" class="img-fluid"></a></span>
                            </div>
                            <div class="shr_dt dot">
                                <span class="elps_icn"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                                <div class="dropdown-menu_review">
                                    <div class="user_name">
                                        <p class="text-center">Administrar</p>
                                    </div>
                                    
                                    <div class="dropdown-main">
                                        <div class="dash-icon">
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#comprarModal{{ $document->id ?? '' }}{{ $order_id ?? '' }}"><i class="fa-solid fa-pen"></i>Editar</a>
                                        </div>
                                        <div class="dash-icon">
                                            <a class="dropdown-item" href="{{ route('user.generate_pdf',['id' => $document->id, 'order_id' => $order_id]) }}"><i class="fa-solid fa-download"></i>Download</a>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade editar-modal" id="comprarModal{{ $document->id ?? '' }}{{ $order_id ?? '' }}" tabindex="-1" aria-labelledby="savedDocumentLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="savedDocumentLabel"></h5>
                                    <div class="close-btn-wrp">
                                        <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="dropdown-main">
                                        <div class="dash-icon">
                                            <a class="dropdown-item" href="{{ route('edit.contracts', ['id' => $document->id, 'order_id' => $order_id]) }}">Text Editor</a>
                                        </div>
                                       
                                        <!-- <div class="dash-icon">
                                            <a class="dropdown-item" href="{{ route('user.attempt_contract_questions', ['slug' => $document->slug,'order_id' => $order_id,'type' => 'edit']) }}">Limited Contract Edit</a>
                                        </div> -->

                                        @if(!$has_subscription && !$has_credits)
                                        <div class="dash-icon">
                                            <a class="dropdown-item" 
                                            href="{{ route('user.attempt_contract_questions', [
                                                'slug' => $document->slug,
                                                'order_id' => $order_id,
                                                'type' => 'edit',
                                                'edit_count' => $edit_count
                                            ]) }}">
                                            Limited Contract Edit
                                            </a>
                                        </div>
                                        @endif
                                       <!--                                       
                                        <div class="dash-icon">
                                            @if($has_subscription && $has_credits)
                                            <a class="dropdown-item" href="{{ route('user.attempt_contract_questions', ['slug' => $document->slug,'type' => 'full', 'has_subscription' => $has_subscription, 'has_credits' => $has_credits, 'order_id' => $order_id]) }}">Full Contract Edit (Paid)</a>
                                            @else
                                            <a class="dropdown-item" href="{{ route('user.attempt_contract_questions', ['slug' => $document->slug,'type' => 'full']) }}">Full Contract Edit (Paid)</a>
                                            @endif
                                        </div> -->

                                        <div class="dash-icon">
                                            @if($creditsLeft > 0)
                                                @if($has_subscription && $has_credits)
                                                    <a class="dropdown-item"
                                                    href="{{ route('user.attempt_contract_questions', [
                                                        'slug' => $document->slug,
                                                        'type' => 'full',
                                                        'has_subscription' => $has_subscription,
                                                        'has_credits' => $has_credits,
                                                        'order_id' => $order_id
                                                    ]) }}">
                                                    Full Contract Edit (Paid)
                                                    </a>
                                                @else
                                                    <a class="dropdown-item"
                                                    href="{{ route('user.attempt_contract_questions', [
                                                        'slug' => $document->slug,
                                                        'type' => 'full'
                                                    ]) }}">
                                                    Full Contract Edit (Paid)
                                                    </a>
                                                @endif
                                            @else
                                                <a class="dropdown-item disabled text-muted" href="javascript:void(0)">
                                                    Full Contract Edit (Paid)
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                No purchased documents found.
            @endif

        </div>
    </div>

<script>
    
    // for three dots //

    // $(document).ready(function () {
    //     $(document).on("click", ".elps_icn", function (event) {
    //         event.stopPropagation(); // Prevents click event from bubbling up
            
    //         // Close all other dropdowns
    //         $(".dropdown-menu_review").not($(this).next()).removeClass("show");
            
    //         // Toggle only the corresponding dropdown
    //         $(this).next(".dropdown-menu_review").toggleClass("show");
    //     });

    //     // Close the dropdown if clicking outside
    //     $(document).on("click", function (event) {
    //         if (!$(event.target).closest(".dropdown-menu_review, .elps_icn").length) {
    //             $(".dropdown-menu_review").removeClass("show");
    //         }
    //     });
    // });


    $(document).ready(function () {
        $(document).on("click", ".elps_icn", function (event) {
            event.stopPropagation();

            $(".dropdown-menu_review").not($(this).next()).removeClass("show");

            $(this).next(".dropdown-menu_review").toggleClass("show");
        });

        $(document).on("click", function (event) {
            if (!$(event.target).closest(".dropdown-menu_review, .elps_icn").length) {
                $(".dropdown-menu_review").removeClass("show");
            }
        });

        $(document).on("mouseleave", ".shr_dt", function () {
            $(this).find(".dropdown-menu_review").removeClass("show");
        });
    });

   

</script>

<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.open-edit-modal')) {
            const btn = e.target.closest('.open-edit-modal');
            document.getElementById('modal_document_id').value = btn.dataset.documentId;
            document.getElementById('modal_order_id').value = btn.dataset.orderId;
            document.getElementById('modal_edit_count').value = btn.dataset.editCount;
        }
    });
</script>



@endsection