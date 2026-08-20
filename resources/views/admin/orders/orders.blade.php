@extends('admin_layout.master')
@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="components-preview wide-md">

                        <div class="nk-block nk-block-lg">

                            <livewire:admin-order-filter/>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection



