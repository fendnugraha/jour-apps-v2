@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-3">

    <div class="row">
        <div class="col-sm-8">
            <form action="/home/reportcabang" method="post">
                @csrf
                @can('admin')

                <div class="mb-2 row">
                    <label for="date_issued" class="col-sm col-form-label">Cabang</label>
                    <div class="col-sm-8">
                        <select name="cabang" id="cabang" class="form-select">
                            @foreach ($warehouse as $wh)
                            <option value="{{ $wh->id }}" {{$cabang==$wh->id ? 'selected' : ''}}>{{ $wh->w_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endcan
                <div class="mb-2 row">
                    <label for="start_date" class="col-sm col-form-label">Dari</label>
                    <div class="col-sm-8">
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                            name="start_date" id="start_date"
                            value="{{ $start_date == null ? date('Y-m-d') :  $start_date}}">
                        @error('start_date')
                        <div class="invalid-feedback">
                            <small>{{ $message }}</small>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="end_date" class="col-sm col-form-label">Sampai</label>
                    <div class="col-sm-8">
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date"
                            id="end_date" value="{{$end_date == null ? date('Y-m-d') : $end_date}}">
                        @error('end_date')
                        <div class="invalid-feedback">
                            <small>{{ $message }}</small>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="/home" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <h5>Laporan Harian Cabang {{ $start_date }} s/d {{ $end_date }}</h5>
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
                    <h1 class="text-warning display-2 fw-bold">{{
                        number_format($endbalance->flatten()->sum('balance'))
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
                    <h5>Total Fee (Admin)</h5>
                    <h1>{{ number_format($fee) }}</h1>
                </div>
            </div>
        </div>
        <div class="div9">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Pengeluaran (Biaya)</h5>
                    <h1>{{ number_format(-$cost->flatten()->sum('fee_amount')) }}</h1>
                </div>
            </div>
        </div>
        <div class="div10">
            <div class="card text-bg-dark h-100 rounded-3">
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5>Total Laba (Profit)</h5>
                    <h1>{{ number_format($fee+$cost->flatten()->sum('fee_amount')) }}</h1>
                </div>
            </div>
        </div>
    </div>
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                type="button" role="tab" aria-controls="nav-home" aria-selected="true">Transaksi</button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Voucher & Kartu</button>
            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact"
                type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Kas & Bank</button>
            {{-- <button class="nav-link" id="nav-disabled-tab" data-bs-toggle="tab" data-bs-target="#nav-disabled"
                type="button" role="tab" aria-controls="nav-disabled" aria-selected="false" disabled>Disabled</button>
            --}}
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
            tabindex="0">
            <h4 class="mt-3">Transaksi</h4>
            <table class="table display">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Deskripsi</th>
                        <th>Type</th>
                        <th>Jumlah</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trx as $t)
                    @php
                    $hidden = $t->trx_type == 'Mutasi Kas' || $t->trx_type == 'Pengeluaran' || $t->trx_type == 'Voucher
                    & SP' || $t->trx_type == 'Deposit' ? 'hidden' : '' @endphp
                    <tr class="{{ $t->fee_amount == 0 && $t->description == $t->trx_type ? 'table-danger' : '' }}">
                        <td>{{ $t->date_issued }}</td>
                        <td>
                            <span class="badge text-bg-secondary">{{ $t->invoice }}</span>
                            <span class="badge text-bg-warning">{{ $t->debt->acc_name ?? '' }} x {{ $t->cred->acc_name
                                ?? '' }}</span>
                            <br>
                            Note: {{ $t->description }}
                            .
                            @if ($t->trx_type !== 'Mutasi Kas' && $t->trx_type !== 'Pengeluaran')
                            Fee (Admin): <span class="text-success fw-bold">{{ $t->fee_amount == 0 ? 'Gratis' :
                                number_format($t->fee_amount)
                                }}
                            </span>
                            @endif
                            <br>
                            <small class="text-muted">#{{ $t->warehouse->w_name }}</small>
                        </td>
                        <td class="text-center"><span class="badge text-bg-primary">{{ $t->trx_type }}</span></td>
                        <td class="text-end">{{ number_format($t->amount) }}</td>
                        <td class="text-center">
                            <a href="/home/{{ $t->id }}/edit" class="btn btn-warning btn-sm" {{$hidden}}>
                                <i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('accounttrace.delete', $t->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')"
                                    class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
            <h4 class="mt-3">Penjualan Vcr & Kartu SP</h4>
            <table class="table display">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $sumtotalcost = 0;
                    @endphp
                    @foreach ($vcr as $v)
                    @php
                    $sumtotalcost += $v->total_cost;
                    @endphp
                    <tr>
                        <td>{{ $v->product->name }}</td>
                        <td>{{ $v->qty }}</td>
                        <td>{{ number_format($v->total_cost) }}</td>
                        <td>{{ number_format($sumtotalcost) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
            <h4 class="mt-3">Kas & Bank</h4>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Akun</th>
                        <th>Saldo Akhir</th>
                        <th>Total Penambahan</th>
                        <th>Total Pengurangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warehouseaccount as $wa)
                    <tr>
                        <td>{{ $wa->acc_name }}</td>
                        <td>{{ number_format($wa->balance) }}</td>
                        <td>{{ number_format($penambahan->where('debt_code', $wa->acc_code)->sum('amount')) }}</td>
                        <td>{{ number_format($penambahan->where('cred_code', $wa->acc_code)->sum('amount')) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="nav-disabled" role="tabpanel" aria-labelledby="nav-disabled-tab" tabindex="0">...
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
</div>
{{-- End Content --}}
@endsection