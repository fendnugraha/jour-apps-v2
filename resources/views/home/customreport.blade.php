@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-3">

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
                <th colspan="3" class="text-danger"><a href="#" class="text-danger text-decoration-none"
                        data-bs-toggle="modal" data-bs-target="#exampleModal">Total Pengeluaran (Biaya)</a></th>
                <th class="text-end text-danger">{{ number_format(-$totalBiaya->sum('fee_amount')) }}</th>
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
    <hr>
    <h4>Penjualan Voucher & SP</h4>
    <table class="table display-no-order">
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
    <hr>
    <h2>Ringkasan Transaksi</h2>
    <small>{{ \Carbon\Carbon::parse($start_date)->locale('id_ID')->isoFormat('dddd, D MMMM YYYY') }}
        s/d {{ \Carbon\Carbon::parse($end_date)->locale('id_ID')->isoFormat('dddd, D MMMM YYYY') }}</small>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Cabang</th>
                <th>Transfer</th>
                <th>Tarik Tunai</th>
                <th>Voucher & SP</th>
                <th>Deposit (Pulsa dll)</th>
                <th>Transaksi</th>
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
            $totaltrx = 0;
            @endphp
            @foreach ($revenue as $w)
            @php
            $rv = $w->whereBetween('date_issued', [\Carbon\Carbon::parse($start_date)->startOfDay(),
            \Carbon\Carbon::parse($end_date)->endOfDay()])->where('warehouse_id', $w->warehouse_id)->get();

            $rvTransfer += $rv->where('trx_type', 'Transfer Uang')->sum('amount');
            $rvTarikTunai += $rv->where('trx_type', 'Tarik Tunai')->sum('amount');
            $rvVcr += $rv->where('trx_type', 'Voucher & SP')->sum('amount');
            $rvdeposit += $rv->where('trx_type', 'Deposit')->sum('amount');
            $rvLaba += $w->sumfee;
            $rvBiaya += $rv->where('trx_type', 'Pengeluaran')->sum('fee_amount');
            $totaltrx += $rv->count();
            @endphp
            <tr>
                <td>{{ $w->warehouse->w_name }}</td>
                <td>{{ number_format($rv->where('trx_type', 'Transfer Uang')->sum('amount')) }}</td>
                <td>{{ number_format($rv->where('trx_type', 'Tarik Tunai')->sum('amount')) }}</td>
                <td>{{ number_format($rv->where('trx_type', 'Voucher & SP')->sum('amount')) }}</td>
                <td>{{ number_format($rv->where('trx_type', 'Deposit')->sum('amount')) }}</td>
                <td>{{ number_format($rv->count()) }}</td>
                <td class="text-danger">
                    {{ number_format(-$rv->where('trx_type', 'Pengeluaran')->sum('fee_amount')) }}
                </td>
                <td class="text-success fw-bold">{{ number_format($w->sumfee) }}</td>
            </tr>

            @endforeach
        </tbody>
        <tfoot class="table-warning">
            <tr>
                <th>Total</th>
                <th>{{ number_format($rvTransfer) }}</th>
                <th>{{ number_format($rvTarikTunai) }}</th>
                <th>{{ number_format($rvVcr) }}</th>
                <th>{{ number_format($rvdeposit) }}</th>
                <th>{{ number_format($totaltrx) }}</th>
                <th>{{ number_format(-$rvBiaya) }}</th>
                <th>{{ number_format($revenue->sum('sumfee')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table display table-bordered">
                    <thead>
                        <tr>
                            <th class="bg-danger text-light">Waktu</th>
                            <th class="bg-danger text-light">Pengeluaran (Biaya)</th>
                            <th class="bg-success text-light">Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($totalBiaya as $c)
                        <tr>
                            <td>{{ $c->created_at }}</td>
                            <td>
                                {{ strtoupper($c->description) }} <small class="text-muted">({{ $c->warehouse->w_name
                                    }})</small>
                            </td>
                            <td class="text-end text-danger">{{ number_format(-$c->fee_amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




{{-- End Content --}}
@endsection