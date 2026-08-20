@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
       
          
              <div class="row main_section_div">
                   <div class="col col-md-8 doc-left-content">
                        <div class="doc-top-butns2 mt-0">
                             <div class="form-group d-flex justify-content-between ">

                                  {{-- <button type="button" class="btn btn-light">AI Autofill</button> --}}
                                  <b><h4>Add New Order<span class="required_field">*</span></h4></b>
                               

                             </div>
                        </div>
                        <div class="card card-bordered card-preview mt-4">
                             <div class="card-inner">
                         
                                  <div class="col-md-12 doc-title">
                                       <div class="form-group">
                                            <label class="form-label" for="title"><b><h3>Order Detail <span class="required">#36472MX1</span></h3></b></label>
                                          
                                            <p>Payment via Credit Card<span class="card_id">(ch_011XX_141H1_TN)</span> .Paid on <span class="date">Feburary 10,2025</span> @ <span class="time">11:48.</span><br>
                                            Customer IP: <span>200.68.31.11</span></p>

                                       </div>
                                  </div>
                                  {{-- @endif --}}
                           
                                 

                                  <div class="col-md-12 mt-2">
                                    <div class="container">
                                        <div class="row">
                                          <div class="col-sm">
                                            <h5 class="mt-2">General </h5>
                                            <b > Date Created:</b>  
                                            <input type="date" name="" id=""  class="form-control" >
                                            @<input type="time"  name="" id=""  class="form-control">

                                            <b><p>Status:</p></b>  
                                            <Select class="form-control">
                                                <option value="1">Active</option>
                                                <option value="2">Inactive</option>
                                            </Select>


                                            <b><p>Customer:</p></b>  
                                            <Select class="form-control">
                                                <option value="1">guest</option>
                                                <option value="2">Inactive</option>
                                            </Select>


                                          </div>
                                          <div class="col-sm">
                                            <h5 class="mt-2">Billing</h5>  
                                            <p ><span >#114/ Monica/crystal</span></p>  
                                            <br>
                                            <b><p>Email:</p></b>

                                            <p ><span >monica_quera_01@hotmail.com</span></p>  



                                          </div>
                                          <div class="col-sm">
                                            <h5 class="mt-2">Shipping</h5>  
                                            <b><p>Address:</p></b>

                                            <p ><span >No Shipping Yet</span></p>  
                                          </div>
                                        </div>
                                      </div>
                                  </div>
                                  <hr>
                                
                                  <h5 class="mt-4">Pdf Document Data</h5>
                                  <hr>
                                  <div class="col-md-12 mt-2">
                                       <div class="form-group">
                                            
                                        <h5 class="mt-2">Invoice No.</h5>  
                                        {{-- <p ><b>Invoice No.</b><span >MX28221</span></p>  
                                        <br>
                                        <b><p>Invoice Date:</p><span class="date">Feburary 10,2025</span>@ <span class="time">11:45 AM</span></b>
                                        <br> --}}
                                        {{-- <div id="moreDetails"  style="display: none">
                                             <b><p>Invoice Display Date:</p></b><span class="invoice_date">Invoice date</span>
                                             <b><p>Invoice Created via:</p></b><span class="email_attachment">Email Attachment</span>
                                         </div> --}}
                                 
                                         <button>Set Invoice Number & Date</button>
                                         <p><span>Notes (Printed in the invoice)</span></p>
                                       </div>
                                  </div>
                                  <br>
                                  <hr>
                                <div class="col-md-12 mt-2">
                                    <div class="invoice-bills">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="w-150px">Image</th>
                                                        <th class="w-60">Item ID</th>
                                                        <th>Price</th>
                                                        <th>Qty</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>---</td>
                                                        <td>24108054</td>
                                                        <td>$40.00</td>
                                                        <td>5</td>
                                                        <td>$200.00</td>
                                                    </tr>
                                                 
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">Subtotal</td>
                                                        <td>$435.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">Processing fee</td>
                                                        <td>$10.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">TAX</td>
                                                        <td>$43.50</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">Grand Total</td>
                                                        <td>$478.50</td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                           
                                            <div class="d-flex justify-content-between">
                                                <div class="left-btn">
                                                <button class="">Add item(s)</button>
                                                <button class="">Apply Coupon</button>
                                                </div>
                                                <div class="rgt-btn">
                                                <button class="">Recalculate</button>
                                            </div>
                                            </div>


                                            <div class="nk-notes ff-italic fs-12px text-soft"> Invoice was created on a computer and is valid without the signature and seal. </div>
                                        </div>
                                    </div>
                                </div>  


                                <hr>
                                
                                <h5 class="mt-4">Download Product Permission.</h5>
                                <hr>

                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                         
                                     <h5 class="mt-2"></h5>  
                                    
                                     <div class="d-flex justify-content-between mt-2">
                                                
                                      <input type="text" class="form-control" placeholder="Search for downloadable product">
                                        <button class="ms-3">Grant </button>
                                        </div>
                                    </div>
                               </div>
                                 
                             </div>
                        </div>
                   </div>
                   <div class="col col-md-4 doc-right-content">
                        <div class="card card-bordered card-preview">
                             <div class="card-inner">
                                  {{-- <div class="d-flex justify-content-end">
                                       <div class="nk-block-head-content">
                                            <div class="up-btn mbsc-form-group">
                                                 @if(isset($document) && $document != null)
                                                 <button class="btn btn-sm btn-primary" type="submit">Update</button>
                                                 @else
                                                 <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                                 @endif
                                            </div>
                                       </div>
                                  </div>  --}}
                                  <div class="col-md-12">
                                      
                                  </div>
                                  <div class="col-md-12 mt-2">
                                       <div class="form-group">
                                           <label class="form-label" for="meta_title">Request Attribution:</label>
                                        <br>
                                        <b>Origin</b><br>
                                        <span>Unknown</span>
                                    
                                          
                                       </div>
                                  </div>

                                  <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                         <label class="form-label" for="meta_title">Send Order Email</label>
                                     <Select class="form-control">
                                          <option value="1">Choose an email to send</option>
                                          <option value="2">Inactive</option>
                                      </Select>
                                    <button class="btn btn-primary mt-1">Save Order & Send email</button>

                                    </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_description">Request Action</label>
                                         <Select class="form-control">
                                              <option value="1">Send Order Detail to Customer</option>
                                              <option value="2">Resend new order notification</option>
                                          </Select>
                                       
                               
                                        </div>
                                        <div class="d-flex justify-content-between">
                                        <a href="">Move to Trash</a>
                                        <button class="btn btn-primary">Update</button>
                                        </div>
                                        {{-- <div class="d-flex justify-content-end">
                                        
                                        </div> --}}
                                   </div>
                                </div>
                                </div>

                                  <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                            <label class="form-label" for="meta_title">Create PDF</label>
                                            <br>
                                            {{-- <input type="file"   name="uploadfile" id="img" style="display:none;"/> --}}
                                                <a class="form-control" >PDF-invoice</a>
                                            <br>
                                            {{-- <input type="file"  name="uploadfile" id="img" style="display:none;"/> --}}
                                                <a class="form-control" >PDF-Packing slip<a>

                                    </div>
                                   </div>

                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_title">Order Notes</label>

                                             <p><span>Add notes</span></p>
                                             
                                            <textarea class="form-control" name="order_notes" id="" cols="10" rows="5"></textarea>

                                            <div class="d-flex justify-content-between mt-2">
                                                
                                            <Select class="form-control">
                                                <option value="1">Private Notes</option>
                                                <option value="2">Resend </option>
                                            </Select>
                                            <button class="ms-3"> Add </button>
                                            </div>
                                        </div>
                                        </div>


                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                    <label class="form-label" for="meta_title">Buyer Document</label>
                                                    <br>
                                                    {{-- <input type="file"   name="uploadfile" id="img" style="display:none;"/> --}}
                                                        <a class="form-control" >Download Document</a>
                                                    <br>
                                            </div>
                                        </div>
                             </div> 
                        </div>
                   </div>
              </div>
         
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
     document.getElementById("toggleDetails").addEventListener("click", function(event) {
       
 
         let detailsDiv = document.getElementById("moreDetails");
         let toggleButton = document.getElementById("toggleDetails");
 
         if (detailsDiv.style.display === "none" || detailsDiv.style.display === "") {
             detailsDiv.style.display = "block";
             toggleButton.textContent = "Hide detailS";
         } else {
             detailsDiv.style.display = "none";
             toggleButton.textContent = "View more detail";
         }
    
     });
 
    
 </script>
@endsection