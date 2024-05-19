<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceivableExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    public function query()
    {
        return Receivable::query()
            ->select(
                'receivables.contact_id',
                DB::raw('min(contacts.name) as name'),
                DB::raw('SUM(bill_amount) as bill'),
                DB::raw('SUM(payment_amount) as payment'),
                DB::raw('SUM(bill_amount - payment_amount) as balance')
            )
            ->join('contacts', 'contacts.id', '=', 'receivables.contact_id')
            ->groupBy('receivables.contact_id')
            ->orderBy('name', 'asc');
    }

    public function headings(): array
    {
        return [
            ["Receivable Table Export - Exported at: " . Carbon::now()->format('Y-m-d H:i:s')],
            ["Contact ID", "Nama", "Tagihan", "Pembayaran", "Sisa Tagihan"]
        ];
    }

    public function map($rcv): array
    {
        return [
            $rcv->contact_id,
            $rcv->name,
            $rcv->bill,
            $rcv->payment,
            $rcv->balance
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
            'B' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
            'C' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
            'D' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
            'E' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC,
        ];
    }


    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true]],
        ];
    }
}
