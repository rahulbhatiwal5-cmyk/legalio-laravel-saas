@extends('admin_layout.master')
@section('content')

<div class="nk-content">

    <div class="container-fluid">
        <div class="row main_section_div">
            <div class="col col-md-8 doc-left-content">
                <div class="doc-top-butns2 mt-0">
                    <div class="form-group d-flex justify-content-between">
                        <b><h4>Edit Order<span class="required_field">*</span></h4></b>
                    </div>
                </div>
                <div class="card card-bordered card-preview mt-4">
                    <div class="card-inner">

                        <div class="col-md-12 doc-title">
                        <div class="form-group">
                            <label class="form-label" for="title">
                                <b><h3>Order Detail <span class="required">#{{$order->order_num ?? '' }}</span></h3></b>
                            </label>

                            @if($payment->type == "stripe")
                                <p>Payment via Credit Card
                                    <span class="card_id">({{ $paymentIntent->payment_method ?? 'No record found' }})</span>.
                                </br>
                                    Created on <span class="date">{{$formattedDate ?? '' }}</span> @ <span class="time">{{$order->created_at->format('H:i')}}</span>.
                                </br>
                                    Paid on <span class="date">{{$formattedDate ?? '' }}</span> @ <span class="time">{{$order->transactions->created_at->format('H:i')}}</span>.
                                </p>
                            @elseif($payment->type == "paypal")
                                <p>Payment via PayPal
                                    <span class="card_id">({{ $paymentMethod ?? 'No record found' }})</span>.
                                </br>
                                    Created on <span class="date">{{$formattedDate ?? '' }}</span> @ <span class="time">{{$order->created_at->format('H:i')}}</span>.
                                </br>
                                    Paid on <span class="date">{{$formattedDate ?? '' }}</span> @ <span class="time">{{$order->transactions->created_at->format('H:i')}}</span>.
                                </p>
                            @else
                                <p>Payment Method:
                                    <span class="card_id">No record found</span>.
                                    Created on <span class="date">{{$formattedDate ?? '' }}</span> @ <span class="time">{{$order->created_at->format('H:i')}}</span>.
                                    Paid on <span class="date">{{$formattedDate ?? '' }}</span> @ <span class="time">{{$order->transactions->created_at->format('H:i')}}</span>.
                                </p>
                            @endif
                        </div>

                        </div>
                        {{-- @endif --}}
                        <div class="col-md-12 mt-2">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">

                                <h5 class="mt-2">Customer Information  <em class="icon ni ni-edit-alt" type="button" id="edit_btn_2"></em>
                                    <em class="icon ni ni-cross" type="button" id="cross_btn_2" style="display: none;"></em></h5>

                                <div class="customer_detail mt-1 mb-1">
                                    <b>First Name:</b>
                                    <span>{{$order->user->first_name ?? ''}} {{$order->user->last_name ?? ''}}</span><br>
                                    <b>Last Name:</b>
                                    <span>{{$order->user->last_name ?? ''}}</span><br>
                                    <b>Email:</b>
                                    <span>{{$order->user->email ?? ''}}</span>

                                </div>
                                <div id="customer_detail_hide"  class= "form-row customer-hide  mt-1" style="display:none">
                                    <form action="{{ route('update.customer.details', $order->order_num) }}" id="userDetails" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{-- <label for="first_name">First Name</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{old('first_name',$order->user->first_name ?? '')}}"> --}}
                                                <x-google-input
                                                type="text"
                                                name="first_name"
                                                id="first_name"
                                                label="First Name"
                                                :value="$order->user?->first_name  ?? ''"
                                            />

                                            </div>
                                            <div class="form-group col-md-6">
                                                {{-- <label for="last_name">Last Name</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{old('last_name',$order->user->last_name ?? '')}}">
                                            --}}
                                                <x-google-input
                                                type="text"
                                                name="last_name"
                                                id="last_name"
                                                label="Last Name"
                                                :value="$order->user?->last_name  ?? ''"
                                            />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{-- <label for="email">Correo electronico</label>
                                                <input type="text" class="form-control" id="email" name="email" placeholder="Apartment" value="{{old('email',$order->user->email ?? '')}}"> --}}
                                                <x-google-input
                                                type="text"
                                                name="email"
                                                id="email"
                                                label="Email"
                                                :value="$order->user?->email  ?? ''"
                                            />
                                            </div>
                                            <div class="form-group col-md-6">
                                                {{-- <label for="phone">Phone</label>
                                                <input type="number" class="form-control" id="phone" name="phone" placeholder="+12231323"> --}}
                                                <x-google-input
                                                type="text"
                                                name="phone"
                                                id="phone"
                                                label="Phone"

                                            />
                                            </div>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <div class="up-btn mbsc-form-group">
                                                <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                                {{-- <Select class="form-control " style="appearance: auto;">
                                    <option value="1">{{$order->user->first_name ?? '' }}.({{$order->user->email ?? '' }})</option>
                                </Select>
                                <br>
                                <b > Date Created:</b>
                                <!-- Date Input -->
                                    <input type="date" name="created_at_date" id="created_at_date" class="form-control mt-1"
                                    value="{{ date('Y-m-d', strtotime($order->created_at ?? '' )) }}" readonly>

                                <!-- Time Input -->
                                    <input type="time" name="created_at_time" id="created_at_time" class="form-control mt-1"
                                    value="{{ date('H:i', strtotime($order->created_at ?? '' )) }}" readonly> --}}

                                </div>
                                <div class="col-sm">

                                <div class="d-flex justify-content-between">
                                    <h5 class="mt-2">Billing Information<em class="icon ni ni-edit-alt" type="button" id="edit_btn"></em>
                                        <em class="icon ni ni-cross" type="button" id="cross_btn" style="display: none;"></em></h5>


                                </div>
                                <div id="billing_detail" class="billing_detail mt-1" >

                                    <b >Company:</b>
                                <span>{{$userAddress->company ?? ''}}</span><br>


                                <!-- <b>Other Company:</b>

                                <span>{{$userAddress->company_2 ?? ''}}</span><br> -->



                                <b>Address:</b>

                                <span>{{$userAddress->address ?? ''}}</span><br>


                                <b>City:</b>

                                <span>{{$userAddress->city ?? ''}}</span><br>

                                <b>ZIP:</b>


                                <span>{{$userAddress->postal_code ?? ''}}</span><br>

                                <b>State:</b>

                                <span>{{$userAddress->state ?? ''}}</span><br>

                                <b>Country:</b>


                                <span>{{$userAddress->country ?? ''}}</span><br>

                                </div>

                                <div id="billing_hide"  class= "form-row billing-hide  mt-1" style="display:none">
                                    <form action="{{ route('update.billing.details', $order->order_num) }}" id="billingDetails" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group">
                                            {{-- <label for="company">Company:</label>
                                            <input type="text" class="form-control" id="company" name="company" placeholder="#1234" value="{{old('company',$userAddress->company ?? '')}}"> --}}
                                            <x-google-input
                                            type="text"
                                            name="company"
                                            id="company"
                                            label="Company"
                                            :value="$userAddress->company  ?? ''"
                                            />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                {{-- <label for="address">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" value="{{old('address',$userAddress->address ?? '')}}"> --}}
                                                <x-google-input
                                                    type="text"
                                                    name="address"
                                                    id="address"
                                                    label="Address"
                                                    :value="$userAddress->address ?? '' "
                                                />
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{-- <label for="city">City</label>
                                                <input type="text" class="form-control" id="city" name="city" placeholder="Apartment" value="{{old('city',$userAddress->city ?? '')}}"> --}}
                                                <x-google-input
                                                type="text"
                                                name="city"
                                                id="city"
                                                label="City"
                                                :value="$userAddress->city ?? '' "
                                            />
                                            </div>
                                            <div class="form-group col-md-6">
                                                {{-- <label for="postal_code">PostCode/Zip</label>
                                                <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="231323" value="{{old('postal_code',$userAddress->postal_code ?? '')}}"> --}}
                                                <x-google-input
                                                    type="text"
                                                    name="postal_code"
                                                    id="postal_code"
                                                    label="PostCode/Zip"
                                                    :value="$userAddress->postal_code ?? '' "
                                                />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{-- <label for="country">Country/Region</label> --}}
                                                {{-- @php
                                                $countries = [
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

                                                <select class="form-control" id="country" name="country" style="appearance: auto;">
                                                    @foreach($countries as $key => $value)
                                                        <option value="{{ $key }}" {{ ($userAddress->country ?? '') == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select> --}}

                                                @php

                                                    $defaultCountry = old('country') ?? $userAddress->country  ?? 'México';

                                                    $countries = [
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

                                                <x-google-input
                                                    type="select"
                                                    name="country"
                                                    id="country"
                                                    label="Country/Region"
                                                    :options="$countries"
                                                    :value="$defaultCountry"
                                                />
                                            </div>
                                            <div class="form-group col-md-6">
                                                {{-- <label for="state">State</label>
                                                <input type="text" class="form-control" id="state" name="state" placeholder="231323" value="{{old('state',$userAddress->state ?? '')}}"> --}}
                                                <x-google-input
                                                type="text"
                                                name="state"
                                                id="state"
                                                label="State"
                                                :value="$userAddress->state ?? '' "
                                            />
                                            </div>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <div class="up-btn mbsc-form-group">
                                                <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($order->order_type == 'one_time')
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mt-4">Receipt</h5>
                    <a href="javascript:void(0)" class="btn btn-lg btn-white show-invoice-modal" data-order-id="{{ $order->id }}">
                        <em class="icon ni ni-printer-fill"></em>
                    </a>

                    <!-- Invoice Modal -->
                    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="invoiceModalTitle">Invoice Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="invoice-container">
                                        <!-- Placeholder while loading -->
                                        <div class="d-flex justify-content-center my-5">
                                            <div class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                    <button type="button" class="btn btn-primary print-modal-invoice" disabled>
                                        <em class="icon ni ni-printer-fill"></em> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden div for printing -->
                    <div id="printArea" style="display: none;"></div>
                </div>
                @endif

                    {{-- <div class="col-md-12 mt-2">
                        <div class="form-group">

                        <h5 class="mt-2">Invoice No.</h5>
                        <b>Invoice No.</b><span >{{$order->order_num}}</span>
                        <br>

                        <div id="moreDetails"  style="display: none">
                                <b><p>Invoice Display Date:</p></b><span class="invoice_date">{{$formattedDate ?? '' }}</span>

                            </div>


                            <b><a href="" id="toggleDetails">View more details</a></b>
                            <p><span>Notes (Printed in the invoice)</span></p>

                        </div>
                    </div> --}}
                    {{-- <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label class="form-label" >Create PDF</label>
                            <div class="mb-2">
                                <a href="javascript:void(0)" class="form-control">PDF-invoice</a>
                            </div>
                            <div class="mb-2">
                                <a href="javascript:void(0)" class="form-control">PDF-Packing slip</a>
                            </div>
                        </div>
                    </div> --}}

                    <hr>
                    @if($order->order_type == 'one_time')
                    <div class="col-md-12 mt-2">
                        <div class="invoice-bills">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="w-150px">Image</th>
                                            <th class="w-60"></th>
                                            <th>Price</th>
                                            <th></th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>
                                                <?php
                                                    $image_path = getStorageFilepath($order->document->document_image ?? '');
                                                    $quantity = 1;
                                                    $doc_price = $order->document->doc_price ?? 0;
                                                    $default_price = web_setting('default_document_price');
       
                                                    if($doc_price){
                                                        $price = $doc_price;
                                                    }else{
                                                        $price = $default_price->value ?? 199;
                                                    }

                                                    $amount = $price * $quantity;
                                                ?>
                                                <img src="{{ $image_path }}" alt="">
                                            </td>
                                            {{-- <td>{{$order->document->id ?? '' }}</td> --}}
                                            <td></td>
                                            <td>${{ number_format($price, 2) }}</td>
                                            {{-- <td>{{$quantity ?? '' }}</td> --}}
                                            <td></td>
                                            <td>${{ number_format($amount, 2) }}</td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="2">Subtotal</td>
                                            <td>${{$amount ?? '' }}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td colspan="2"></td>
                                            <td colspan="2">Processing fee</td>
                                            <td>$00.00</td>
                                        </tr> --}}
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="2">TAX</td>
                                            <td>$00.00</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="2">Grand Total</td>
                                            <td>${{$amount ?? '' }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                {{-- <button class="" type="button">Refund</button> --}}
                                {{-- <div class="nk-notes ff-italic fs-12px text-soft"> Invoice was created on a computer and is valid without the signature and seal. </div> --}}

                                <hr>
                            </div>
                        </div>
                    </div>
                    @elseif($order->order_type == 'subscription')
                    <div class="col-md-12 mt-2">
                        <h5>Subscription Details</h5>
                        <div class="subscription-bills">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <!-- <th>Plan</th> -->
                                            <th>Credit</th>
                                            <th>Interval</th>
                                            <th>Start Date</th>
                                            <th>Next Invoice Date</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <?php
                                                $quantity = 1;
                                                $amount = $order->amount;

                                                $interval = $subscription?->plan?->interval;

                                                $start_date = Carbon\Carbon::parse($subscription?->current_period_start_date);
                                                $startDate = $start_date->translatedFormat('F d, Y');

                                                $end_date = Carbon\Carbon::parse($subscription?->current_period_end_date);
                                                $endDate = $end_date->translatedFormat('F d, Y');
                                                $credit = web_setting('fair_use_document_limit')->value;
                                            ?>
                                            <!-- <td>{{ $subscription?->plan_id ?? '' }}</td> -->

                                            <td>{{ $credit ?? '' }}</td>
                                            <td>{{ $interval ?? ''}}</td>
                                            <td>{{ $startDate ?? '' }}</td>
                                            <td>{{ $endDate ?? '' }}</td>
                                            <td>{{ $subscription?->status ?? '' }}</td>
                                            <td>${{ number_format($amount, 2) }}/@if($interval == 'monthly')month @elseif($interval == 'yearly')year @endif</td>
                                        </tr>

                                    </tbody>

                                </table>
                                <hr>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col col-md-4 doc-right-content">
            <form action="{{route('update.orders.details',$order->order_num)}}" id="orderDetails" method="POST" >
                @csrf
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="col-md-12">
                            @php
                            $statuses = [0 => 'Incomplete', 1 => 'Succeeded', 2 => 'Refunded'];
                            @endphp

                            <label class="form-label" for="status">Status:</label>
                            <select name="status" class="form-control" id="status" style="appearance: auto;">
                                @foreach ($statuses as $key => $status)
                                    <option value="{{ $key }}" {{ old('status', $order->status) == $status ? 'selected' : '' }}>
                                        {{ $status ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text text-danger" id="status_error"></span>
                        </div>
                    </div>   
                </div>  
                <div class="card card-bordered card-preview mt-4">
                    <div class="card-inner">  
                        <div class="col-md-12 mt-2">
                            @if(!$freeGrant)
                                <label class="form-label" for="">Grant Free:</label> 
                                <div class="form-control-wrap">
                                    <select class="form-select grant_free" name="grant_free" id="grant_free">
                                        <option value="">Select</option>
                                        <option value="document">Document</option>
                                        <option value="subscription">Subscription</option>
                                    </select>
                                </div>  
                            @else
                                <div class="form-control-plaintext">
                                    <label class="form-label" for="">Free Granted:</label> {{ $freeGrant->grant_type === 'document' ? 'Document' : 'Subscription' }}
                                </div>
                            @endif       
                        </div>
                        <div id="granted_info" class="mt-3"></div>
                        <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="documentModalLabel">Grant Free Document</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label" for="free_document">Select Document:</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select js-select2 free_document" multiple name="free_document[]" id="free_document">
                                                @if(isset($documents) && $documents->isNotEmpty())
                                                    @if(count($documents) == count($grantedDocIds))
                                                        
                                                    @else
                                                        <option value="all">All</option>
                                                    @endif

                                                    @foreach($documents as $document)
                                                        @php

                                                            $isSelected = in_array($document->id, $grantedDocIds ?? [])
                                                            || ($order->document?->id == $document->id && empty($grantedDocIds));
                                                        @endphp

                                                        <option value="{{ $document->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                            {{ $document->title }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <label class="mt-2">Duration:</label>
                                        <input type="number" class="form-control" id="document_days" value="{{ $free_grant ?? 90 }}" readonly>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="saveDocumentGrant">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="subscriptionModalLabel">Grant Free Subscription</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label>Select Subscription:</label>
                                    <select class="form-select" id="free_subscription">
                                        @foreach($plans as $plan)
                                            @if($plan->number_of_months == 1)
                                            <option value="{{ $plan->number_of_months ?? '' }}" data-id="{{ $plan->id ?? '' }}" data-price="{{ $plan->price ?? '' }}" selected>
                                                {{ $plan->number_of_months }}
                                            </option>
                                            @else
                                            <option value="{{ $plan->number_of_months ?? '' }}" data-id="{{ $plan->id ?? '' }}" data-price="{{ $plan->price ?? '' }}">
                                                {{ $plan->number_of_months }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="saveSubscriptionGrant">Save</button>
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-md-12 mt-2">
                            <label class="form-label" for="free_grant_access">Grant Free Document:</label>
                            <div class="form-control-wrap">
                                <select class="form-select js-select2 free_grant_access" multiple name="free_grant_access[]" id="free_grant_access">
                                    @if(isset($documents) && $documents->isNotEmpty())
                                        @if(count($documents) == count($grantedDocIds))
                                            
                                        @else
                                            <option value="all">All</option>
                                        @endif

                                        @foreach($documents as $document)
                                            @php

                                                $isSelected = in_array($document->id, $grantedDocIds ?? [])
                                                || ($order->document?->id == $document->id && empty($grantedDocIds));
                                            @endphp

                                            <option value="{{ $document->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $document->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <span class="text text-danger" id="free_grant_error"></span>
                        </div> -->
                        <!-- <div class="col-md-12 mt-2">
                            <label class="form-label" for="grant_Free_Subscription">Grant Free Subscription:</label>
                            <div class="form-control-wrap">
                                <select class="form-select grant_Free_Subscription" name="grant_Free_Subscription" id="grant_Free_Subscription">
                                    @if(isset($plans) && $plans->isNotEmpty())
                                        @foreach($plans as $plan)
                                            @if($plan_id == $plan->id)
                                            <option value="{{ $plan->id }}" selected>
                                                {{ $plan->credit ?? '0' }} Credits
                                            </option>
                                            @else
                                            <option value="{{ $plan->id }}">
                                                {{ $plan->credit ?? '0' }} Credits
                                            </option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">No plans available</option>
                                    @endif
                                </select>
                            </div>
                            <span class="text text-danger" id="free_sub_error"></span>
                        </div> -->
                        <div class="d-flex justify-content-between mt-2">
                            {{-- <a href="">Move to Trash</a> --}}
                            <button class="btn btn-primary" type="submit" id="updateOrderDetails">Update</button>
                        </div>
                    </div>
                </div>
                <div class="card card-bordered card-preview mt-4">
                    <div class="card-inner">
                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                <label class="form-label" for="meta_title">Send Email</label>
                                <select class="form-control" style="appearance: auto;">
                                    <!-- <option value="1">New Order</option>
                                    <option value="2">Processing Order</option> -->
                                    <option value="1">Order Details</option>
                                    <option value="2">Invoice</option>
                                </select>
                                <button class="btn btn-primary mt-2" type="button">Send email</button>
                            </div>
                        </div>
                    
                        <hr>
                        <div class="col-md-12 mt-2">
                            <!-- <div class="form-group dwnload-btn">
                                <label class="form-label" >Download Contract</label>

                                <br>

                                <form action="{{ route('download.PDF', ['id' => $order->id]) }}" method="GET">
                                    <button type="submit" class="btn btn-secondary" style="background-color:#364a63; border:none">Download PDF</button>
                                </form>

                                <br>

                                <form action="{{ route('download.docx', ['id' => $order->id]) }}" method="GET">
                                    <button type="submit" class="btn btn-secondary" style="background-color:#364a63; border:none">Download DOCX</button>
                                </form>
                                <br>
                                <form action="{{ route('download.pages', ['id' => $order->id]) }}" method="GET" id="downloadPagesForm" target="downloadIframe">

                                </form>


                                <button type="button"
                                class="btn btn-secondary"
                                style="background-color:#364a63; border:none"
                                onclick="startDownloadWithLoader()">Download PAGES</button>

                            </div> -->
                                    <div class="btn-group dwn_contract_btn">
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <a type="logo" class="">Download Document</a>
                                        </button>
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow-btn" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                        <div class="dropdown-menu download-options">
                                            <a href="{{ route('download.PDF', ['id' => $order->id]) }}" class="dropdown-item" target="_blank">Download .pdf</a>
                                            <a href="{{ route('download.docx', ['id' => $order->id]) }}" class="dropdown-item" target="_blank">Download .docx</a>
                                            <a class="dropdown-item" onclick="startDownloadWithLoader()">Download .pages</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview mt-4">
                            <div class="card-inner">
                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="meta_title">Customer History</label>
                                        <div class="col-md-12 mb-1">
                                            <div class="form-group">
                                                <b>Customer Profile:</b> <span><a href="{{route('show.orders',$order->user->id ?? '' )}}" onmouseover="this.style.color='#FD5602'" onmouseout="this.style.color='#012555'" style="color:#012555;font-weight:bold;text-decoration:underline;">View Other order</a></span>
                                            </div>
                                        </div>
                                        {{-- <Select class="form-control"> --}}
                                        <div class="form-control">
                                            <b>Total Order:</b><span>{{ $totalOrder }}</span><br>
                                            <b>Total Revenue:</b><span>${{number_format($totalRevenue, 2) }}</span><br>
                                            <b>Average Order Value:</b><span>${{number_format($averageOrderValue, 2)}}</span>

                                        </div>

                                        {{-- </Select> --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('grantType');

        select.addEventListener('change', function () {
            const selected = this.value;
            const options = this.querySelectorAll('option');

            options.forEach(option => {
                if (option.value !== selected && option.value !== '') {
                    option.disabled = selected !== '';
                } else {
                    option.disabled = false;
                }
            });
        });
        select.dispatchEvent(new Event('change'));
    });
</script>


<script>

    $('#updateOrderDetails').click(function(e){
        e.preventDefault();
        var months = $('#months').val();
        let hasError = false;

        if(months === ''){
            $('#month_error').text('Please enter months');
            hasError = true;
        }else{
            console.log('fdlojgkidfkgjdkgjfk');
        }

        $('#months').on('input', function() {
            $('#month_error').remove();
        })

        if(!hasError){
            $('#orderDetails').submit();
        }

    })   

</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let editBtn = document.getElementById("edit_btn");
        let crossBtn = document.getElementById("cross_btn");
        let billingDetail = document.getElementById("billing_detail");
        let billingHide = document.getElementById("billing_hide");

        function toggleBillingSection() {
            let isEditing = billingDetail.style.display === "none";

            billingDetail.style.display = isEditing ? "block" : "none";
            billingHide.style.display = isEditing ? "none" : "block";

            // Toggle icons
            editBtn.style.display = isEditing ? "inline-block" : "none";
            crossBtn.style.display = isEditing ? "none" : "inline-block";
        }

        editBtn.addEventListener("click", toggleBillingSection);
        crossBtn.addEventListener("click", toggleBillingSection);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let editCustomerBtn = document.getElementById("edit_btn_2");
        let crossCustomerBtn = document.getElementById("cross_btn_2");
        let customerDetail = document.querySelector(".customer_detail");
        let customerDetailHide = document.getElementById("customer_detail_hide");

        function toggleCustomerSection() {
            let isEditing = customerDetail.style.display === "none";

            customerDetail.style.display = isEditing ? "block" : "none";
            customerDetailHide.style.display = isEditing ? "none" : "block";

            // Toggle icons
            editCustomerBtn.style.display = isEditing ? "inline-block" : "none";
            crossCustomerBtn.style.display = isEditing ? "none" : "inline-block";
        }

        editCustomerBtn.addEventListener("click", toggleCustomerSection);
        crossCustomerBtn.addEventListener("click", toggleCustomerSection);
    });
</script>




{{-- <script>
    function startDownloadWithLoader() {
        let loader = Swal.fire({
            title: "Preparing your download...",
            text: "Please wait while we generate your file.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // When iframe "loads", download has begun
        const iframe = document.querySelector('iframe[name="downloadIframe"]');

        const onDownloadStart = () => {
            Swal.close(); // Close the loader
            iframe.removeEventListener('load', onDownloadStart);
        };

        iframe.addEventListener('load', onDownloadStart);

        // Submit form to trigger download in iframe
        document.getElementById('downloadPagesForm').submit();
    }
</script> --}}

<script>
 function startDownloadWithLoader() {
    // Display the loading alert
    Swal.fire({
        title: "Preparing your download...",
        text: "Please wait while we generate your file.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("{{ route('download.pages', ['id' => $order->id]) }}")
        .then(response => {
            const disposition = response.headers.get('Content-Disposition');
            let fileName = 'generated-file.pages'; // fallback

            // Try to extract filename from Content-Disposition header
            if (disposition && disposition.includes('filename=')) {
                const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match && match[1]) {
                    fileName = match[1].replace(/['"]/g, '');
                }
            }

            return response.blob().then(blob => ({ blob, fileName }));
        })
        .then(({ blob, fileName }) => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = fileName; // ← dynamically set filename
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.close();
        })
        .catch(error => {
            console.error('Error downloading file:', error);
            Swal.close();
            alert('There was an error generating the file.');
        });
}

</script>

<script>
    $(document).ready(function(){
        $('#dropdownMenuButton').click(function(){
            $('.download-options').toggle();
        })

    })
</script>

<script>
    let currentOrderId = null;

    $(document).ready(function () {
        // Open modal and load invoice content
        $('.show-invoice-modal').on('click', function () {
            currentOrderId = $(this).data('order-id');

            $('#invoiceModal').modal('show');

            $('.invoice-container').html(`
                <div class="d-flex justify-content-center my-5">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);

            $('.print-modal-invoice').prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.order.invoice.content', ['id' => ':id']) }}".replace(':id', currentOrderId),
                type: 'GET',
                success: function (response) {
                    $('.invoice-container').html(response);
                    $('.print-modal-invoice').prop('disabled', false);
                },
                error: function () {
                    $('.invoice-container').html('<div class="alert alert-danger">Failed to load invoice</div>');
                    $('.print-modal-invoice').prop('disabled', true);
                }
            });
        });

        // Print invoice (PDF download)
        $('.print-modal-invoice').on('click', function () {
            if (!currentOrderId) {
                alert('Order ID not found.');
                return;
            }

            const url = `/admin/orders/${currentOrderId}/download-invoice`;
            window.open(url, '_blank'); // Trigger Laravel PDF response
        });

        $('#grant_Free_Subscription').on('change', function () {
            $('.months_toggle').show();
        });

    });

 

</script>

<script>
    $(document).ready(function() {
        // Open relevant popup
        $('#grant_free').on('change', function() {
            const val = $(this).val();
            if (val === 'document') {
                $('#documentModal').modal('show');
            } else if (val === 'subscription') {
                $('#subscriptionModal').modal('show');
            }
        });

        // Save document grant
        $('#saveDocumentGrant').on('click', function() {
            const docId = $('#free_document').val();
            const trialDays = $('#document_days').val();
            const docName = $('#free_document option:selected').text();

            $.ajax({
                url: "{{ route('save.free.grantDocument') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: '{{ $id }}',
                    document_id: docId,
                    duration_days: trialDays
                },
                dataType: 'json',
                success: function(response) {
                    if(response.succes == true){
                        $('#granted_info').html(`
                            <div class="alert alert-info">
                                <strong>Granted:</strong> Document <b>${docName}</b> for <b>${trialDays}</b> day(s).
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });

            $('#documentModal').modal('hide');
            
        });

        // Save subscription grant
        $('#saveSubscriptionGrant').on('click', function() {
            const planId = $('#free_subscription').val();
            const months = $('#subscription_months').val();
            const planName = $('#free_subscription option:selected').text();

            $.ajax({
                url: "{{ route('save.free.subscription') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: '{{ $id }}',
                    plan_id: planId,
                    duration_months: months
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success == true){
                        $('#granted_info').html(`
                            <div class="alert alert-info">
                                <strong>Granted:</strong> Subscription <b>${planName}</b> for <b>${months}</b> month(s).
                            </div>
                        `);
                    }
                },
            });

            $('#subscriptionModal').modal('hide');
           
        });
    });
</script>


@endsection



