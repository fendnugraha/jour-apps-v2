@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}
<div class="row g-1">
    <div class="col">
        <div class="card card-widget text-bg-dark">
            <div class="card-body">
                <h5>Saldo awal</h5>
                <h4><i class="fa-solid fa-file-invoice"></i> {{ number_format(intval($initBalance)) }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card card-widget text-bg-dark">
            <div class="card-body p-3">
                <h5>Debet</h5>
                <h4><i class="fa-solid fa-file-invoice"></i> {{ number_format(intval($debt_total)) }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card card-widget text-bg-dark">
            <div class="card-body p-3">
                <h5>Kredit</h5>
                <h4><i class="fa-solid fa-file-invoice"></i> {{ number_format(intval($cred_total)) }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card card-widget text-bg-dark">
            <div class="card-body p-3">
                <h5>Saldo akhir</h5>
                <h4><i class="fa-solid fa-file-invoice"></i> {{ number_format(intval($endBalance)) }}</h4>
            </div>
        </div>
    </div>
</div>

<form action="/home/generalledger" method="post" class="form-inline my-3">
    @csrf
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="accounts">Akun</label>
                <select name="accounts" id="accounts" class="form-select">
                    <option value="">Pilih Akun</option>
                    @foreach ($account as $ac)
                    <option value="{{ $ac->acc_code }}" {{$accounts==$ac->acc_code ? 'selected' : ''}}>{{
                        $ac->acc_name
                        }} - {{ $ac->acc_code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="start_date">Dari</label>
                <input type="date" name="start_date" id="start_date" class="form-control"
                    value="{{ date('Y-m-d') == null ? date('Y-m-d') : $start_date }}">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="end_date">Sampai</label>
                <input type="date" name="end_date" id="end_date" class="form-control"
                    value="{{ date('Y-m-d') == null ? date('Y-m-d') : $end_date }}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary my-2">Submit</button>
    <a href="/home/dailyreport" class="btn btn-secondary my-2">Kembali</a>
</form>


<table class="table display-no-order">
    <thead>
        <tr>
            <th>WAKTU</th>
            <th>INVOICE</th>
            <th>DESKRIPSI</th>
            <th>DEBET</th>
            <th>CREDIT</th>
            <th>SALDO</th>
        </tr>
    </thead>
    <tbody>
        <?php $balance = 0; ?>
        @foreach ($account_trace as $ac)
        <?php
    $debt_amount = $ac->debt_code == $accounts ? $ac->amount : 0;
    $cred_amount = $ac->cred_code == $accounts ? $ac->amount : 0;
    $status == 'D' ?  $balance += $debt_amount - $cred_amount : $balance += $cred_amount - $debt_amount;
    ?>
        <tr>
            <td>{{ $ac->date_issued }}</td>
            <td>{{ $ac->invoice }}</td>
            <td>
                <span class="badge text-bg-success">{{ $ac->debt->acc_name ?? ''}} x {{ $ac->cred->acc_name ??
                    ''}}</span>
                <span class="badge text-bg-warning">{{ $ac->warehouse->w_name}}</span>
                <span class="badge text-bg-dark">{{ $ac->user->name}}</span>
                <br>
                Note: {{ $ac->description }}
                <br>
                @if ($ac->trx_type !== 'Mutasi Kas' && $ac->trx_type !== 'Pengeluaran')
                Fee (Admin): <span class="text-success fw-bold">{{ $ac->fee_amount == 0 ? 'Gratis' :
                    number_format($ac->fee_amount)
                    }}
                </span>

                @endif
            </td>
            <td>{{ $ac->debt_code == $accounts ? number_format($ac->amount) : '' }}</td>
            <td>{{ $ac->cred_code == $accounts ? number_format($ac->amount) : '' }}</td>
            <td>{{ number_format($initBalance + $balance) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection