@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-5">
    <h4>Laporan Harian Cabang {{ date('l, d F Y') }}</h4>
    <div class="daily-report mb-5">
        <div class="div1">
            <div class="card text-bg-dark h-100">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h4>Saldo Kas</h4>
                    <h1>{{ number_format($totalCash->flatten()->sum('balance')) }}</h1>

                </div>
            </div>
        </div>
        <div class="div2">
            <div class="card text-bg-dark h-100">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h4>Total Saldo Bank</h4>
                    <h1>{{ number_format($totalBank->flatten()->sum('balance')) }}</h1>
                </div>
            </div>
        </div>
        <div class="div3">
            <div class="card text-bg-dark h-100">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h4>Total Transfer</h4>
                    <h1>{{ number_format($totalTransfer) }}</h1>
                </div>
            </div>
        </div>
        <div class="div4">
            <div class="card text-bg-dark h-100">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h4>Total Tarik Tunai</h4>
                    <h1>{{ number_format($totalTarikTunai) }}</h1>
                </div>
            </div>
        </div>
        <div class="div5">
            <div class="card text-bg-dark h-100">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h4>Total Kas & Bank</h4>
                    <h1 class="text-warning display-2 fw-bold">{{ number_format($endbalance->flatten()->sum('balance'))
                        }}</h1>
                </div>
            </div>
        </div>
        <div class="div6">
            <div class="card text-bg-dark h-100">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h4>Total Laba (Profit)</h4>
                    <h1>{{ number_format($fee) }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h2 class="mt-5">Saldo Kas & Bank</h2>
    <table class="table display">
        <thead>
            <tr>
                <th scope="col">Account</th>
                <th scope="col">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($warehouseaccount as $wa)

            <tr>
                <td>{{ $wa->acc_name }}</td>
                <td>{{ number_format($wa->balance) }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>

</div>



{{-- End Content --}}
@endsection