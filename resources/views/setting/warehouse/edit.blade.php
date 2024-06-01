@extends('include.main')

@include('include.topbar')
@section('container')
<div class="container mt-3">
    <!-- Content  -->
    <div class="card">
        <div class="card-body">
            <form action="/setting/warehouse/{{ $warehouse->id }}/edit" method="post">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="code" class="form-label">Warehouse Code</label>
                    <input type="text" class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" id="code"
                        name="code" value="{{ old('code') ?? $warehouse->code }}">
                    @error('code')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Warehouse Name</label>
                    <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                        name="name" value="{{ old('name') ?? $warehouse->name }}">
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="account" class="form-label">Cash Account</label>
                    <select name="account" id="account"
                        class="form-select {{ $errors->has('account') ? 'is-invalid' : '' }}">
                        <option value="">Pilih Akun</option>
                        @foreach ($account as $ac)
                        <option value="{{ $ac->id }}" {{ $warehouse->chart_of_account_id == $ac->id ? 'selected' : ''
                            }}>{{ $ac->acc_code }} - {{ $ac->acc_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea name="address" id="address" cols="30" rows="5"
                        class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}"
                        value="{{ old('address') ?? $warehouse->address }}">{{ old('address') ?? $warehouse->address }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <button type="submit" class="btn btn-success">Update</button>
                <a href="/setting/warehouses" class="btn btn-danger">Cancel</a>
            </form>


        </div>
    </div>
</div>
@endsection