<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AccountTrace;
use Illuminate\Support\Facades\Schema;
use Livewire\WithPagination;

class SearchTable extends Component
{
    public $search = '';

    use WithPagination;
    public function render()
    {
        $results = AccountTrace::where(function ($query) {
            $columns = Schema::getColumnListing('account_traces'); // Assuming your table name is 'account_traces'
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%' . $this->search . '%');
            }
        })->paginate(10);

        return view('livewire.search-table', [
            'accountTrace' => $results
        ]);
    }
}
