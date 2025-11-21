<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrafikController extends Controller
{
    public function index()
    {
        return view('grafik');
    }

    public function dataUntukGrafik(Request $request)
    {
        $range = $request->input('range', '12_months');

        $startDate = $this->calculateStartDate($range);
        $isDaily = str_contains($range, 'day');

        // Format periode + group by sesuai kebutuhan
        $selectFormat = $isDaily
            ? "TO_CHAR(tanggal, 'YYYY-MM-DD')"
            : "TO_CHAR(tanggal, 'YYYY-MM')";

        $groupKey = $isDaily
            ? "DATE(tanggal)"
            : "DATE_TRUNC('month', tanggal)";

        // PEMASUKAN
        $pemasukan = Keuangan::select(
                DB::raw("$selectFormat AS periode"),
                DB::raw("SUM(nominal) AS total_nominal")
            )
            ->where('tanggal', '>=', $startDate)
            ->where('tipe', 'pemasukan')
            ->groupBy(DB::raw("$groupKey, periode"))
            ->orderBy('periode')
            ->get();

        // PENGELUARAN
        $pengeluaran = Keuangan::select(
                DB::raw("$selectFormat AS periode"),
                DB::raw("SUM(nominal) AS total_nominal")
            )
            ->where('tanggal', '>=', $startDate)
            ->where('tipe', 'pengeluaran')
            ->groupBy(DB::raw("$groupKey, periode"))
            ->orderBy('periode')
            ->get();

        $formattedData = $this->formatChartData($pemasukan, $pengeluaran, $isDaily);

        return response()->json([
            'status' => 'success',
            'range' => $range,
            'data' => $formattedData,
        ]);
    }

    protected function calculateStartDate(string $range): Carbon
    {
        $now = Carbon::now();

        return match ($range) {
            '7_days' => $now->subDays(6)->startOfDay(),
            '30_days' => $now->subDays(29)->startOfDay(),
            '12_months' => $now->subMonths(11)->startOfMonth(),
            'current_year' => $now->startOfYear(),
            '5_years' => $now->subYears(4)->startOfYear(),
            default => $now->subMonths(11)->startOfMonth(),
        };
    }

    protected function formatChartData($pemasukan, $pengeluaran, bool $isDaily): array
    {
        $allPeriods = $pemasukan->pluck('periode')
            ->merge($pengeluaran->pluck('periode'))
            ->unique()
            ->sort();

        $labels = [];
        $dataPemasukan = [];
        $dataPengeluaran = [];

        $mapPemasukan = $pemasukan->keyBy('periode');
        $mapPengeluaran = $pengeluaran->keyBy('periode');

        foreach ($allPeriods as $period) {

            // Label chart
            if ($isDaily) {
                $labels[] = Carbon::parse($period)->isoFormat('D MMM');
            } else {
                $labels[] = Carbon::createFromFormat('Y-m', $period)->isoFormat('MMM YYYY');
            }

            $dataPemasukan[] = (float) ($mapPemasukan[$period]->total_nominal ?? 0);
            $dataPengeluaran[] = (float) ($mapPengeluaran[$period]->total_nominal ?? 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Pemasukan',
                    'data' => $dataPemasukan,
                    'backgroundColor' => '#00BCD4   '
                ],
                [
                    'label' => 'Total Pengeluaran',
                    'data' => $dataPengeluaran,
                    'backgroundColor' => '#E91E63'
                ],
            ],
        ];
    }
}
