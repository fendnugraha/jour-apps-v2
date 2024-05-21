@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Start --}}
<div class="container mt-3">

    <div class="row">
        <div class="col-sm-8">
            <form action="/home/reporttrxcabang" method="post">
                @csrf
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


    <h5 class="mb-3">Rekap Transaksi {{ $start_date }} s/d {{ $end_date }}</h5>


    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                type="button" role="tab" aria-controls="nav-home" aria-selected="true">Semua Transaksi</button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Belum Diambil
            </button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active py-3" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
            tabindex="0">
            <table class="table display-no-order">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Waktu</th>
                        {{-- <th scope="col">Account</th> --}}
                        <th scope="col">Keterangan</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Fee Admin</th>
                        <th scope="col">User</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php($no = 1)
                    @foreach ($accounttrace as $at)
                    @php($warna = $at->trx_type == 'Transfer Uang' ? 'table-danger' : 'table-success')
                    @php($hidden = $at->trx_type == 'Voucher & SP' ? 'hidden' : ($at->trx_type == 'Deposit' ? 'hidden' :
                    ($at->trx_type == 'Mutasi Kas' ? 'hidden' : ($at->trx_type == 'Pengeluaran' ? 'hidden' : '' ))))
                    @php($status = $at->status == 1 ? '<span class="badge bg-success">Success</span>' : '<span
                        class="badge bg-warning text-dark">Belum diambil </span>')
                    @php($fee = $at->fee_amount == 0 && $at->trx_type !== 'Mutasi Kas' ? '<span
                        class="badge bg-danger">Gratis</span>' : '')
                    @php(
                    $badge = ($at->trx_type == 'Transfer Uang') ? 'badge bg-info text-dark' :
                    (($at->trx_type == 'Tarik Tunai') ? 'badge bg-danger' :
                    (($at->trx_type == 'Voucher & SP') ? 'badge bg-primary' :
                    (($at->trx_type == 'Deposit') ? 'badge bg-warning text-dark' : 'badge bg-secondary'))))
                    <tr>
                        <th scope="row">{{ $no++ }}</th>
                        <td>{{ $at->date_issued }}</td>
                        {{-- <td>{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{
                            $at->debt->acc_name }} </td>
                        --}}
                        <td>
                            <span class="badge text-bg-light">{{ $at->invoice }}</span> {!! $status !!} <span
                                class="badge {{$badge}}">{{
                                $at->trx_type }}</span>
                            {!! $fee !!}
                            <br>

                            {{ $at->description }}
                            @if($at->sale){{ $at->sale->product->name . ' - ' . $at->sale->quantity. ' Pcs Harga
                            Modal
                            Rp.'
                            .
                            number_format($at->sale->cost) }}
                            @endif
                            <br>
                            <span class="text-muted">{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i>
                                {{
                                $at->debt->acc_name }}</span>
                        </td>
                        <td>{{ number_format($at->amount) }}</td>
                        <td>{{ number_format($at->fee_amount) }}</td>
                        <td>{{ $at->user->name }}</td>
                        <td class="text-center">
                            <a href="/home/{{ $at->id }}/edit" class="btn btn-warning btn-sm" {{$hidden}}>
                                <i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('accounttrace.delete', $at->id) }}" method="post" class="d-inline">
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
        <div class="tab-pane fade py-3" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
            <table class="table display-no-order">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Waktu</th>
                        {{-- <th scope="col">Account</th> --}}
                        <th scope="col">Keterangan</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Fee Admin</th>
                        <th scope="col">User</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php($no = 1)
                    @foreach ($belumdiambil as $at)
                    @php($warna = $at->trx_type == 'Transfer Uang' ? 'table-danger' : 'table-success')
                    @php($hidden = $at->trx_type == 'Voucher & SP' ? 'hidden' : ($at->trx_type == 'Deposit' ? 'hidden' :
                    ($at->trx_type == 'Mutasi Kas' ? 'hidden' : ($at->trx_type == 'Pengeluaran' ? 'hidden' : '' ))))
                    @php($status = $at->status == 1 ? '<span class="badge bg-success">Success</span>' : '<span
                        class="badge bg-warning text-dark">Belum diambil </span>')
                    @php(
                    $badge = ($at->trx_type == 'Transfer Uang') ? 'badge bg-info text-dark' :
                    (($at->trx_type == 'Tarik Tunai') ? 'badge bg-danger' :
                    (($at->trx_type == 'Voucher & SP') ? 'badge bg-primary' :
                    (($at->trx_type == 'Deposit') ? 'badge bg-warning text-dark' : 'badge bg-secondary'))))
                    <tr class="{{ $warna }}">
                        <th scope="row">{{ $no++ }}</th>
                        <td>{{ $at->date_issued }}</td>
                        {{-- <td>{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{
                            $at->debt->acc_name }} </td>
                        --}}
                        <td>
                            <span class="badge text-bg-light">{{ $at->invoice }}</span> {!! $status !!} <span
                                class="badge {{$badge}}">{{
                                $at->trx_type }}</span><br>

                            {{ $at->description }}
                            @if($at->sale){{ $at->sale->product->name . ' - ' . $at->sale->quantity. ' Pcs Harga
                            Modal
                            Rp.'
                            .
                            number_format($at->sale->cost) }}
                            @endif
                            <br>
                            <span class="text-muted">{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i>
                                {{
                                $at->debt->acc_name }}</span>
                        </td>
                        <td>{{ number_format($at->amount) }}</td>
                        <td>{{ number_format($at->fee_amount) }}</td>
                        <td>{{ $at->user->name }}</td>
                        <td class="text-center">
                            <a href="/home/{{ $at->id }}/edit" class="btn btn-warning btn-sm" {{$hidden}}>
                                <i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('accounttrace.delete', $at->id) }}" method="post" class="d-inline">
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
    </div>
</div>

{{-- End Content --}}
@endsection