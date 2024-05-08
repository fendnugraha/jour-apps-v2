@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-3">
    <h5>Laporan Harian Cabang {{ date('l, d F Y') }}</h5>
    <div class="daily-report my-3">
        <div class="div1">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Saldo Kas</h5>
                    <h1>{{ number_format($totalCash->flatten()->sum('balance')) }}</h1>

                </div>
            </div>
        </div>
        <div class="div2">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Saldo Bank</h5>
                    <h1>{{ number_format($totalBank->flatten()->sum('balance')) }}</h1>
                </div>
            </div>
        </div>
        <div class="div3">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Transfer</h5>
                    <h1>{{ number_format($totalTransfer) }}</h1>
                </div>
            </div>
        </div>
        <div class="div4">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Tarik Tunai</h5>
                    <h1>{{ number_format($totalTarikTunai) }}</h1>
                </div>
            </div>
        </div>
        <div class="div5">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Kas & Bank</h5>
                    <h1 class="text-warning display-2 fw-bold">{{ number_format($endbalance->flatten()->sum('balance'))
                        }}</h1>
                </div>
            </div>
        </div>
        <div class="div6">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Voucher & Kartu SP</h5>
                    <h1>{{ number_format($totalVcr) }}</h1>
                </div>
            </div>
        </div>
        <div class="div7">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Deposit, Pulsa, Dll</h5>
                    <h1>{{ number_format($totaldeposit) }}</h1>
                </div>
            </div>
        </div>
        <div class="div8">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Laba (Profit)</h5>
                    <h1>{{ number_format($fee) }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-4">
            <h2 class="">Saldo Kas & Bank</h2>
            <table class="table">
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
        <div class="col-sm">
            <div class="row">
                <div class="col-sm">
                    <h2 class="">Mutasi Kas</h2>
                </div>
                <div class="col-sm">
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#generalLedger">
                        History Mutasi Saldo</button>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Account</th>
                        <th scope="col">Penambahan</th>
                        <th scope="col">Pengurangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penambahan as $wa)
                    <tr>
                        <td>{{ $wa->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{ $wa->debt->acc_name }}
                        </td>
                        <td>{{ number_format($wa->amount) }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                    @foreach ($pengeluaran as $wa)
                    <tr>
                        <td>{{ $wa->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{ $wa->debt->acc_name }}
                        </td>
                        <td></td>
                        <td>{{ number_format($wa->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            <h2 class="">Penjulalan Vcr & Kartu SP</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Waktu</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Product</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Jual </th>
                        <th scope="col">Modal</th>
                        <th scope="col">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $s)
                    @php
                    $jual = $s->quantity * $s->price;
                    $modal = $s->quantity * $s->cost;
                    $fee = $jual - $modal;
                    @endphp
                    <tr>
                        <td>{{ $s->created_at }}</td>
                        <td>{{ $s->invoice }}</td>
                        <td>{{ $s->product->name }}</td>
                        <td>{{ $s->quantity }}</td>
                        <td>{{ number_format($jual) }}
                            <small class="text-muted d-block">{{ number_format($s->quantity) }} * {{
                                number_format($s->price)
                                }}</small>
                        </td>
                        <td>{{ number_format($modal) }}
                            <small class="text-muted d-block">{{ number_format($s->quantity) }} * {{
                                number_format($s->cost)
                                }}</small>
                        </td>
                        <td>{{ number_format($fee) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="generalLedger" tabindex="-1" aria-labelledby="generalLedgerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="generalLedgerLabel">History Saldo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/home/generalledger" method="post">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="accounts">Akun</label>
                        <select name="accounts" id="accounts" class="form-select">
                            <option value="">Pilih Akun</option>
                            @foreach ($account as $ac)
                            <option value="{{ $ac->acc_code }}">{{ $ac->acc_name }} - {{ $ac->acc_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="start_date">Dari</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="end_date">Sampai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ date('Y-m-d') }}">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- End Content --}}
@endsection