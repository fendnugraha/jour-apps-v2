@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container mt-5">
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
      <div class="d-grid gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalPemasukan">
          Pemasukan
        </button>
      </div>
    </div>
    <div class="col-sm">
      <div class="d-grid gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalPengeluaran">
          Pengeluaran
        </button>
      </div>
    </div>
  </div>
  <div class="container mt-5">
    <table class="table display">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Account</th>
          <th scope="col">Status</th>
          <th scope="col">Jumlah</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr class="table-success">
          <th scope="row">1</th>
          <td>BCA 4343434343</td>
          <td><span class="badge bg-success">Transfer</span></td>
          <td>200.000</td>
          <td>
            <a href="#" class="btn btn-primary">Detail
            </a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit</button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#ModalDelete">Delete</button>

          </td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td>BCA 4343434343</td>
          <td><span class="badge bg-success">Tarik Tunai</span></td>
          <td>200.000</td>
          <td>
            <a href="#" class="btn btn-primary">Detail
            </a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit</button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#ModalDelete">Delete</button>

          </td>
        </tr>
        <tr class="table-success">
          <th scope="row">3</th>
          <td>BCA 4343434343</td>
          <td><span class="badge bg-success">Transfer</span></td>
          <td>200.000</td>
          <td>
            <a href="#" class="btn btn-primary">Detail
            </a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit</button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#ModalDelete">Delete</button>

          </td>
        </tr>
        <tr>
          <th scope="row">4</th>
          <td>BCA 4343434343</td>
          <td><span class="badge bg-success">Tarik Tunai</span></td>
          <td>200.000</td>
          <td>
            <a href="#" class="btn btn-primary">Detail
            </a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit</button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#ModalDelete">Delete</button>

          </td>
        </tr>
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
          <form action="" method="post">
            @csrf
            <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">Email address</label>
              <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
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
          <form action="" method="post">
            @csrf
            <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">Email address</label>
              <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Pemasukan --}}
  <div class="modal fade" id="ModalPemasukan" tabindex="-1" aria-labelledby="ModalPemasukanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalPemasukanLabel">Pemasukan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post">
            @csrf
            <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">Email address</label>
              <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Pengeluaran --}}
  <div class="modal fade" id="ModalPengeluaran" tabindex="-1" aria-labelledby="ModalPengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="ModalPengeluaranLabel">Pengeluaran</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post">
            @csrf
            <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">Email address</label>
              <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- End Modal Area --}}

@endsection