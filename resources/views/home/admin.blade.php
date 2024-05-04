@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container mt-3">
  <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#ModalPemasukan">
    Mutasi Kas
  </button>

  <div class="daily-report my-3">
    <div class="div1">
      <div class="card text-bg-primary h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Saldo Kas</h4>
          <h1>{{ number_format($sumtotalCash) }}</h1>

        </div>
      </div>
    </div>
    <div class="div2">
      <div class="card text-bg-primary h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Saldo Bank</h4>
          <h1>{{ number_format($sumtotalBank) }}</h1>
        </div>
      </div>
    </div>
    <div class="div3">
      <div class="card text-bg-primary h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Transfer</h4>
          <h1>{{ number_format($sumtotalTransfer) }}</h1>
        </div>
      </div>
    </div>
    <div class="div4">
      <div class="card text-bg-primary h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Tarik Tunai</h4>
          <h1>{{ number_format($sumtotalTarikTunai) }}</h1>
        </div>
      </div>
    </div>
    <div class="div5">
      <div class="card text-bg-primary h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Kas & Bank</h4>
          <h1 class="text-warning fw-bold">{{ number_format($sumendbalance) }}</h1>
        </div>
      </div>
    </div>
    <div class="div6">
      <div class="card text-bg-primary h-100">
        <div class="card-body d-flex justify-content-center align-items-center flex-column">
          <h4>Total Laba (Profit)</h4>
          <h1>{{ number_format($sumfee) }}</h1>
        </div>
      </div>
    </div>
  </div>

  @foreach ($dailyreport as $w)
  <h2>{{ $w['warehouse'] }}</h2>
  <div class="wh-detail my-3 border p-3 rounded">
    <div class="daily-report my-3">
      <div class="div1">
        <div class="card text-bg-dark h-100">
          <div class="card-body d-flex justify-content-center align-items-center flex-column">
            <h4>Saldo Kas</h4>
            <h1>{{ number_format($w['totalCash']) }}</h1>

          </div>
        </div>
      </div>
      <div class="div2">
        <div class="card text-bg-dark h-100">
          <div class="card-body d-flex justify-content-center align-items-center flex-column">
            <h4>Total Saldo Bank</h4>
            <h1>{{ number_format($w['totalBank']) }}</h1>
          </div>
        </div>
      </div>
      <div class="div3">
        <div class="card text-bg-dark h-100">
          <div class="card-body d-flex justify-content-center align-items-center flex-column">
            <h4>Total Transfer</h4>
            <h1>{{ number_format($w['totalTransfer']) }}</h1>
          </div>
        </div>
      </div>
      <div class="div4">
        <div class="card text-bg-dark h-100">
          <div class="card-body d-flex justify-content-center align-items-center flex-column">
            <h4>Total Tarik Tunai</h4>
            <h1>{{ number_format($w['totalTarikTunai']) }}</h1>
          </div>
        </div>
      </div>
      <div class="div5">
        <div class="card text-bg-dark h-100">
          <div class="card-body d-flex justify-content-center align-items-center flex-column">
            <h4>Total Kas & Bank</h4>
            <h1 class="text-warning fw-bold">{{ number_format($w['endbalance']) }}</h1>
          </div>
        </div>
      </div>
      <div class="div6">
        <div class="card text-bg-dark h-100">
          <div class="card-body d-flex justify-content-center align-items-center flex-column">
            <h4>Total Laba (Profit)</h4>
            <h1>{{ number_format($w['fee']) }}</h1>
          </div>
        </div>
      </div>
    </div>

    <table class="table">
      @foreach ($w['warehouseaccount'] as $wa)
      <tr>
        <th>{{ $wa->acc_name }}</th>
        <td>{{ number_format(intval($wa->balance)) }}</td>
      </tr>
      @endforeach
    </table>
  </div>
  @endforeach
</div>





<div class="modal fade" id="ModalPemasukan" tabindex="-1" aria-labelledby="ModalPemasukanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="ModalPemasukanLabel">Pemasukan</h1>
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
        <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- End Content Area --}}
@endSection