<div>
    <input wire:model.live="search" type="text" placeholder="Search..." class="form-control">
    <table class="table mt-3">
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
            @foreach ($accountTrace as $at)
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
                {{-- <td>{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{ $at->debt->acc_name }}
                </td>
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
                    <span class="text-muted">{{ $at->cred->acc_name }} <i class="fa-solid fa-arrow-right"></i> {{
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
                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm"><i
                                class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $accountTrace->links() }}
</div>