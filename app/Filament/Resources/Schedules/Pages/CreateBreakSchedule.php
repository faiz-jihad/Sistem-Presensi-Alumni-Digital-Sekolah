<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateBreakSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected static ?string $title = 'Tambah Jam Istirahat';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->components([
                        \Filament\Forms\Components\Select::make('school_id')
                            ->relationship('school', 'name')
                            ->label('Sekolah')
                            ->required()
                            ->visible(fn () => auth()->user()->role === 'super_admin')
                            ->default(fn () => auth()->user()->school_id)
                            ->live()
                            ->columnSpanFull()
                            ->afterStateUpdated(function (callable $set) {
                                $set('semester_id', null);
                                $set('class_hour_package_id', null);
                                $set('class_hour_id', null);
                            }),
                        \Filament\Forms\Components\Select::make('semester_id')
                            ->label('Semester / Tahun Ajaran')
                            ->options(function (callable $get) {
                                $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                if (!$schoolId) return [];
                                return \App\Models\Semester::whereHas('academicYear', function ($q) use ($schoolId) {
                                    $q->where('school_id', $schoolId);
                                })->get()->mapWithKeys(function ($sem) {
                                    return [$sem->id => "{$sem->academicYear->name} - {$sem->name}"];
                                });
                            })
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih Semester'),
                        \Filament\Forms\Components\Select::make('class_hour_package_id')
                            ->label('Paket Jam Pelajaran')
                            ->options(function (callable $get) {
                                $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                if (!$schoolId) return [];
                                return \App\Models\ClassHourPackage::where('school_id', $schoolId)->where('status', 'active')->pluck('name', 'id');
                            })
                            ->required()
                            ->live()
                            ->native(false)
                            ->placeholder('Pilih Paket Jam')
                            ->afterStateUpdated(fn (callable $set) => $set('class_hour_id', null)),
                        \Filament\Forms\Components\Select::make('class_hour_id')
                            ->label('Jam Pelajaran')
                            ->options(function (callable $get) {
                                $packageId = $get('class_hour_package_id');
                                if (!$packageId) return [];
                                return \App\Models\ClassHour::where('class_hour_package_id', $packageId)
                                    ->where('status', 'active')
                                    ->where('is_break', true)
                                    ->orderBy('order')
                                    ->get()
                                    ->mapWithKeys(function ($hour) {
                                        return [$hour->id => "Istirahat Ke-{$hour->order} ({$hour->code}): " . substr($hour->start_time, 0, 5) . " - " . substr($hour->end_time, 0, 5)];
                                    });
                            })
                            ->required()
                            ->disabled(fn (callable $get) => !$get('class_hour_package_id'))
                            ->native(false)
                            ->placeholder('Pilih Jam Istirahat'),
                        \Filament\Forms\Components\Select::make('day')
                            ->label('Hari')
                            ->options([
                                'monday' => 'Senin',
                                'tuesday' => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday' => 'Kamis',
                                'friday' => 'Jumat',
                                'saturday' => 'Sabtu',
                                'sunday' => 'Minggu',
                            ])
                            ->required()
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('effective_start_date')
                            ->label('Tanggal Mulai Berlaku'),
                        \Filament\Forms\Components\DatePicker::make('effective_end_date')
                            ->label('Tanggal Akhir Berlaku'),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->role !== 'super_admin') {
            $data['school_id'] = auth()->user()->school_id;
        }

        $data['class_id'] = null;
        $data['subject_id'] = null;
        $data['teacher_id'] = null;
        $data['room'] = null;

        return $data;
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }
    
    public function getBreadcrumb(): string
    {
        return 'Tambah';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
