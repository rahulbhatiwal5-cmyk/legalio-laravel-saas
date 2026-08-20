<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Livewire\WithPagination;
use Carbon\Carbon;



class AdminOrderFilter extends Component
{
    use WithPagination;

    public int $perPage = 50;

    public $startDate = null;
    public $endDate = null;
    public $searchResults = [];
    public $search = '';
    public $searchType = 'order_num';
    public $searchPerformed = false;
    public $hasResults = false;
    public $selectedSearchValue = '';
    public $status = null;
    public $statusCounts = [];

    protected $queryString = ['startDate', 'endDate', 'search', 'searchType', 'searchPerformed', 'status'];

    public function mount()
    {
        // Initialize status counts on page load
        $this->updateStatusCounts();
        
        // Check if we need to apply filters from URL
        if (request()->has('startDate') || request()->has('endDate') || 
            request()->has('search') || request()->has('status')) {
            $this->searchPerformed = true;
            if (request()->has('searchType')) {
                $this->searchType = request()->get('searchType');
            }
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'search'])) {
            $this->resetPage();
            $this->dispatch('reinitDataTable');
        }
    }

    public function changePerPage($value)
    {
        // dd('Value received: ' . $value);
        $this->perPage = (int) $value;
        $this->resetPage();
    }
    

    public function handleSearch($value)
    {
        if (strlen($value) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Order::with('user')
            ->where(function($query) use ($value) {
                $query->where('order_num', 'like', "%$value%")
                    ->orWhereHas('user', function($q) use ($value) {
                        $q->where('first_name', 'like', "%$value%")
                          ->orWhere('email', 'like', "%$value%");
                    });
            })
            ->limit(5)
            ->get();
    }

    public function selectSearch($value, $type = 'order_num', $displayValue = null)
    {
        $this->search = $value;
        $this->searchType = $type;
        $this->selectedSearchValue = $displayValue ?: $value;
        $this->searchResults = [];
    }

    public function setDateRange($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->resetPage();
        $this->searchPerformed = true;
        $this->updateStatusCounts();
    }

    public function performSearch()
    {
        $this->resetPage();
        $this->searchResults = [];
        $this->searchPerformed = true;
        $this->updateStatusCounts();
    }

    public function filterByStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
        // Keep other filters intact
    }

    public function resetFilters()
    {
        $this->reset(['startDate', 'endDate', 'search', 'searchType', 
                     'searchPerformed', 'selectedSearchValue', 'status']);
        $this->resetPage();
        $this->updateStatusCounts();
    }

    public function updateStatusCounts()
    {
        // Base query for filtering
        $baseQuery = Order::query();
        
        // Apply date filter if set
        if ($this->startDate && $this->endDate) {
            $baseQuery->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }
        
        // Apply search filter if set
        if (!empty($this->search)) {
            if ($this->searchType == 'order_num') {
                $baseQuery->where('order_num', 'like', "%{$this->search}%");
            } elseif ($this->searchType == 'email') {
                $baseQuery->whereHas('user', function($q) {
                    $q->where('email', $this->selectedSearchValue ?: $this->search);
                });
            } elseif ($this->searchType == 'name') {
                $baseQuery->whereHas('user', function($q) {
                    $q->where('email', $this->selectedSearchValue ?: $this->search);
                });
            }
        }
        
        // Calculate counts for each status
        $this->statusCounts = [
            'all' => (clone $baseQuery)->count(),
            'completed' => (clone $baseQuery)->where('status', '1')->count(),
            'canceled' => (clone $baseQuery)->where('status', '3')->count(),
            'refunded' => (clone $baseQuery)->where('status', '2')->count(),
            'failed' => (clone $baseQuery)->where('status', '0')->count(), // Assuming failed is also status 0
        ];
    }

    public function render()
    {
        $query = Order::query();
    
        // Apply date filter if set
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }
    
        // Apply status filter if set
        if (!is_null($this->status)) {
            if ($this->status === 'completed') {
                $query->where('status', 1);
            } elseif ($this->status === 'canceled') {
                $query->where('status', 0);
            } elseif ($this->status === 'refunded') {
                $query->where('status', 2);
            } elseif ($this->status === 'failed') {
                $query->where('status', 0);
            }
        }
    
        // Apply search filter if set
        if (!empty($this->search)) {
            if ($this->searchType == 'order_num') {
                $query->where('order_num', 'like', "%{$this->search}%");
            } elseif ($this->searchType == 'email') {
                $query->whereHas('user', function($q) {
                    $q->where('email', $this->selectedSearchValue ?: $this->search);
                });
            } elseif ($this->searchType == 'name') {
                $query->whereHas('user', function($q) {
                    $q->where('first_name', 'like', "%" . ($this->selectedSearchValue ?: $this->search) . "%");
                });
            }
        }
    
        $orders = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->hasResults = $orders->total() > 0;
    
        return view('livewire.admin-order-filter', compact('orders'));
    }
}

