

<div>
    <div class="nk-content-inner">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title order_title">All Orders</h4>

                @if($searchPerformed)
                <div class="alert alert-info mt-3">
                    @if($hasResults)
                        @if($searchType == 'order_num')
                            Showing order: <strong>{{ $search }}</strong>
                        @elseif($searchType == 'email')
                            Showing all orders from email: <strong>{{ $selectedSearchValue }}</strong>
                        @elseif($searchType == 'name')
                            Showing all orders from customer: <strong>{{ $selectedSearchValue }}</strong>
                        @elseif($searchType == 'date')
                            Showing orders from {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                        @else
                            Showing filtered orders
                        @endif
                    @else
                        No orders found matching your criteria
                    @endif
                </div>
                @endif
                <div class="row odr-filter-rw">
                    <div class="odr-filter-cl">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a wire:click.prevent="filterByStatus(null)" href="#" class="nav-link {{ is_null($status) ? 'active' : '' }}">
                                    All ({{ $statusCounts['all'] ?? 0 }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a wire:click.prevent="filterByStatus('completed')" href="#" class="nav-link {{ $status === 'completed' ? 'active' : '' }}">
                                    Completed ({{ $statusCounts['completed'] ?? 0 }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a wire:click.prevent="filterByStatus('canceled')" href="#" class="nav-link {{ $status === 'canceled' ? 'active' : '' }}">
                                    Canceled ({{ $statusCounts['canceled'] ?? 0 }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a wire:click.prevent="filterByStatus('refunded')" href="#" class="nav-link {{ $status === 'refunded' ? 'active' : '' }}">
                                    Refunded ({{ $statusCounts['refunded'] ?? 0 }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a wire:click.prevent="filterByStatus('failed')" href="#" class="nav-link {{ $status === 'failed' ? 'active' : '' }}">
                                    Failed ({{ $statusCounts['failed'] ?? 0 }})
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class=" odr-filter-cl">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">

                                <ul class="nk-block-tools g-3">
                                    <!-- Date Range Picker -->
                                    <li>
                                        <div wire:ignore>
                                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span>{{ $startDate && $endDate ? Carbon\Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon\Carbon::parse($endDate)->format('M d, Y') : 'Select Date Range' }}</span> <i class="fa fa-caret-down"></i>
                                            </div>
                                        </div>
                                    </li>

                                    <!-- Search Input -->
                                    <li>
                                        <div wire:click.away="$set('searchResults', [])" class="position-relative">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Search by order #, name or email..."
                                                wire:model.defer="search"
                                                wire:keydown.debounce.300ms="handleSearch($event.target.value)"
                                                wire:keydown.enter="performSearch"
                                                autocomplete="off"
                                            />

                                            @if($searchResults && strlen($search) > 1)
                                                <div
                                                    class="dropdown-menu show mt-1 shadow-sm rounded"
                                                    style="max-height: 250px; overflow-y: auto; z-index: 1050; position: absolute; top: 100%; left: 0; right: 0;"
                                                >
                                                    @forelse($searchResults as $result)
                                                        <a href="#"
                                                        class="dropdown-item"
                                                        wire:click.prevent="selectSearch('{{ $result->order_num }}')">
                                                            <strong>{{ $result->order_num }}</strong> – {{ $result->user->first_name ?? 'Unknown' }}
                                                        </a>
                                                        @if($result->user && $result->user->email)
                                                            <a href="#"
                                                            class="dropdown-item small text-muted ps-4"
                                                            wire:click.prevent="selectSearch('{{ $result->user->email }}', 'email', '{{ $result->user->email }}')">
                                                                Filter by email: {{ $result->user->email }}
                                                            </a>
                                                        @endif
                                                        @if($result->user && $result->user->first_name)
                                                            <a href="#"
                                                            class="dropdown-item small text-muted ps-4"
                                                            wire:click.prevent="selectSearch('{{ $result->user->first_name }}', 'name', '{{ $result->user->first_name }}')">
                                                                Filter by name: {{ $result->user->first_name }}
                                                            </a>
                                                        @endif
                                                    @empty
                                                        <div class="dropdown-item text-muted">No results found</div>
                                                    @endforelse
                                                </div>
                                            @endif
                                        </div>
                                    </li>

                                    <!-- Search Button -->
                                    <li>
                                        <button wire:click="performSearch" class="btn btn-primary">
                                            Search
                                        </button>
                                    </li>

                                    <!-- Reset Filters Button -->
                                    @if($searchPerformed || !is_null($status))
                                    <li>
                                        <button wire:click="resetFilters" class="btn btn-primary">
                                            Reset Filters
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">

            <div class="flex items-center mb-4">
                <label for="perPage" class="mr-2 font-semibold">Show</label>
                <select id="perPage" class="border rounded px-2 py-1 text-sm" wire:change="changePerPage($event.target.value)">
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
                <span class="ml-2 text-gray-600">orders per page</span>
            </div>




            <table id="data_order_table" class="nowrap table">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        {{-- <th>Age</th> --}}
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($orders) )
                    @foreach($orders as $id => $order)
                    {{-- @dump($order) --}}

                    <tr>
                        <td>
                            <a href="{{route('orders.details',$order->order_num ?? '')}}" onmouseover="this.style.color='#FD5602'" onmouseout="this.style.color='#012555'" >{{ $order->order_num ?? '' }}</a>
                        </td>
                        <td>
                            <a href="{{route('orders.details',$order->order_num ?? '')}}" onmouseover="this.style.color='#FD5602'" onmouseout="this.style.color='#012555'" >{{ $order->user->first_name ?? '' }}</a>
                        </td>
                        <td>
                            @if ($order->created_at)
                                {{ $order->created_at->format('Y-m-d') }}
                            @else
                                N/A
                            @endif
                        </td>

                        <td>
                            <div class="tb-tnx-amount">
                                <!-- {{ $order->status ?? '' }} -->
                                
                                @if($order->status == "Incomplete" )
                                    <span class="Status text-danger">{{ $order->status ?? '' }}</span>
                                @elseif($order->status == "Succeeded")
                                    <span class="Status">Completed</span>
                                @elseif($order->status === "Cancelled")
                                    <span class="Status text-danger">Cancelled</span>
                                @else 
                                    <span class="Status text-danger">{{ $order->status ?? '' }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            ${{ $order->amount ?? '' }}
                        </td>
                    </tr>

                    @endforeach
                    @endif
                </tbody>

            </table>
            <div class="mt-4 flex justify-between items-center">
                <ul class="pagination flex items-center">
                    @if ($orders->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button class="page-link" wire:click="gotoPage({{ $orders->currentPage() - 1 }})">Previous</button>
                        </li>
                    @endif

                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        <li class="page-item {{ $orders->currentPage() == $page ? 'active' : '' }}">
                            <button class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                        </li>
                    @endforeach

                    @if ($orders->hasMorePages())
                        <li class="page-item">
                            <button class="page-link" wire:click="gotoPage({{ $orders->currentPage() + 1 }})">Next</button>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next</span>
                        </li>
                    @endif
                </ul>

                <!-- Display "1 of 4 pages" on the right side -->
                <span class="text-sm text-gray-600">
                    Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                </span>
            </div>
        </div>
    </div>

    @script
    <script>
        $(document).ready(function() {

            // Initialize DateRangePicker safely
            function initializeDateRangePicker() {
                console.log('Initializing DateRangePicker...');
                const $picker = $('#reportrange');

                // Skip if element doesn't exist
                if ($picker.length === 0) {
                    console.log('DateRangePicker element not found');
                    return;
                }

                // Clean up existing instance if it exists
                const existingPicker = $picker.data('daterangepicker');
                if (existingPicker) {
                    console.log('Removing existing DateRangePicker');
                    existingPicker.remove();
                }

                // Get initial dates
                var urlParams = new URLSearchParams(window.location.search);
                var startParam = urlParams.get('startDate');
                var endParam = urlParams.get('endDate');

                var start = startParam ? moment(startParam) : moment().subtract(29, 'days');
                var end = endParam ? moment(endParam) : moment();

                // Create new instance with separate event handlers
                $picker.daterangepicker({
                    startDate: start,
                    endDate: end,
                    opens: 'left',
                    autoUpdateInput: false, // Important for proper handling
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });

                // Handle the apply event separately from initialization
                $picker.off('apply.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
                    console.log('Date range applied:', picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
                    $(this).find('span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));

                    // Update Livewire props - this is critical
                    @this.set('startDate', picker.startDate.format('YYYY-MM-DD'));
                    @this.set('endDate', picker.endDate.format('YYYY-MM-DD'));

                    // Call Livewire search method
                    @this.call('setDateRange', picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
                });

                // Set initial display
                if (startParam && endParam) {
                    $picker.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                } else {
                    $picker.find('span').html('Select Date Range');
                }

                console.log('DateRangePicker initialized successfully');
            }

            // Initialize components on page load
            initializeDateRangePicker();
            // initializeDataTable();
            // setupObserver();

            // Listen for Livewire events
            window.livewire.on('dateRangeUpdated', function(data) {
                console.log('Received dateRangeUpdated event:', data);
                if (data.startDate && data.endDate && $('#reportrange').length > 0) {
                    var daterangepicker = $('#reportrange').data('daterangepicker');
                    if (daterangepicker) {
                        daterangepicker.setStartDate(moment(data.startDate));
                        daterangepicker.setEndDate(moment(data.endDate));
                        $('#reportrange span').html(moment(data.startDate).format('MMMM D, YYYY') + ' - ' + moment(data.endDate).format('MMMM D, YYYY'));
                    }
                }
            });

        });
    </script>
    @endscript


</div>
