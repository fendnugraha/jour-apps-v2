@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container mt-3">
    @include('include.setting')
    <a href="/setting" class="btn btn-primary mb-3"><i class="fa-solid fa-arrow-left"></i> Go back</a>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProduct">
        <i class="fa-solid fa-plus"></i> Add new product
    </button>
    <div class="row">
        <div class="col-sm-8">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <table class="table display-no-order">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Product name</th>
                        <th>Modal</th>
                        <th>Terjual</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ number_format($p->cost) }}</td>
                        <td>{{ number_format($p->sold) }}</td>
                        <td>
                            <a href="/product/{{ $p->id }}/edit" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('product.delete', $p->id) }}" method="POST" class="d-inline">
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

    {{-- Modal Area --}}

    <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductLabel">Add Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/product/addproduct" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Product</label>
                            <input type="text" name="name" class="form-control" id="name" required
                                value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label for="cost" class="form-label">Modal</label>
                            <input type="number" name="cost" class="form-control" id="cost" value="{{ old('cost') }}"
                                required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit"
                        onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"
                        class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- End Modal Area --}}

</div>

{{-- End Modal Area --}}

@endsection