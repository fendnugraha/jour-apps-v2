@extends('include.main')

@include('include.topbar')
@section('container')
{{-- Content Area --}}
<div class="container mt-3">
    <h2>Transfer Saldo ke Cabang <span class="text-primary">{{ $warehouse->w_name }}</span></h2>
    <div class="row">
        <div class="col-sm-7">
            <div class="card text-bg-dark rounded-3">
                <div class="card-body">
                    <form action="/mutasi" method="post">
                        @csrf
                        <div class="mb-2 row">
                            <label for="date_issued" class="col-sm col-form-label">Tanggal</label>
                            <div class="col-sm-8">
                                <input type="datetime-local"
                                    class="form-control @error('date_issued') is-invalid @enderror" name="date_issued"
                                    id="date_issued"
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
                                <select name="cred" id="cred" class="form-select @error('cred') is-invalid @enderror">
                                    <option value="">-Pilih Sumber Dana-</option>
                                    @foreach ($hqaccount as $coa)
                                    <option value="{{ $coa->acc_code }}" {{ old('cred')==$coa->acc_code ? 'selected' :
                                        '' }}>{{ $coa->acc_name }}</option>
                                    @endforeach
                                </select>
                                @error('cred')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <label for="debt" class="col-sm col-form-label">Ke</label>
                            <div class="col-sm-8">
                                <select name="debt" id="debt" class="form-select @error('debt') is-invalid @enderror">
                                    <option value="">-Pilih Akun Tujuan-</option>
                                    @foreach ($warehouseaccount as $coa)
                                    <option value="{{ $coa->acc_code }}" {{ old('debt')==$coa->acc_code ? 'selected' :
                                        '' }}>{{ $coa->acc_name }}</option>
                                    @endforeach
                                </select>
                                @error('debt')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <label for="amount" class="col-sm col-form-label">Jumlah</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                    name="amount" id="amount" value="{{old('amount') == null ? '' : old('amount')}}"
                                    placeholder="Jumlah Transfer">
                                @error('amount')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <label for="description" class="col-sm col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control @error('description') is-invalid @enderror"
                                    name="description" id="description"
                                    value="{{old('description') == null ? '' : old('description')}}"
                                    placeholder="Keterangan">
                                @error('description')
                                <div class="invalid-feedback">
                                    <small>{{ $message }}</small>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex mt-3 justify-content-start gap-2 align-items-center">
                            <button type="submit"
                                onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"
                                class="btn btn-primary">Simpan</button>
                            <a href="/home/administrator" class="btn btn-danger">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <h4 class="my-3">History Saldo</h4>
    <table class="table display-no-order">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Akun</th>
                <th>Penambahan</th>
                <th>Pengurangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($accountTrace as $ac)
            @php
            $debt_amount = $hq->contains($ac->debt_code) ? number_format($ac->amount) : '';
            $cred_amount = $hq->contains($ac->cred_code) ? number_format($ac->amount) : '';
            @endphp
            <tr>
                <td>
                    {{ $ac->date_issued }}</td>
                <td>{{ $ac->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{ $ac->debt->acc_name }}
                </td>
                <td class="text-end">{{ $cred_amount }}</td>
                <td class="text-end">{{ $debt_amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>
{{-- End Content Area --}}
@endSection