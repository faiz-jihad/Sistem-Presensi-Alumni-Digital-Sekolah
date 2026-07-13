<x-filament-widgets::widget>
    @php
        $data = $this->getData();
    @endphp

    <div class="schedule-stats-widget-container">
        <div class="schedule-stats-grid">
            
            <!-- Card 1: Sekolah -->
            <div class="schedule-stat-card">
                <div class="schedule-stat-icon-bg">
                    <x-heroicon-o-building-library class="schedule-stat-icon" />
                </div>
                <div class="schedule-stat-info">
                    <span class="schedule-stat-label">Total Sekolah</span>
                    <span class="schedule-stat-value">{{ $data['total_schools'] }}</span>
                </div>
                <div class="schedule-stat-pattern"></div>
            </div>

            <!-- Card 2: Kelas -->
            <div class="schedule-stat-card">
                <div class="schedule-stat-icon-bg">
                    <x-heroicon-o-square-3-stack-3d class="schedule-stat-icon" />
                </div>
                <div class="schedule-stat-info">
                    <span class="schedule-stat-label">Total Kelas</span>
                    <span class="schedule-stat-value">{{ $data['total_classes'] }}</span>
                </div>
                <div class="schedule-stat-pattern"></div>
            </div>

            <!-- Card 3: Guru -->
            <div class="schedule-stat-card">
                <div class="schedule-stat-icon-bg">
                    <x-heroicon-o-user-group class="schedule-stat-icon" />
                </div>
                <div class="schedule-stat-info">
                    <span class="schedule-stat-label">Total Guru</span>
                    <span class="schedule-stat-value">{{ $data['total_teachers'] }}</span>
                </div>
                <div class="schedule-stat-pattern"></div>
            </div>

            <!-- Card 4: Mata Pelajaran -->
            <div class="schedule-stat-card">
                <div class="schedule-stat-icon-bg">
                    <x-heroicon-o-book-open class="schedule-stat-icon" />
                </div>
                <div class="schedule-stat-info">
                    <span class="schedule-stat-label">Mata Pelajaran</span>
                    <span class="schedule-stat-value">{{ $data['total_subjects'] }}</span>
                </div>
                <div class="schedule-stat-pattern"></div>
            </div>

        </div>
    </div>

    <style>
        .schedule-stats-widget-container {
            margin-bottom: 24px;
        }

        .schedule-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 640px) {
            .schedule-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .schedule-stat-card {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 24px;
            border-radius: 20px;
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            box-shadow: 0 10px 25px -5px rgba(30, 136, 229, 0.3), 0 8px 10px -6px rgba(30, 136, 229, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: default;
        }

        .schedule-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -5px rgba(30, 136, 229, 0.45), 0 15px 20px -8px rgba(30, 136, 229, 0.35);
        }

        .schedule-stat-icon-bg {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.4s ease;
        }

        .schedule-stat-card:hover .schedule-stat-icon-bg {
            transform: rotate(-6deg) scale(1.1);
            background: rgba(255, 255, 255, 0.28);
        }

        .schedule-stat-icon {
            width: 28px !important;
            height: 28px !important;
            color: #ffffff !important;
        }

        .schedule-stat-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            z-index: 1;
        }

        .schedule-stat-label {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .schedule-stat-value {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            color: #ffffff;
        }

        /* Decorative circle background patterns */
        .schedule-stat-pattern {
            position: absolute;
            top: 0;
            right: 0;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            transform: translate(25px, -25px);
            pointer-events: none;
            transition: all 0.4s ease;
        }

        .schedule-stat-card:hover .schedule-stat-pattern {
            transform: translate(15px, -15px) scale(1.3);
            background: rgba(255, 255, 255, 0.08);
        }
    </style>
</x-filament-widgets::widget>
