@extends('include.main')

@include('include.topbar')
@section('container')

<div class="container mt-3">
    <a href="/setting/warehouses" class="btn btn-primary mb-3"><i class="fa-solid fa-arrow-left"></i> Go back</a>
    <div class="row">
        <div class="col-md-6">
            <h3>Warehouse Details</h3>
            <table class="table">
                <tr>
                    <th>Nama Cabang</th>
                    <td>{{$warehouse->name}}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{$warehouse->address}}</td>
                </tr>
                <tr>
                    <th>Akun Kas</th>
                    <td>{{$warehouse->ChartOfAccount->acc_name}}</td>
                </tr>
                <tr>
                    <th>Created at</th>
                    <td>{{$warehouse->created_at}}</td>
                </tr>
            </table>
        </div>
    </div>

    <a href="/setting/warehouse/{{ $warehouse->id }}/edit" class="btn btn-warning btn-sm"><i
            class="fa-solid fa-pen-to-square"></i> Edit Warehouse (Gudang)</a>

    <div class="warehouse-account-list mt-3 border p-3 rounded">
        <h4>Daftar Akun Cabang</h4>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addWarehouseAccount">
            Tambah Akun Cabang
        </button>

        <table class="table display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Account</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($warehouseaccount as $wa)
                <tr>
                    <td>{{ $wa->id }}</td>
                    <td>{{ $wa->chartofaccount->acc_code }}</td>
                    <td>{{ $wa->chartofaccount->acc_name }}</td>
                    <td>
                        <form action="{{ route('warehouseaccount.delete', $wa->id) }}" method="POST" class="d-inline">
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
<!-- Modal -->
<div class="modal fade" id="addWarehouseAccount" tabindex="-1" aria-labelledby="addWarehouseAccountLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addWarehouseAccountLabel">Tambah Akun Cabang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/warehouse/addwarehouseaccount" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Account</label>
                        <select name="account_id" id="" class="form-select">
                            @foreach ($chartofaccounts as $wa)
                            <option value="{{$wa->id}}">{{$wa->acc_name}}</option>
                            @endforeach
                        </select>

                        <input type="text" name="warehouse_id" value="{{$warehouse->id}}" hidden>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Tambahkan Akun Cabang</button>
            </div>
            </form>
        </div>
    </div>
</div>


@endsection