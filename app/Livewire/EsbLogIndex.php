<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\EsbApiRequestLog;
use Carbon\Carbon;
use Livewire\WithPagination;

class EsbLogIndex extends Component
{
    use WithPagination;

    public $title = "ESB API Request Logs";

    // Filters
    public $search = '';
    public $method = '';
    public $success = null; // true/false/null
    public $retried_with_refresh = null; // true/false/null
    public $user_id = null;
    public $request_source = '';
    public $request_type = '';
    public $created_after = '';
    public $created_before = '';
    public $perPage = 50;

    public $selectedLog = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'method' => ['except' => ''],
        'success' => ['except' => null],
        'retried_with_refresh' => ['except' => null],
        'user_id' => ['except' => null],
        'request_source' => ['except' => ''],
        'request_type' => ['except' => ''],
        'created_after' => ['except' => ''],
        'created_before' => ['except' => ''],
        'perPage' => ['except' => 50],
    ];

    private function requestLog(): EsbApiRequestLog
    {
        return app(EsbApiRequestLog::class);
    }

    protected function buildFilters(): array
    {
        $filters = [];

        if (!empty($this->search)) {
            $filters['search'] = trim($this->search);
        }

        if (!empty($this->method)) {
            $filters['method'] = strtoupper($this->method);
        }

        if ($this->success !== null && $this->success !== '') {
            // allow string booleans from UI
            $filters['success'] = filter_var($this->success, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($this->retried_with_refresh !== null && $this->retried_with_refresh !== '') {
            $filters['retried_with_refresh'] = filter_var($this->retried_with_refresh, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (!empty($this->user_id)) {
            $filters['user_id'] = $this->user_id;
        }

        if (!empty($this->request_source)) {
            $filters['request_source'] = $this->request_source;
        }

        if (!empty($this->request_type)) {
            $filters['request_type'] = $this->request_type;
        }

        if (!empty($this->created_after)) {
            try {
                $filters['created_after'] = Carbon::parse($this->created_after)->startOfDay();
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        if (!empty($this->created_before)) {
            try {
                $filters['created_before'] = Carbon::parse($this->created_before)->endOfDay();
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }

        return $filters;
    }

    public function updated($name, $value)
    {
        if ($name !== 'page') {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->method = '';
        $this->success = null;
        $this->retried_with_refresh = null;
        $this->user_id = null;
        $this->request_source = '';
        $this->request_type = '';
        $this->created_after = '';
        $this->created_before = '';
        $this->resetPage();
    }

    public function getStatusLabel($log): string
    {
        if ($log->success) {
            return 'Success';
        }

        if (!empty($log->error_message)) {
            return 'Failed';
        }

        return 'Pending';
    }

    public function formatJson(?string $value): string
    {
        if (blank($value)) {
            return '-';
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $value;
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function selectLog($id)
    {
        $this->selectedLog = $this->requestLog()->getById($id);



        $this->dispatch('modal-show', name: 'esb-log-modal');
    }

    public function render()
    {
        $filters = $this->buildFilters();
        $perPage = max(10, min(100, intval($this->perPage)));
        $requestLog = $this->requestLog();

        return view('livewire.esb-log-index', [
            'logs' => $requestLog->paginate($filters, $perPage),
            'stats' => $requestLog->getStats($filters),
            'filterOptions' => $requestLog->getFilterOptions(),
        ])->layout('components.layouts.app', ['title' => $this->title]);
    }
}
