<div
    x-data="mapPicker({
        latitude: $wire.entangle('data.latitude'),
        longitude: $wire.entangle('data.longitude'),
        radius: $wire.entangle('data.radius_meters')
    })"
    class="space-y-4 col-span-full"
>
    <div class="flex flex-col gap-2">
        <span class="text-sm font-medium text-gray-950 dark:text-white">
            Pilih Lokasi di Peta
        </span>
        
        <!-- Map Container -->
        <div
            id="map-picker-container"
            class="h-80 w-full rounded-xl border border-gray-300 dark:border-gray-700 shadow-sm overflow-hidden"
            style="min-height: 350px; position: relative; z-index: 1;"
            wire:ignore
        ></div>
    </div>
    
    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-start gap-2">
        <svg class="text-blue-500" style="width: 16px; height: 16px; min-width: 16px; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Klik pada peta atau geser penanda (marker) untuk menentukan koordinat sekolah. Lingkaran biru menandakan batas radius presensi (geofencing).</span>
    </div>

    <!-- Leaflet Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        /* Fix marker image paths when loaded from CDN on some browsers */
        .leaflet-default-icon-path {
            background-image: url(https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png);
        }
    </style>

    <script>
        (function () {
            const initComponent = () => {
                // Hindari registrasi ganda
                if (window.Alpine.data('mapPicker')) return;

                window.Alpine.data('mapPicker', (config) => ({
                    latitude: config.latitude,
                    longitude: config.longitude,
                    radius: config.radius,
                    map: null,
                    marker: null,
                    radiusCircle: null,

                    init() {
                        this.initMap();
                    },

                    initMap() {
                        if (typeof L === 'undefined') {
                            setTimeout(() => this.initMap(), 100);
                            return;
                        }

                        let defaultLat = parseFloat(this.latitude) || -6.200000;
                        let defaultLng = parseFloat(this.longitude) || 106.816666;
                        let defaultRadius = parseFloat(this.radius) || 100;

                        // Inisialisasi Map
                        this.map = L.map('map-picker-container').setView([defaultLat, defaultLng], 16);

                        // Gunakan OpenStreetMap tiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> kontributor'
                        }).addTo(this.map);

                        // Buat Penanda (Marker) yang dapat digeser
                        this.marker = L.marker([defaultLat, defaultLng], {
                            draggable: true
                        }).addTo(this.map);

                        // Buat Lingkaran Radius untuk Geofencing
                        this.radiusCircle = L.circle([defaultLat, defaultLng], {
                            radius: defaultRadius,
                            color: '#3b82f6',
                            fillColor: '#3b82f6',
                            fillOpacity: 0.15,
                            weight: 1.5
                        }).addTo(this.map);

                        // Ketika penanda digeser
                        this.marker.on('dragend', (e) => {
                            let position = this.marker.getLatLng();
                            this.updateCoordinates(position.lat, position.lng);
                        });

                        // Ketika peta diklik
                        this.map.on('click', (e) => {
                            this.marker.setLatLng(e.latlng);
                            this.updateCoordinates(e.latlng.lat, e.latlng.lng);
                        });

                        // Pantau perubahan koordinat dari input eksternal/manual
                        this.$watch('latitude', (value) => {
                            let lat = parseFloat(value);
                            let lng = parseFloat(this.longitude);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                let newLatLng = new L.LatLng(lat, lng);
                                this.marker.setLatLng(newLatLng);
                                this.radiusCircle.setLatLng(newLatLng);
                                this.map.setView(newLatLng, this.map.getZoom());
                            }
                        });

                        this.$watch('longitude', (value) => {
                            let lat = parseFloat(this.latitude);
                            let lng = parseFloat(value);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                let newLatLng = new L.LatLng(lat, lng);
                                this.marker.setLatLng(newLatLng);
                                this.radiusCircle.setLatLng(newLatLng);
                                this.map.setView(newLatLng, this.map.getZoom());
                            }
                        });

                        this.$watch('radius', (value) => {
                            let r = parseFloat(value) || 0;
                            this.radiusCircle.setRadius(r);
                        });

                        // Trigger penyesuaian ukuran peta setelah inisialisasi agar ubin tidak abu-abu
                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 500);
                    },

                    updateCoordinates(lat, lng) {
                        let formattedLat = lat.toFixed(8);
                        let formattedLng = lng.toFixed(8);

                        // 1. Update Alpine/Livewire state (via entangle)
                        this.latitude = formattedLat;
                        this.longitude = formattedLng;

                        // 2. Dual-binding Fallback: Langsung perbarui elemen input DOM dan picu event
                        let latInput = document.querySelector('[name$="latitude"]');
                        let lngInput = document.querySelector('[name$="longitude"]');

                        if (latInput) {
                            latInput.value = formattedLat;
                            latInput.dispatchEvent(new Event('input'));
                            latInput.dispatchEvent(new Event('change'));
                        }

                        if (lngInput) {
                            lngInput.value = formattedLng;
                            lngInput.dispatchEvent(new Event('input'));
                            lngInput.dispatchEvent(new Event('change'));
                        }

                        if (this.radiusCircle) {
                            this.radiusCircle.setLatLng([lat, lng]);
                        }
                    }
                }));
            };

            if (window.Alpine) {
                initComponent();
            } else {
                document.addEventListener('alpine:init', initComponent);
            }
        })();
    </script>
</div>
