@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container" style="margin-top: 70px">
  <div class="row ">
    <div class="col-sm">
      <div class="d-grid gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTransfer">
          Transfer
        </button>
      </div>
    </div>
    <div class="col-sm">
      <div class="d-grid gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTarikTunai">
          Tarik Tunai
        </button>
      </div>
    </div>
    <div class="col-sm">
      <div class="dropdown">
        <div class="d-grid gap-2">
          <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Voucher & Deposit
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#ModalvcrSP">Voucher &
                Kartu SP</a></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#ModalDeposit">Deposit</a></li>
          </ul>
        </div>
        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalvcrSP">
          Voucher & Deposit
        </button> --}}
      </div>
    </div>
    <div class="col-sm">
      <div class="d-grid gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalPengeluaran">
          Mutasi Kas
        </button>
      </div>
    </div>
  </div>
  <div class="container mt-5">
    <table class="table display">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Waktu</th>
          <th scope="col">Account</th>
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
        @php($hidden = $at->trx_type == 'Voucher & SP' ? 'hidden' : '')
        @php($status = $at->status == 1 ? '<span class="badge bg-success">Sudah diambil</span>' : '<span
          class="badge bg-warning text-dark">Belum diambil </span>')
        <tr class="{{ $warna }}">
          <th scope="row">{{ $no++ }}</th>
          <td>{{ $at->date_issued }}</td>
          <td>{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{ $at->debt->acc_name }} </td>
          <td>
            {!! $status !!} <span class="badge bg-success">{{ $at->trx_type }}</span><br>

            {{ $at->description }} @if($at->sale){{ $at->sale->product->name . ' - ' . $at->sale->quantity. ' Pcs Harga
            Modal
            Rp.'
            .
            number_format($at->sale->cost) }}@endif
          </td>
          <td>{{ number_format($at->amount) }}</td>
          <td>{{ number_format($at->fee_amount) }}</td>
          <td>{{ $at->user->name }}</td>
          <td>
            <a href="/home/{{ $at->id }}/edit" class="btn btn-warning btn-sm" {{$hidden}}>
              <i class="fa-solid fa-pen-to-square"></i></a>
            <form action="{{ route('accounttrace.delete', $at->id) }}" method="post">
              @csrf
              @method('DELETE')
              <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm"><i
                  class="fa-solid fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- End Content --}}












  {{-- Modal Input Area --}}

  {{-- Transfer --}}
  <div class="modal fade" id="ModalTransfer" tabindex="-1" aria-labelledby="ModalTransferLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalTransferLabel">Transfer</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/addTransfer" method="post">
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
              <label for="account" class="col-sm col-form-label">Nomor Rekening</label>
              <div class="col-sm-8">
                <select name="account" id="account" class="form-select">
                  <option value="">Pilih Akun</option>
                  @foreach ($warehouseaccount as $coa)
                  <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-2 row">
              <label for="amount" class="col-sm col-form-label">Jumlah</label>
              <div class="col-sm-8">
                <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount"
                  id="amount" value="{{old('amount') == null ? 0 : old('amount')}}">
                @error('amount')
                <div class="invalid-feedback">
                  <small>{{ $message }}</small>
                </div>
                @enderror
              </div>
            </div>
            <div class="mb-2 row">
              <label for="fee_amount" class="col-sm col-form-label">Fee Admin</label>
              <div class="col-sm-8">
                <input type="number" class="form-control @error('fee_amount') is-invalid @enderror" name="fee_amount"
                  id="fee_amount" value="{{old('fee_amount') == null ? 0 : old('fee_amount')}}">
                @error('fee_amount')
                <div class="invalid-feedback">
                  <small>{{ $message }}</small>
                </div>
                @enderror
              </div>
            </div>

            <div class="mb-2 row">
              <label for="description" class="col-sm col-form-label">Deskripsi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control @error('description') is-invalid @enderror" name="description"
                  id="description" value="{{old('description') == null ? '' : old('description')}}"
                  placeholder="Keterangan">
                @error('description')
                <div class="invalid-feedback">
                  <small>{{ $message }}</small>
                </div>
                @enderror
              </div>
            </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Transfer</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- TarikTunai --}}
  <div class="modal fade" id="ModalTarikTunai" tabindex="-1" aria-labelledby="ModalTarikTunaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalTarikTunaiLabel">Tarik Tunai</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/addTarikTunai" method="post">
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
              <label for="account" class="col-sm col-form-label">Nomor Rekening</label>
              <div class="col-sm-8">
                <select name="account" id="account" class="form-select">
                  <option value="">Pilih Akun</option>
                  @foreach ($warehouseaccount as $coa)
                  <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-2 row">
              <label for="amount" class="col-sm col-form-label">Jumlah</label>
              <div class="col-sm-8">
                <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount"
                  id="amount" value="{{old('amount') == null ? 0 : old('amount')}}">
                @error('amount')
                <div class="invalid-feedback">
                  <small>{{ $message }}</small>
                </div>
                @enderror
              </div>
            </div>
            <div class="mb-2 row">
              <label for="fee_amount" class="col-sm col-form-label">Fee Admin</label>
              <div class="col-sm-8">
                <input type="number" class="form-control @error('fee_amount') is-invalid @enderror" name="fee_amount"
                  id="fee_amount" value="{{old('fee_amount') == null ? 0 : old('fee_amount')}}">
                @error('fee_amount')
                <div class="invalid-feedback">
                  <small>{{ $message }}</small>
                </div>
                @enderror
              </div>
            </div>
            <div class="mb-2 row">
              <label for="description" class="col-sm col-form-label">Deskripsi</label>
              <div class="col-sm-8">
                <input type="text" class="form-control @error('description') is-invalid @enderror" name="description"
                  id="description" value="{{old('description') == null ? '' : old('description')}}"
                  placeholder="Keterangan">
                @error('description')
                <div class="invalid-feedback">
                  <small>{{ $message }}</small>
                </div>
                @enderror
              </div>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked" name="status" checked>
              <label class="form-check-label" for="flexCheckChecked">
                Sudah diambil
              </label>
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

  {{-- Voucher --}}
  <div class="modal fade" id="ModalvcrSP" tabindex="-1" aria-labelledby="ModalvcrSPLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalvcrSPLabel">Penjualan Voucher & Kartu Perdana</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/transaksi" method="post">
            @csrf
            <div class="mb-3 row">
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
            <div class="row">
              {{-- <div class="mb-3 col-sm-4">
                <label for="trx_type" class="form-label">Produk</label>
                <select class="form-select" name="trx_type" id="trx_type">
                  <option selected>- Pilih Type Transaksi -</option>
                  <option value="Voucher & SP">Voucher & Kartu Perdana</option>
                  <option value="Deposit">Deposit</option>
                </select>
              </div> --}}
              <input type="text" name="trx_type" id="trx_type" value="Voucher & SP" hidden>
              <div class="mb-3 col-sm">
                <label for="product_id" class="form-label">Produk</label>
                <select class="form-select" name="product_id" id="product_id">
                  <option selected>- Pilih Produk -</option>
                  @foreach ($product as $p)
                  <option value="{{ $p->id }}">{{ $p->name }} - Rp. {{ number_format($p->cost) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Keterangan</label>
              <input type="text" class="form-control" id="description" name="description" placeholder="Description">
            </div>
            <div class="row">
              <div class="mb-3 col-sm-2">
                <label for="Qty" class="form-label">Qty</label>
                <input type="number" class="form-control" id="Qty" name="qty" placeholder="Qty" value="1">
              </div>
              <div class="mb-3 col-sm">
                <label for="jual" class="form-label">Harga Jual</label>
                <input type="number" class="form-control" id="jual" name="jual" placeholder="Rp">
              </div>
              <div class="mb-3 col-sm">
                <label for="modal" class="form-label">Modal</label>
                <input type="number" class="form-control" id="modal" name="modal" placeholder="Rp">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Deposit --}}
  <div class="modal fade" id="ModalDeposit" tabindex="-1" aria-labelledby="ModalDepositLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalDepositLabel">Transaksi Deposit</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/transaksi" method="post">
            @csrf
            <div class="mb-3 row">
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
            <div class="row">
              {{-- <div class="mb-3 col-sm-4">
                <label for="trx_type" class="form-label">Produk</label>
                <select class="form-select" name="trx_type" id="trx_type">
                  <option selected>- Pilih Type Transaksi -</option>
                  <option value="Voucher & SP">Voucher & Kartu Perdana</option>
                  <option value="Deposit">Deposit</option>
                </select>
              </div> --}}
              <input type="text" name="trx_type" id="trx_type" value="Deposit" hidden>
              {{-- <div class="mb-3 col-sm">
                <label for="product_id" class="form-label">Produk</label>
                <select class="form-select" name="product_id" id="product_id">
                  <option selected>- Pilih Produk -</option>
                  @foreach ($product as $p)
                  <option value="{{ $p->id }}">{{ $p->name }} - Rp. {{ number_format($p->cost) }}</option>
                  @endforeach
                </select>
              </div> --}}
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Keterangan</label>
              <input type="text" class="form-control" id="description" name="description" placeholder="Description">
            </div>
            <div class="row">
              {{-- <div class="mb-3 col-sm-2">
                <label for="Qty" class="form-label">Qty</label> --}}
                <input type="number" class="form-control" id="Qty" name="qty" placeholder="Qty" value="1" hidden>
                {{--
              </div> --}}
              <div class="mb-3 col-sm">
                <label for="jual" class="form-label">Harga Jual</label>
                <input type="number" class="form-control" id="jual" name="jual" placeholder="Rp">
              </div>
              <div class="mb-3 col-sm">
                <label for="modal" class="form-label">Modal</label>
                <input type="number" class="form-control" id="modal" name="modal" placeholder="Rp">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Pengeluaran --}}
  <div class="modal fade" id="ModalPengeluaran" tabindex="-1" aria-labelledby="ModalPengeluaranLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalPengeluaranLabel">Pengeluaran</h1>
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
                  @foreach ($warehouseaccount as $coa)
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
                  @foreach ($hqaccount as $coa)
                  <option value="{{ $coa->acc_code }}">{{ $coa->acc_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-2 row">
              <label for="amount" class="col-sm col-form-label">Jumlah</label>
              <div class="col-sm-8">
                <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount"
                  id="amount" value="{{old('amount') == null ? 0 : old('amount')}}">
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
</div>

{{-- End Modal Area --}}

@endsection