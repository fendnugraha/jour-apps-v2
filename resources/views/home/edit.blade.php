@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container" style="margin-top: 70px">
    <h2>{{ $accountTrace->trx_type }} - {{ $warehouse_cash }}</h2>
    <form action="{{ route('accounttrace.update', $accountTrace->id) }}" method="post">
        @csrf
        @method('put')
        <div class="mb-3 row">
            <label for="date_issued" class="col-sm col-form-label">Tanggal</label>
            <div class="col-sm-8">
                <input type="datetime-local" class="form-control @error('date_issued') is-invalid @enderror"
                    name="date_issued" id="date_issued" value="{{ $accountTrace->date_issued }}">
                @error('date_issued')
                <div class="invalid-feedback">
                    <small>{{ $message }}</small>
                </div>
                @enderror
            </div>
        </div>
        @php
        $accountTrace->debt_code == $warehouse_cash ? $debt_status = 'd-none' : $debt_status = '';
        $accountTrace->cred_code == $warehouse_cash ? $cred_status = 'd-none' : $cred_status = '';
        @endphp
        <div class="mb-3 row {{$debt_status}}">
            <label for="debt_code" class="col-sm col-form-label">Nama Rekening</label>
            <div class="col-sm-8">
                <select name="debt_code" id="debt_code" class="form-select" $debt_status>
                    <option value="">Pilih Akun</option>
                    @foreach ($account as $coa)
                    <option value="{{ $coa->acc_code }}" {{ $coa->acc_code == $accountTrace->debt_code ? 'selected' : ''
                        }}>
                        {{ $coa->acc_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3 row {{$cred_status}}">
            <label for="cred_code" class="col-sm col-form-label">Nama Rekening</label>
            <div class="col-sm-8">
                <select name="cred_code" id="cred_code" class="form-select" $cred_status>
                    <option value="">Pilih Akun</option>
                    @foreach ($account as $coa)
                    <option value="{{ $coa->acc_code }}" {{ $coa->acc_code == $accountTrace->cred_code ? 'selected' : ''
                        }}>
                        {{ $coa->acc_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="amount" class="col-sm col-form-label">Jumlah</label>
            <div class="col-sm-8">
                <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount"
                    id="amount" value="{{ $accountTrace->amount }}">
                @error('amount')
                <div class="invalid-feedback">
                    <small>{{ $message }}</small>
                </div>
                @enderror
            </div>
        </div>

        <div class="mb-3 row">
            <label for="fee_amount" class="col-sm col-form-label">Fee Admin</label>
            <div class="col-sm-8">
                <input type="number" class="form-control @error('fee_amount') is-invalid @enderror" name="fee_amount"
                    id="fee_amount" value="{{ $accountTrace->fee_amount }}">
                @error('fee_amount')
                <div class="invalid-feedback">
                    <small>{{ $message }}</small>
                </div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="/home" class="btn btn-danger">Cancel</a>
    </form>
</div>

{{-- End Modal Area --}}

@endsection