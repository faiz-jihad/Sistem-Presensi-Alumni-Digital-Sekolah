<?php

namespace App\Filament\Pages;

use App\Models\Alumni;
use App\Models\AlumniProfile;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Support\Icons\Heroicon;

class TracerStudy extends Page
{
    protected string $view = 'filament.pages.tracer-study';

    public static function getNavigationLabel(): string
    {
        return 'Tracer Study';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Alumni';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-pie';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    protected static ?string $title = 'Tracer Study Alumni';
    protected ?string $heading = 'Tracer Study Alumni';
    protected ?string $subheading = 'Pantau data tracer study alumni, sebaran status, universitas, dan perusahaan tempat bekerja.';

    public function getStats(): array
    {
        $totalAlumni = Alumni::withoutTrashed()->count();
        $verifiedAlumni = Alumni::withoutTrashed()->where('verification_status', 'verified')->count();
        $withProfile = AlumniProfile::count();

        return [
            'total_alumni'    => $totalAlumni,
            'verified_alumni' => $verifiedAlumni,
            'with_profile'    => $withProfile,
            'response_rate'   => $totalAlumni > 0 ? round(($withProfile / $totalAlumni) * 100) : 0,
        ];
    }

    public function getStatusBreakdown(): array
    {
        $statuses = [
            'studying'         => ['label' => 'Kuliah',                'color' => '#3b82f6', 'icon' => 'academic-cap'],
            'working'          => ['label' => 'Bekerja',               'color' => '#10b981', 'icon' => 'briefcase'],
            'entrepreneur'     => ['label' => 'Wirausaha',             'color' => '#f59e0b', 'icon' => 'light-bulb'],
            'studying_working' => ['label' => 'Kuliah + Bekerja',      'color' => '#8b5cf6', 'icon' => 'star'],
            'unemployed'       => ['label' => 'Belum Bekerja',         'color' => '#ef4444', 'icon' => 'clock'],
        ];

        $counts = AlumniProfile::select('current_status', DB::raw('count(*) as total'))
            ->whereNotNull('current_status')
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        $totalWithStatus = array_sum($counts);

        $result = [];
        foreach ($statuses as $key => $meta) {
            $count = $counts[$key] ?? 0;
            $result[] = [
                'key'        => $key,
                'label'      => $meta['label'],
                'color'      => $meta['color'],
                'icon'       => $meta['icon'],
                'count'      => $count,
                'percentage' => $totalWithStatus > 0 ? round(($count / $totalWithStatus) * 100) : 0,
            ];
        }

        usort($result, fn($a, $b) => $b['count'] <=> $a['count']);

        return $result;
    }

    public function getTopUniversities(): array
    {
        return AlumniProfile::select('university_name', DB::raw('count(*) as total'))
            ->whereNotNull('university_name')
            ->where('university_name', '!=', '')
            ->groupBy('university_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn($row) => ['name' => $row->university_name, 'count' => $row->total])
            ->toArray();
    }

    public function getTopCompanies(): array
    {
        return AlumniProfile::select('company_name', DB::raw('count(*) as total'))
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->groupBy('company_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn($row) => ['name' => $row->company_name, 'count' => $row->total])
            ->toArray();
    }

    public function getTopProvinces(): array
    {
        return AlumniProfile::select('province', DB::raw('count(*) as total'))
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn($row) => ['name' => $row->province, 'count' => $row->total])
            ->toArray();
    }

    public function getGraduationYearBreakdown(): array
    {
        return Alumni::withoutTrashed()
            ->select('graduation_year', DB::raw('count(*) as total'))
            ->whereNotNull('graduation_year')
            ->groupBy('graduation_year')
            ->orderByDesc('graduation_year')
            ->limit(8)
            ->get()
            ->map(fn($row) => ['year' => $row->graduation_year, 'count' => $row->total])
            ->toArray();
    }
}