<div>
    <table class="table">
        <tbody>
            @foreach ($warehouseaccount as $wa)

            <tr>
                <th colspan="2" class="bg-warning">{{ $wa->acc_name }}</th>
            </tr>
            <tr>
                <td></td>
                <td class="text-end fw-bold" style="font-size: 1.2rem">{{ number_format($wa->balance) }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>