@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}
<div class="container mt-3">
    <!-- Content  -->
    <div class="row mb-3">
        <div class="col-lg">
            <div class="card card-widget text-bg-dark">
                <div class="card-body">
                    <h4>Bills</h4>
                    <h1><i class="fa-solid fa-receipt"></i> {{custom_number($bill_total)}}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg">
            <div class="card card-widget bg-secondary-subtle">
                <div class="card-body">
                    <h4>Payments</h4>
                    <h1><i class="fa-solid fa-credit-card"></i> {{custom_number($payment_total)}}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg">
            <div class="card card-widget text-bg-secondary">
                <div class="card-body">
                    <h4>Balance</h4>
                    <h1><i class="fa-solid fa-file-invoice"></i> {{custom_number($balance_total)}}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content-menu-nav d-flex gap-2 mb-3">
        <a href="/piutang" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Go back</a>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fa-solid fa-plus"></i> Add New
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/piutang/addPiutang">Piutang</a></li>
                <li><a class="dropdown-item" href="/piutang/addReceivableDeposit">Piutang Saldo & Awal</a></li>
                <li><a class="dropdown-item" href="/jurnal/addjournal">Piutang Penjualan Barang</a></li>
            </ul>
        </div>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="fa-solid fa-credit-card"></i> Input Pembayaran
        </button>

        <!-- Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Form Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="/piutang/payment" method="POST">
                            @csrf
                            <!-- Isi form pembayaran di sini -->
                            <div class="mb-3">
                                <label for="date_issued" class="form-label">Tanggal</label>
                                <input type="datetime-local"
                                    class="form-control {{ $errors->has('date_issued') ? 'is-invalid' : '' }}"
                                    id="date_issued" name="date_issued" placeholder="Masukkan tanggal pembayaran"
                                    value="{{ old('date') == null ? date('Y-m-d H:i') : old('date') }}">
                                @error('date_issued')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="invoice" class="form-label">Faktur</label>
                                <select name="invoice" id="invoice"
                                    class="form-select {{ $errors->has('invoice') ? 'is-invalid' : '' }}">
                                    <option value="">Pilih Faktur</option>
                                    @foreach($balances as $invoice => $balance)
                                    @if($balance->net_balance > 0)
                                    <option value="{{ $balance->invoice }}" {{ old('invoice')==$balance->invoice ?
                                        'selected' : '' }}>{{$balance->date_issued}} || {{ $balance->invoice }} || {{
                                        number_format($balance->net_balance) }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('invoice')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="debt_code" class="form-label">Akun Debet</label>
                                <select name="debt_code" id="debt_code"
                                    class="form-select {{ $errors->has('debt_code') ? 'is-invalid' : '' }}">
                                    <option value="">Pilih Akun Debet</option>
                                    @foreach ($rscFund as $ac)
                                    <option value="{{ $ac->acc_code }}" {{ old('debt_code')==$ac->acc_code ? 'selected'
                                        : '' }}>{{ $ac->acc_name }} - {{ $ac->acc_code }}</option>
                                    @endforeach
                                </select>
                                @error('debt_code')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" cols="30" rows="5"
                                    class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                    value="{{ old('description') }}">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Jumlah Pembayaran</label>
                                <input type="number"
                                    class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" name="amount"
                                    id="amount" placeholder="Masukkan jumlah pembayaran" value="{{ old('amount') }}">
                                @error('amount')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                            <!-- ... -->
                            <button type="submit" class="btn btn-primary">Bayar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <h4>{{ $rcv->first()->contact->name }}. <strong class="text-primary">Total: {{ number_format($balance_total)
            }}</strong></h4>
    <table class="display-no-order table">
        <thead>
            <tr>
                <th>Date Issued</th>
                <th>Description</th>
                <th>Bills</th>
                <th>Payments</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if ($rcv)
            @php
            $balance = 0;
            @endphp

            @foreach ($rcv as $r)
            <tr>
                <td>{{ $r->date_issued }}</td>
                <td>
                    <span class="text-success" style="font-weight: 700">{{ $r->invoice}} | {{ $r->account->acc_name }} |
                        #{{ $r->payment_nth }}</span><br>
                    {{ $r->description }}
                </td>
                <td>{{ number_format($r->bill_amount) == 0 ? '' : number_format($r->bill_amount) }}</td>
                <td>{{ number_format($r->payment_amount) == 0 ? '' : number_format($r->payment_amount) }}</td>
                <td>
                    <a href="/piutang/{{ $r->id }}/invoice" class="btn btn-primary">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <form action="/piutang/{{ $r->id }}/delete" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')

                        <!-- Your form fields or button here -->
                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="4">
                    <div class="alert alert-danger" role="alert">
                        No data
                    </div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    <!-- End Content -->
</div>


@endsection