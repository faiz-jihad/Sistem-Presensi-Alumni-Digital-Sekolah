<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $days = $data['days'];
        $monthName = $data['month_name'];
        $year = $data['year'];
        $teacherName = $data['teacher_name'];
    @endphp

    <div class="teacher-calendar-container">
        <!-- Self-contained Header Block -->
        <div class="calendar-header-block">
            <div class="calendar-title-area">
                <x-heroicon-o-calendar class="calendar-header-icon" style="width: 24px; height: 24px; color: #1E88E5; flex-shrink: 0; display: inline-block;" />
                <span class="calendar-title-text">Kalender Mengajar: {{ $teacherName }}</span>
            </div>
            <div class="calendar-controls">
                <button wire:click="previousMonth" class="cal-nav-btn" type="button" title="Bulan Sebelumnya">
                    <x-heroicon-m-chevron-left style="width: 16px; height: 16px; flex-shrink: 0; display: inline-block;" />
                </button>
                <span class="calendar-current-month">{{ $monthName }} {{ $year }}</span>
                <button wire:click="nextMonth" class="cal-nav-btn" type="button" title="Bulan Selanjutnya">
                    <x-heroicon-m-chevron-right style="width: 16px; height: 16px; flex-shrink: 0; display: inline-block;" />
                </button>
            </div>
        </div>

        <!-- Day of Week Headers -->
        <div class="calendar-week-grid">
            <div class="week-header-cell">Senin</div>
            <div class="week-header-cell">Selasa</div>
            <div class="week-header-cell">Rabu</div>
            <div class="week-header-cell">Kamis</div>
            <div class="week-header-cell">Jumat</div>
            <div class="week-header-cell">Sabtu</div>
            <div class="week-header-cell">Minggu</div>
        </div>

        <!-- Days Grid -->
        <div class="calendar-days-grid">
            @foreach($days as $dayData)
                @if(is_null($dayData['date']))
                    <div class="calendar-day-cell pad-cell"></div>
                @else
                    <div class="calendar-day-cell {{ $dayData['is_today'] ? 'is-today' : '' }}">
                        <div class="day-number-row">
                            <span class="day-number-label">{{ $dayData['date'] }}</span>
                            @if($dayData['is_today'])
                                <span class="today-dot"></span>
                            @endif
                        </div>
                        
                        <div class="day-schedules-list">
                            @foreach($dayData['schedules'] as $schedule)
                                @if($schedule->classHour?->is_break)
                                    <div class="cal-schedule-item is-break-item" title="Istirahat ({{ substr($schedule->classHour->start_time, 0, 5) }} - {{ substr($schedule->classHour->end_time, 0, 5) }})">
                                        <div class="cal-schedule-class">Istirahat</div>
                                        <div class="cal-schedule-time">
                                            {{ substr($schedule->classHour->start_time, 0, 5) }} - {{ substr($schedule->classHour->end_time, 0, 5) }}
                                        </div>
                                    </div>
                                @else
                                    <div class="cal-schedule-item" title="{{ $schedule->subject?->name }} - {{ $schedule->class?->name }} (Ruang {{ $schedule->room }})">
                                        <div class="cal-schedule-class">{{ $schedule->class?->name }}</div>
                                        <div class="cal-schedule-subject">{{ $schedule->subject?->name }}</div>
                                        <div class="cal-schedule-time">
                                            @if($schedule->classHour)
                                                {{ substr($schedule->classHour->start_time, 0, 5) }} - {{ substr($schedule->classHour->end_time, 0, 5) }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <style>
        .teacher-calendar-container {
            --cal-accent: #1E88E5;
            --cal-accent-hover: #1565C0;
            --cal-accent-light: rgba(30, 136, 229, 0.06);
            --cal-accent-light-border: rgba(30, 136, 229, 0.15);
            
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.025);
            margin-bottom: 24px;
        }

        .dark .teacher-calendar-container {
            border-color: #374151;
            background: #111827;
            box-shadow: none;
        }

        .calendar-header-block {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .calendar-header-block {
            background: #1f2937;
            border-bottom-color: #374151;
        }

        .calendar-title-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .calendar-header-icon {
            width: 24px !important;
            height: 24px !important;
            color: var(--cal-accent) !important;
        }

        .calendar-title-text {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .dark .calendar-title-text {
            color: #f3f4f6;
        }

        .calendar-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cal-nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #4b5563;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .dark .cal-nav-btn {
            border-color: #374151;
            background: #111827;
            color: #d1d5db;
        }

        .cal-nav-btn:hover {
            border-color: var(--cal-accent);
            background: var(--cal-accent-light);
            color: var(--cal-accent);
        }

        .calendar-current-month {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            min-width: 120px;
            text-align: center;
        }

        .dark .calendar-current-month {
            color: #f3f4f6;
        }

        .calendar-week-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .calendar-week-grid {
            background: #1f2937;
            border-bottom-color: #374151;
        }

        .week-header-cell {
            padding: 12px 8px;
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.75px;
        }

        .dark .week-header-cell {
            color: #9ca3af;
        }

        .calendar-days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-auto-rows: minmax(130px, auto);
            background: #e5e7eb;
            gap: 1px;
        }

        .dark .calendar-days-grid {
            background: #374151;
        }

        .calendar-day-cell {
            padding: 10px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .dark .calendar-day-cell {
            background: #1f2937;
        }

        .calendar-day-cell.pad-cell {
            background: #f9fafb;
        }

        .dark .calendar-day-cell.pad-cell {
            background: #111827;
        }

        .day-number-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .day-number-label {
            font-size: 13px;
            font-weight: 700;
            color: #4b5563;
        }

        .dark .day-number-label {
            color: #9ca3af;
        }

        .calendar-day-cell.is-today {
            background: rgba(30, 136, 229, 0.02);
            position: relative;
        }

        .calendar-day-cell.is-today::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--cal-accent);
        }

        .calendar-day-cell.is-today .day-number-label {
            color: var(--cal-accent);
            font-weight: 900;
        }

        .today-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--cal-accent);
            box-shadow: 0 0 8px var(--cal-accent);
        }

        .day-schedules-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .cal-schedule-item {
            padding: 6px 10px;
            border-radius: 6px;
            background: var(--cal-accent);
            color: #ffffff;
            border: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(30, 136, 229, 0.15);
        }

        .dark .cal-schedule-item {
            background: var(--cal-accent);
            color: #ffffff;
            box-shadow: none;
        }

        .cal-schedule-item:hover {
            transform: translateY(-2px);
            background: var(--cal-accent-hover);
            box-shadow: 0 4px 8px rgba(30, 136, 229, 0.3);
        }

        .dark .cal-schedule-item:hover {
            background: var(--cal-accent-hover);
        }

        .cal-schedule-item.is-break-item {
            background: #d97706;
            box-shadow: 0 2px 4px rgba(217, 119, 6, 0.15);
            cursor: default;
        }

        .cal-schedule-item.is-break-item:hover {
            transform: translateY(-2px);
            background: #b45309;
            box-shadow: 0 4px 8px rgba(217, 119, 6, 0.3);
        }

        .dark .cal-schedule-item.is-break-item {
            background: #d97706;
            box-shadow: none;
        }

        .dark .cal-schedule-item.is-break-item:hover {
            background: #b45309;
        }

        .cal-schedule-class {
            font-size: 11px;
            font-weight: 800;
            color: #ffffff;
            text-transform: uppercase;
        }
        
        .dark .cal-schedule-class {
            color: #ffffff;
        }

        .cal-schedule-subject {
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            margin-top: 2px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .cal-schedule-subject {
            color: rgba(255, 255, 255, 0.95);
        }

        .cal-schedule-time {
            font-size: 9px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 3px;
        }

        .dark .cal-schedule-time {
            color: rgba(255, 255, 255, 0.85);
        }
    </style>
</x-filament-widgets::widget>
