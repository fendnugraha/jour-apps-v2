@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <div class="card text-bg-dark card-widget my-1">
                <div class="card-body">
                    <h4>Total Transfer</h4>
                    <h2>Rp 20.000.000</h2>
                </div>
            </div>
            <div class="card text-bg-dark card-widget my-1">
                <div class="card-body">
                    <h4>Total Tarik Tunai</h4>
                    <h2>Rp 20.000.000</h2>
                </div>
            </div>
            <div class="card text-bg-dark card-widget my-1">
                <div class="card-body">
                    <h4>Total Pemasukan</h4>
                    <h2>Rp 20.000.000</h2>
                </div>
            </div>
            <div class="card text-bg-dark card-widget my-1">
                <div class="card-body">
                    <h4>Total Pengeluaran</h4>
                    <h2>Rp 20.000.000</h2>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card text-bg-dark card-widget">
                <div class="card-body">
                    <h4>Total Transfer</h4>
                    <h2>Rp 20.000.000</h2>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- End Content --}}
@endsection