@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container mt-3">
    <livewire:search-table />
</div>
{{-- End Content Area --}}
@endSection