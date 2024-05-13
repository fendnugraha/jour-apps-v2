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
            <tr>
                <th>Total</th>
                <th>{{ number_format($totalTransfer->count() + $totalTarikTunai->count() + $totalVcr->count() +
                    $totaldeposit->count()) }}</th>
                <th>{{ number_format($totalTransfer->sum('amount') + $totalTarikTunai->sum('amount') +
                    $totalVcr->sum('amount') + $totaldeposit->sum('amount')) }}</th>
                <th class="text-end text-success">{{ number_format($totalTransfer->sum('fee_amount') +
                    $totalTarikTunai->sum('fee_amount') +
                    $totalVcr->sum('fee_amount') + $totaldeposit->sum('fee_amount')) }}</th>
            </tr>
            <tr>
                <th colspan="4" class="bg-danger text-light">Pengeluaran (Biaya)</th>
            </tr>
            @foreach ($totalBiaya as $c)
            <tr>
                <td colspan="3">{{ $c->description }}</td>
                <td class="text-end text-danger">{{ number_format($c->fee_amount) }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="3">Total Pengeluaran</th>
                <th class="text-end text-danger">{{ number_format($totalBiaya->sum('fee_amount')) }}</th>
            </tr>
            </tbody>
            <tfoot>
                <tr class="bg-success text-light">
                    <th colspan="3" class="bg-success text-light">Laba Bersih (Net Profit)</th>
                    <th class="text-end bg-success text-light">{{ number_format($totalLaba) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <h2>Ringkasan Transaksi</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Cabang</th>
                <th>Transfer</th>
                <th>Tarik Tunai</th>
                <th>Voucher & SP</th>
                <th>Deposit (Pulsa dll)</th>
                <th>Pengeluaran (Biaya)</th>
                <th>Laba Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php
            $rvTransfer = 0;
            $rvTarikTunai = 0;
            $rvVcr = 0;
            $rvdeposit = 0;
            $rvLaba = 0;
            $rvBiaya = 0;
            $rvLaba = 0;
            @endphp
            @foreach ($revenue as $w)
            @php
            $rvTransfer += $w->where('trx_type', 'Transfer Uang')->where('warehouse_id',
            $w->warehouse_id)->sum('amount');
            $rvTarikTunai += $w->where('trx_type', 'Tarik Tunai')->where('warehouse_id',
            $w->warehouse_id)->sum('amount');
            $rvVcr += $w->where('trx_type', 'Voucher & SP')->where('warehouse_id',
            $w->warehouse_id)->sum('amount');
            $rvdeposit += $w->where('trx_type', 'Deposit')->where('warehouse_id',
            $w->warehouse_id)->sum('amount');
            $rvLaba += $w->sumfee;
            $rvBiaya += $w->where('trx_type', 'Pengeluaran')->where('warehouse_id',
            $w->warehouse_id)->sum('fee_amount');
            @endphp
            <tr>
                <td>{{ $w->warehouse->w_name }}</td>
                <td>{{ number_format($w->where('trx_type', 'Transfer Uang')->where('warehouse_id',
                    $w->warehouse_id)->sum('amount')) }}</td>
                <td>{{ number_format($w->where('trx_type', 'Tarik Tunai')->where('warehouse_id',
                    $w->warehouse_id)->sum('amount')) }}</td>
                <td>{{ number_format($w->where('trx_type', 'Voucher & SP')->where('warehouse_id',
                    $w->warehouse_id)->sum('amount')) }}</td>
                <td>{{ number_format($w->where('trx_type', 'Deposit')->where('warehouse_id',
                    $w->warehouse_id)->sum('amount')) }}</td>
                <td class="text-danger">{{ number_format(-$w->where('trx_type', 'Pengeluaran')->where('warehouse_id',
                    $w->warehouse_id)->sum('fee_amount')) }}
                </td>
                <td class="text-success fw-bold">{{ number_format($w->sumfee) }}</td>
            </tr>

            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>{{ number_format($rvTransfer) }}</th>
                <th>{{ number_format($rvTarikTunai) }}</th>
                <th>{{ number_format($rvVcr) }}</th>
                <th>{{ number_format($rvdeposit) }}</th>
                <th>{{ number_format(-$rvBiaya) }}</th>
                <th>{{ number_format($revenue->sum('sumfee')) }}</th>
            </tr>
        </tfoot>
    </table>


    {{-- End Content --}}
    @endsection