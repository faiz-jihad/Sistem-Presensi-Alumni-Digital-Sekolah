<div class="space-y-2">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div x-data="leafletMapPicker({
        latitude: $wire.entangle('data.latitude'),
        longitude: $wire.entangle('data.longitude'),
        radius: $wire.entangle('data.radius_meters')
    })"
         x-init="initMap()"
         class="w-full relative"
         wire:ignore>
        
        <div x-ref="mapContainer" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 shadow-sm" style="height: 380px; z-index: 1;"></div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    // Check if component already registered to prevent duplicate declaration errors
    if (Alpine.components && Alpine.components['leafletMapPicker']) {
        return;
    }
    
    Alpine.data('leafletMapPicker', (config) => ({
        latitude: config.latitude,
        longitude: config.longitude,
        radius: config.radius,
        map: null,
        marker: null,
        circle: null,
        
        initMap() {
            if (typeof L === 'undefined') {
                setTimeout(() => this.initMap(), 100);
                return;
            }
            
            // Set default coordinates if not present (Jakarta by default)
            const initialLat = parseFloat(this.latitude) || -6.2088;
            const initialLng = parseFloat(this.longitude) || 106.8456;
            const zoomLevel = this.latitude && this.longitude ? 16 : 13;
            
            this.map = L.map(this.$refs.mapContainer).setView([initialLat, initialLng], zoomLevel);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);
            
            // Marker
            this.marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(this.map);
            
            // Marker Drag Event
            this.marker.on('dragend', () => {
                const position = this.marker.getLatLng();
                this.latitude = position.lat.toFixed(6);
                this.longitude = position.lng.toFixed(6);
                this.updateCircle();
            });
            
            // Map Click Event
            this.map.on('click', (e) => {
                const position = e.latlng;
                this.marker.setLatLng(position);
                this.latitude = position.lat.toFixed(6);
                this.longitude = position.lng.toFixed(6);
                this.updateCircle();
            });
            
            // Circle
            this.updateCircle();
            
            // Watchers for inputs change
            this.$watch('latitude', (val) => {
                const lat = parseFloat(val);
                const lng = parseFloat(this.longitude);
                if (!isNaN(lat) && !isNaN(lng)) {
                    const newLatLng = new L.LatLng(lat, lng);
                    this.marker.setLatLng(newLatLng);
                    this.map.panTo(newLatLng);
                    this.updateCircle();
                }
            });
            
            this.$watch('longitude', (val) => {
                const lat = parseFloat(this.latitude);
                const lng = parseFloat(val);
                if (!isNaN(lat) && !isNaN(lng)) {
                    const newLatLng = new L.LatLng(lat, lng);
                    this.marker.setLatLng(newLatLng);
                    this.map.panTo(newLatLng);
                    this.updateCircle();
                }
            });

            this.$watch('radius', (val) => {
                this.updateCircle();
            });

            // Trigger map resize check to fix render bugs inside hidden tabs or containers
            setTimeout(() => {
                this.map.invalidateSize();
            }, 300);
        },
        
        updateCircle() {
            const lat = parseFloat(this.latitude) || -6.2088;
            const lng = parseFloat(this.longitude) || 106.8456;
            const radiusValue = parseFloat(this.radius) || 100;
            
            if (this.circle) {
                this.map.removeLayer(this.circle);
            }
            
            this.circle = L.circle([lat, lng], {
                color: '#2563eb', // Blue-600
                fillColor: '#3b82f6', // Blue-500
                fillOpacity: 0.2,
                radius: radiusValue
            }).addTo(this.map);
        }
    }));
});
</script>
