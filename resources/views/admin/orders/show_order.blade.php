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
                                <h4 class="nk-block-title">Your's Orders</h4>

                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            @if(isset($orders) && $orders->isNotEmpty())

                            <table class="table">
                                <thead>
                                    <tr class="order_row" >
                                        <th >#</th>
                                        <th >Order</th>
                                        <th >Invoice Number</th>
                                        <th >Invoice Date</th>
                                        <th >Date</th>
                                        <th >Status</th>
                                        <th >Total</th>
                                        <th >Origin</th>

                                    
                                      
                                    </tr>
                                </thead>
                                <?php $count=1; ?>
                                @foreach($orders as $id => $order)
                                    <tbody>
                                        <tr class="order_item" >
                                            <td class="tb-tnx-id">
                                                <a href="#">{{ $count  }}</a>
                                            </td>
                                          
                                            <td>     
                                                <a href="{{route('orders.details',$order->order_num)}}">{{ $order->order_num ?? '' }}</a>
                                            </td>
                                            <td>{{ $order->transactions->payment_intent ?? '' }}</td>
                                            <td>{{ $order->transactions->created_at->format('Y-m-d') ?? '' }}</td>
                                            <td> {{ $order->created_at->format('Y-m-d') ?? '' }} </td>

                                            <td>  
                                                <div class="tb-tnx-amount">
                                                    @if($order->status == "Incomplete" )
                                                        <span class="badge Status badge-dot bg-warning">{{ $order->status ?? '' }}</span>
                                                    
                                                    @elseif($order->status == "Succeeded")
                                                        <span class="badge Status badge-dot bg-success">{{ $order->status ?? '' }}</span>
                                                    @else
                                                        <span class="badge Status badge-dot bg-danger">{{ $order->status ?? '' }}</span>
                                                    @endif
                                                </div>   
                                            </td>
                                            <td>
                                                ${{$order->total_amount}}
                                            </td>
                                            <td>
                                               Organic Google  
                                            </td>
                                        
                                          
                                        </tr>
                                       
                                    </tbody>
                                    <?php $count++; ?>
                                @endforeach
                               
                                    
                                
                            </table>
                            @endif
                        </div>
                   
                  
                       
  
                  <div class="d-flex justify-content-center mt-4">
                        {{-- {{ $orders->links() }}   --}}

                        {{-- <nav aria-label="Page navigation">
                            <ul class="pagination">
                              
                                @if ($orders->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">Previous</a>
                                    </li>
                                @endif
                        
                                
                                @for ($i = 1; $i <= $orders->lastPage(); $i++)
                                    <li class="page-item {{ $i == $orders->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $orders->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                        
                               
                                @if ($orders->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav> --}}    
                          
                    </div>
                    
                    
                    </div>
                </div>
            </div>
           

        </div>


        
@endsection