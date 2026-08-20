@extends('admin_layout.master')
@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="components-preview wide-md mx-auto">
                        <div class="nk-block nk-block-lg">
                            <div class="nk-block-head">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">Document Prompts</h4>
                                </div>
                            </div>
                            @livewire('admin-attach-document-prompt')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


