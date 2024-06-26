@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}
<div class="submenu bg-secondary">
  <div class="container">
    <ul class="nav justify-content-end text-white">
      {{-- <li class="nav-item">
        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#ModalCekSaldo">
          Lihat Saldo Kas & Bank
        </a>
      </li> --}}
      <li class="nav-item">
        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#ModalPemasukan">
          Mutasi Kas
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#ModalPengeluaran">
          Pengeluaran (Biaya)
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#ModalReportCabang">
          Report Cabang
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#ModalReportTrxCabang">
          Report Transaksi Cabang
        </a>
      </li>
    </ul>
  </div>
</div>

<div class="container mt-3">

  <div class="daily-report my-3">
    <div class="div1">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Saldo Kas</h4>
          <h1>{{ number_format($sumtotalCash->sum('balance')) }}</h1>

        </div>
      </div>
    </div>
    <div class="div2">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Saldo Bank</h4>
          <h1>{{ number_format($sumtotalBank->sum('balance')) }}</h1>
        </div>
      </div>
    </div>
    <div class="div3">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Transfer</h4>
          <h1>{{ number_format($sumtotalTransfer->sum('amount')) }}</h1>
        </div>
      </div>
    </div>
    <div class="div4">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Tarik Tunai</h4>
          <h1>{{ number_format($sumtotalTarikTunai) }}</h1>
        </div>
      </div>
    </div>
    <div class="div5">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Kas & Bank</h4>
          <h1 class="text-warning fw-bold display-2">{{ number_format($sumendbalance) }}</h1>
        </div>
      </div>
    </div>
    <div class="div6">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Voucher & Kartu SP</h4>
          <h1>{{ number_format($sumtotalVcr) }}</h1>
        </div>
      </div>
    </div>
    <div class="div7">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Deposit, Pulsa, Dll</h4>
          <h1>{{ number_format($sumtotaldeposit) }}</h1>
        </div>
      </div>
    </div>
    <div class="div8">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Fee (Admin)</h4>
          <h1>{{ number_format($sumfee) }}</h1>
        </div>
      </div>
    </div>
    <div class="div9">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h5>Total Pengeluaran (Biaya)</h5>
          <h1>{{ number_format(-$sumcost) }}</h1>
        </div>
      </div>
    </div>
    <div class="div10">
      <div class="card text-bg-dark h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h5>Total Laba (Profit)</h5>
          <h1>{{ number_format($sumfee+$sumcost) }}</h1>
        </div>
      </div>
    </div>
  </div>


  <table class="table display">
    <thead>
      <tr>
        <th>Nama Konter</th>
        <th>Total Uang Cash</th>
        <th>Total Saldo Bank</th>
        <th>Jumlah</th>
        <th class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($warehouse as $w)
      @php
      $cash = $sumtotalCash->where('warehouse_id', $w->id)->sum('balance');
      $bank = $sumtotalBank->where('warehouse_id', $w->id)->sum('balance');
      @endphp
      <tr>
        <td>{{ $w->name }}</td>
        <td>{{ number_format($cash) }}</td>
        <td>{{ number_format($bank) }}</td>
        <th>{{ number_format($bank+$cash) }}</th>
        <td class="text-center">
          <a href="/home/{{ $w->id }}/transfer" class="btn btn-dark btn-sm">Mutasi Saldo <i
              class="fa-solid fa-circle-arrow-right"></i></a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <hr>
  <h4 class="mt-3">Penjualan Voucher & SP</h4>
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





<div class="modal fade" id="ModalPemasukan" tabindex="-1" aria-labelledby="ModalPemasukanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="ModalPemasukanLabel">Mutasi Kas</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/mutasi" method="post">
          @csrf
          <div class="mb-2 row">
            <label for="date_issued" class="col-sm col-form-label">Tanggal</label>
            <div class="col-sm-8">
              <input type="datetime-local" class="form-control @error('date_issued') is-invalid @enderror"
                name="date_issued" id="date_issued"
                value="{{old('date_issued') == null ? date('Y-m-d H:i') : old('date_issued')}}">
              @error('date_issued')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>
          <div class="mb-2 row">
            <label for="cred" class="col-sm col-form-label">Dari</label>
            <div class="col-sm-8">
              <select name="cred" id="cred" class="form-select">
                <option value="">Pilih Akun</option>
                @foreach ($chartOfAccounts as $coa)
                <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2 row">
            <label for="debt" class="col-sm col-form-label">Ke</label>
            <div class="col-sm-8">
              <select name="debt" id="debt" class="form-select">
                <option value="">Pilih Akun</option>
                @foreach ($chartOfAccounts as $coa)
                <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2 row">
            <label for="amount" class="col-sm col-form-label">Jumlah</label>
            <div class="col-sm-8">
              <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" id="amount"
                value="{{old('amount') == null ? 0 : old('amount')}}">
              @error('amount')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"
          class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="ModalPengeluaran" tabindex="-1" aria-labelledby="ModalPengeluaranLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="ModalPengeluaranLabel">Pengeluaran</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/pengeluaran" method="post">
          @csrf
          <div class="mb-2 row">
            <label for="date_issued" class="col-sm col-form-label">Tanggal</label>
            <div class="col-sm-8">
              <input type="datetime-local" class="form-control @error('date_issued') is-invalid @enderror"
                name="date_issued" id="date_issued"
                value="{{old('date_issued') == null ? date('Y-m-d H:i') : old('date_issued')}}">
              @error('date_issued')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>
          <div class="mb-2 row">
            <label for="cred" class="col-sm col-form-label">Dari</label>
            <div class="col-sm-8">
              <select name="cred" id="cred" class="form-select">
                <option value="">Pilih Akun</option>
                @foreach ($chartOfAccounts as $coa)
                <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2 row">
            <label for="debt" class="col-sm col-form-label">Untuk</label>
            <div class="col-sm-8">
              <select name="debt" id="debt" class="form-select">
                <option value="">Pilih Akun</option>
                @foreach ($expense as $coa)
                <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2 row">
            <label for="description" class="col-sm col-form-label">Keterangan</label>
            <div class="col-sm-8">
              <input type="text" class="form-control @error('description') is-invalid @enderror" name="description"
                id="description" value="{{old('description') == null ? '' : old('description')}}">
              @error('description')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>
          <div class="mb-2 row">
            <label for="amount" class="col-sm col-form-label">Jumlah</label>
            <div class="col-sm-8">
              <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" id="amount"
                value="{{old('amount') == null ? 0 : old('amount')}}">
              @error('amount')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"
          class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ModalReportCabang" tabindex="-1" aria-labelledby="ModalReportCabangLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="ModalReportCabangLabel">Report Cabang</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/home/reportcabang" method="post">
          @csrf
          <div class="mb-2 row">
            <label for="date_issued" class="col-sm col-form-label">Cabang</label>
            <div class="col-sm-8">
              <select name="cabang" id="cabang" class="form-select">
                @foreach ($warehouse as $wh)
                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2 row">
            <label for="start_date" class="col-sm col-form-label">Dari</label>
            <div class="col-sm-8">
              <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                id="start_date" value="{{old('start_date') == null ? date('Y-m-d') : old('start_date')}}">
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
                id="end_date" value="{{old('end_date') == null ? date('Y-m-d') : old('end_date')}}">
              @error('end_date')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"
          class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="ModalReportTrxCabang" tabindex="-1" aria-labelledby="ModalReportTrxCabangLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="ModalReportTrxCabangLabel">Report Transaksi Cabang</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/home/reporttrxcabang" method="post">
          @csrf
          <div class="mb-2 row">
            <label for="start_date" class="col-sm col-form-label">Dari</label>
            <div class="col-sm-8">
              <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                id="start_date" value="{{old('start_date') == null ? date('Y-m-d') : old('start_date')}}">
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
                id="end_date" value="{{old('end_date') == null ? date('Y-m-d') : old('end_date')}}">
              @error('end_date')
              <div class="invalid-feedback">
                <small>{{ $message }}</small>
              </div>
              @enderror
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"
          class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- End Content Area --}}
@endSection