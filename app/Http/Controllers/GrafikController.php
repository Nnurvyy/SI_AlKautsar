<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrafikController extends Controller
{

    public function dataUntukGrafik(Request $request)
    {
        $range = $request->input('range', '12_months');
        $startDate = $this->calculateStartDate($range);
        $isDaily = str_contains($range, 'day');


        $selectFormat = $isDaily ? "TO_CHAR(tanggal, 'YYYY-MM-DD')" : "TO_CHAR(tanggal, 'YYYY-MM')";
        $groupKey = $isDaily ? "DATE(tanggal)" : "DATE_TRUNC('month', tanggal)";

        $pemasukanChart = Keuangan::select(DB::raw("$selectFormat AS periode"), DB::raw("SUM(nominal) AS total_nominal"))
            ->where('tanggal', '>=', $startDate)->where('tipe', 'pemasukan')
            ->groupBy(DB::raw("$groupKey, periode"))->orderBy('periode')->get();

        $pengeluaranChart = Keuangan::select(DB::raw("$selectFormat AS periode"), DB::raw("SUM(nominal) AS total_nominal"))
            ->where('tanggal', '>=', $startDate)->where('tipe', 'pengeluaran')
            ->groupBy(DB::raw("$groupKey, periode"))->orderBy('periode')->get();

        $chartData = $this->formatChartData($pemasukanChart, $pengeluaranChart, $isDaily);


        $topPemasukan = $this->getTopCategories('pemasukan', $startDate);
        $topPengeluaran = $this->getTopCategories('pengeluaran', $startDate);

        return response()->json([
            'status' => 'success',
            'range' => $range,
            'chart' => $chartData,
            'allocation' => [
                'pemasukan' => $topPemasukan,
                'pengeluaran' => $topPengeluaran
            ]
        ]);
    }


    protected function getTopCategories($tipe, $startDate)
    {

        $totalSemua = Keuangan::where('tipe', $tipe)
            ->where('tanggal', '>=', $startDate)
            ->sum('nominal');

        if ($totalSemua == 0) return [];


        $data = Keuangan::with('kategori')
            ->select('id_kategori_keuangan', DB::raw('SUM(nominal) as total'))
            ->where('tipe', $tipe)
            ->where('tanggal', '>=', $startDate)
            ->groupBy('id_kategori_keuangan')
            ->orderByDesc('total')
            ->take(3)
            ->get();


        return $data->map(function ($item) use ($totalSemua) {
            return [
                'kategori' => $item->kategori->nama_kategori_keuangan ?? 'Tanpa Kategori',
                'total' => $item->total,
                'persentase' => round(($item->total / $totalSemua) * 100, 1)
            ];
        });
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
            ->sort()
            ->values();

        $labels = [];
        $dataPemasukan = [];
        $dataPengeluaran = [];


        $mapPemasukan = $pemasukan->keyBy('periode');
        $mapPengeluaran = $pengeluaran->keyBy('periode');

        foreach ($allPeriods as $period) {

            if ($isDaily) {

                $labels[] = Carbon::parse($period)->isoFormat('D MMM');
            } else {


                $labels[] = Carbon::parse($period . '-01')->isoFormat('MMM YYYY');
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
                    'backgroundColor' => '#28a745',
                    'borderRadius' => 4
                ],
                [
                    'label' => 'Total Pengeluaran',
                    'data' => $dataPengeluaran,
                    'backgroundColor' => '#dc3545',
                    'borderRadius' => 4
                ],
            ],
        ];
    }
}
