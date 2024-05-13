@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}

<div class="container" style="margin-top: 70px">


    <div class="row">
        <div class="col-sm-8">
            <form action="/report/customreport" method="post">
                @csrf

                <div class="mb-2 row">
                    <label for="warehouse" class="col-sm col-form-label">Cabang</label>
                    <div class="col-sm-8">
                        <select name="warehouse" id="warehouse" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ $warehouse==$wh->id ? 'selected' : '' }}>{{
                                $wh->w_name
                                }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="start_date" class="col-sm col-form-label">Dari</label>
                    <div class="col-sm-8">
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                            name="start_date" id="start_date"
                            value="{{$start_date == null ? date('Y-m-d') : $start_date}}">
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
                        <a href="/home/dailyreport" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- <h5>Transaksi</h5> --}}
    <h4>{{ $warehouse_name }}</h4>
    <h5>Laporan Transaksi Cabang {{ date($start_date) }} s/d {{ date($end_date) }}</h5>

    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Type</th>
                    <th>Transaksi</th>
                    <th>Pendapatan</th>
                    <th>Fee (Admin)</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Transfer Bank</td>
                    <td>{{ number_format($totalTransfer->count()) }}</td>
                    <td>{{ number_format($totalTransfer->sum('amount')) }}</td>
                    <td>{{ number_format($totalTransfer->sum('fee_amount')) }}</td>
                </tr>
                <tr>
                    <td>Tarik Tunai</td>
                    <td>{{ number_format($totalTarikTunai->count()) }}</td>
                    <td>{{ number_format($totalTarikTunai->sum('amount')) }}</td>
                    <td>{{ number_format($totalTarikTunai->sum('fee_amount')) }}</td>
                </tr>
                <tr>
                    <td>Voucher & SP</td>
                    <td>{{ number_format($totalVcr->count()) }}</td>
                    <td>{{ number_format($totalVcr->sum('amount')) }}</td>
                    <td>{{ number_format($totalVcr->sum('fee_amount')) }}</td>
                </tr>
                <tr>
                    <td>Deposit (Pulsa dll)</td>
                    <td>{{ number_format($totaldeposit->count()) }}</td>
                    <td>{{ number_format($totaldeposit->sum('amount')) }}</td>
                    <td>{{ number_format($totaldeposit->sum('fee_amount')) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>{{ number_format($totalTransfer->count() + $totalTarikTunai->count() + $totalVcr->count() +
                        $totaldeposit->count()) }}</th>
                    <th>{{ number_format($totalTransfer->sum('amount') + $totalTarikTunai->sum('amount') +
                        $totalVcr->sum('amount') + $totaldeposit->sum('amount')) }}</th>
                    <th>{{ number_format($totalTransfer->sum('fee_amount') + $totalTarikTunai->sum('fee_amount') +
                        $totalVcr->sum('fee_amount') + $totaldeposit->sum('fee_amount')) }}</th>
                </tr>
            </tfoot>
    </div>



    {{-- End Content --}}
    @endsection