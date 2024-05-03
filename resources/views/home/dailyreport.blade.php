@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-5">
    <h4>{{ date('l, d F Y') }}</h4>
    <div class="daily-report mb-5">
        <div class="div1">
            <div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Total Kas & Bank</h4>
                <h1>20.000.000</h1>
            </div>
        </div>
    </div>
        <div class="div2"><div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Saldo Awal Kas</h4>
                <h1>20.000.000</h1>
            </div>
        </div></div>
        <div class="div3"><div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Total Transfer</h4>
                <h1>20.000.000</h1>
            </div>
        </div></div>
        <div class="div4"><div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Total Tarik Tunai</h4>
                <h1>20.000.000</h1>
            </div>
        </div></div>
        <div class="div5"><div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Total Laba</h4>
                <h1>20.000.000</h1>
            </div>
        </div></div>
        <div class="div6"><div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Total Pendapatan</h4>
                <h1>20.000.000</h1>
            </div>
        </div></div>
        <div class="div7"><div class="card text-bg-dark h-100">
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <h4>Total Saldo Bank</h4>
                <h1>20.000.000</h1>
            </div>
        </div></div>
    </div>
</div>
<div class="container mt-5">
    <h2 class="mt-5">Saldo Kas & Bank</h2>
    <table class="table display">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Account</th>
                <th scope="col">Status</th>
                <th scope="col">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td scope="row">1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
        </tbody>
    </table>

    <h2 class="mt-5">Penambahan</h2>
    <table class="table display">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Account</th>
                <th scope="col">Status</th>
                <th scope="col">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td scope="row">1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
        </tbody>
    </table>
    
    <h2 class="mt-5">Pengeluaran</h2>
    <table class="table display">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Account</th>
                <th scope="col">Status</th>
                <th scope="col">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td scope="row">1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
        </tbody>
    </table>
</div>



{{-- End Content --}}
@endsection