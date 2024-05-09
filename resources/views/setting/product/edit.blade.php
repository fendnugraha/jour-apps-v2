@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}

<div class="container" style="margin-top: 70px">
    <h4>Edit Product</h4>
    <div class="row">
        <div class="col-8">
            <form action="{{ route('product.update', $product->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                        name="name" value="{{ old('name') ?? $product->name }}">
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="cost" class="form-label">Modal</label>
                    <input type="number" class="form-control {{ $errors->has('cost') ? 'is-invalid' : '' }}" id="cost"
                        name="cost" value="{{ old('cost') ?? $product->cost }}">
                    @error('cost')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="/setting/products" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>


</div>

{{-- End Modal Area --}}

@endsection