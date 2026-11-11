<x-filament-panels::page>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <style>
            .gis-wrap { display: grid; gap: 1.25rem; }
            .gis-card { background: #fff; border-radius: .75rem; box-shadow: 0 0 0 1px rgba(17,24,39,.06), 0 1px 2px rgba(17,24,39,.06); overflow: hidden; }
            .dark .gis-card { background: rgb(var(--gray-900)); box-shadow: 0 0 0 1px rgba(255,255,255,.1); }
            .gis-card-header { padding: .8rem 1rem; border-bottom: 1px solid rgb(var(--gray-200)); display: flex; align-items: center; justify-content: space-between; gap: .5rem; flex-wrap: wrap; }
            .dark .gis-card-header { border-color: rgba(255,255,255,.1); }
            .gis-card-title { font-size: .875rem; font-weight: 700; color: rgb(var(--gray-950)); }
            .dark .gis-card-title { color: #fff; }
            .gis-card-body { padding: 1rem; }
            .gis-stats { display: grid; gap: 1rem; grid-template-columns: repeat(4, minmax(0, 1fr)); }
            @media (max-width: 1024px) { .gis-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (max-width: 640px) { .gis-stats { grid-template-columns: 1fr; } }
            .gis-stat { border-radius: .6rem; padding: .85rem 1rem; background: rgb(var(--gray-50)); border: 1px solid rgb(var(--gray-200)); }
            .dark .gis-stat { background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.1); }
            .gis-stat-label { font-size: .7rem; color: rgb(var(--gray-500)); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
            .gis-stat-value { font-size: 1.5rem; font-weight: 800; color: rgb(var(--gray-950)); margin-top: .2rem; }
            .dark .gis-stat-value { color: #fff; }
            .gis-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
            .gis-table th { background: rgb(var(--gray-50)); padding: .55rem .6rem; text-align: left; font-weight: 700; color: rgb(var(--gray-600)); white-space: nowrap; border-bottom: 1px solid rgb(var(--gray-200)); }
            .dark .gis-table th { background: rgba(255,255,255,.04); color: rgb(var(--gray-300)); border-color: rgba(255,255,255,.1); }
            .gis-table td { padding: .5rem .6rem; border-bottom: 1px solid rgb(var(--gray-100)); color: rgb(var(--gray-700)); }
            .dark .gis-table td { color: rgb(var(--gray-200)); border-color: rgba(255,255,255,.08); }
            .gis-table td.right { text-align: right; }
            .gis-table td.center { text-align: center; }
            .gis-badge { display: inline-flex; align-items: center; border-radius: 9999px; padding: .1rem .45rem; font-size: .65rem; font-weight: 700; }
            .gis-badge-success { background: rgb(var(--success-50)); color: rgb(var(--success-700)); }
            .gis-badge-info { background: rgb(var(--info-50)); color: rgb(var(--info-700)); }
            .dark .gis-badge-success { background: rgba(34,197,94,.2); color: #86efac; }
            .dark .gis-badge-info { background: rgba(59,130,246,.2); color: #93c5fd; }
            #map { height: 500px; width: 100%; border-radius: .6rem; z-index: 10; }
            @keyframes pulse-ring {
                0% { transform: scale(0.95); opacity: 0.8; }
                50% { transform: scale(1.3); opacity: 0.4; }
                100% { transform: scale(0.95); opacity: 0.8; }
            }
            .sppg-hub-pulse {
                animation: pulse-ring 2s infinite ease-in-out;
            }
        </style>
    @endpush

    <div class="gis-wrap">
        <div style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:1rem">
            <div>
                <div style="font-size:1.4rem;font-weight:800;color:rgb(var(--gray-950))" class="dark:text-white">
                    {{ $profile->nama }} &mdash; Pusat Distribusi (Hub)
                </div>
                <div style="margin-top:.2rem;font-size:.85rem;color:rgb(var(--gray-500))">
                    Koordinat Pusat SPPG: <strong>{{ $profile->latitude ?? '-7.2275' }}, {{ $profile->longitude ?? '107.8720' }}</strong> &middot; {{ $profile->alamat ?? 'Kecamatan Samarang, Garut' }}
                </div>
            </div>

            <div style="display:flex;gap:.5rem">
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-printer"
                    size="sm"
                    wire:click="cetakPdf"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="cetakPdf">Cetak PDF Distribusi</span>
                    <span wire:loading wire:target="cetakPdf">Memproses...</span>
                </x-filament::button>
            </div>
        </div>

        <div class="gis-stats">
            <div class="gis-stat">
                <div class="gis-stat-label">Total Lembaga Tujuan</div>
                <div class="gis-stat-value">{{ $totalInstitutions }} Lembaga</div>
            </div>
            <div class="gis-stat">
                <div class="gis-stat-label">Total Penerima Manfaat</div>
                <div class="gis-stat-value">{{ $totalBeneficiaries }} Orang</div>
            </div>
            <div class="gis-stat">
                <div class="gis-stat-label">Jarak Distribusi Terjauh</div>
                <div class="gis-stat-value">{{ $maxDistance }} KM</div>
            </div>
            <div class="gis-stat">
                <div class="gis-stat-label">Rata-rata Jarak Pengantaran</div>
                <div class="gis-stat-value">{{ $avgDistance }} KM</div>
            </div>
        </div>

        <div class="gis-card">
            <div class="gis-card-header">
                <div>
                    <div class="gis-card-title">Peta Cabang &amp; Rute Pengantaran (GIS Central Hub)</div>
                    <div style="font-size:.75rem;color:rgb(var(--gray-500));margin-top:2px;">Titik hijau (Rumah) = Pusat SPPG &middot; Titik biru/hijau = Sekolah &amp; Posyandu</div>
                </div>
                <div style="display:flex;gap:.5rem;align-items:center;">
                    <button type="button" id="start-sim" style="background:#059669;color:#fff;padding:.35rem .75rem;border-radius:.4rem;font-size:.75rem;font-weight:bold;cursor:pointer;border:0;box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                        🚚 Mulai Simulasi Pengantaran
                    </button>
                    <button type="button" id="stop-sim" style="background:#dc2626;color:#fff;padding:.35rem .75rem;border-radius:.4rem;font-size:.75rem;font-weight:bold;cursor:pointer;border:0;display:none;box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                        ⏹ Reset Simulasi
                    </button>
                </div>
            </div>
            <div class="gis-card-body">
                <div id="map"></div>
            </div>
        </div>

        <div class="gis-card">
            <div class="gis-card-header">
                <div class="gis-card-title">Daftar Rute &amp; Jarak Pengantaran per Lembaga</div>
                <div style="font-size:.75rem;color:rgb(var(--gray-500))">Urutan berdasarkan kedekatan jarak dari pusat SPPG</div>
            </div>
            <div style="overflow-x:auto">
                <table class="gis-table">
                    <thead>
                        <tr>
                            <th style="width:2.5rem">No</th>
                            <th>Nama Lembaga / Posyandu</th>
                            <th>Tipe</th>
                            <th>Alamat</th>
                            <th>Koordinat (Lat, Lng)</th>
                            <th class="right">Jarak (KM)</th>
                            <th class="right">Penerima Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($institutions as $inst)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $inst['nama'] }}</strong>
                                    @if ($inst['jenjang'])
                                        <span class="gis-badge gis-badge-info" style="margin-left:.3rem">{{ $inst['jenjang'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="gis-badge {{ $inst['tipe'] === 'sekolah' ? 'gis-badge-info' : 'gis-badge-success' }}">
                                        {{ ucfirst($inst['tipe']) }}
                                    </span>
                                </td>
                                <td>{{ $inst['alamat'] ?? '-' }}</td>
                                <td>
                                    @if ($inst['latitude'] && $inst['longitude'])
                                        <code>{{ $inst['latitude'] }}, {{ $inst['longitude'] }}</code>
                                    @else
                                        <span style="color:rgb(var(--gray-400))">Belum diset</span>
                                    @endif
                                </td>
                                <td class="right" style="font-weight:700">
                                    {{ $inst['distance'] !== null ? $inst['distance'] . ' KM' : '-' }}
                                </td>
                                <td class="right" style="font-weight:700">
                                    {{ $inst['beneficiaries_count'] }} orang
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="center" style="color:rgb(var(--gray-500))">Belum ada data lembaga aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sppgLat = {{ $profile->latitude ?? -7.2275 }};
                const sppgLng = {{ $profile->longitude ?? 107.8720 }};
                const sppgName = "{{ $profile->nama ?? 'SPPG Samarang' }}";

                const map = L.map('map').setView([sppgLat, sppgLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Provinsi boundary (macro scope)
                const provBoundary = @json($provinsiBoundary);
                if (provBoundary && provBoundary.length > 0) {
                    L.polygon(provBoundary, {
                        color: '#8b5cf6',
                        weight: 2,
                        opacity: 0.7,
                        fillColor: '#c084fc',
                        fillOpacity: 0.04,
                        dashArray: '10, 6'
                    }).addTo(map).bindPopup('<b>Batas Wilayah Provinsi ({{ $profile->provinsi ?? "Jawa Barat" }})</b><br>cahyadsn/wilayah_boundaries');
                }

                // Kabupaten boundary (meso scope)
                const kabBoundary = @json($kabupatenBoundary);
                if (kabBoundary && kabBoundary.length > 0) {
                    L.polygon(kabBoundary, {
                        color: '#0284c7',
                        weight: 3,
                        opacity: 0.85,
                        fillColor: '#38bdf8',
                        fillOpacity: 0.1,
                        dashArray: '6, 4'
                    }).addTo(map).bindPopup('<b>Batas Wilayah Kabupaten (Kabupaten {{ $profile->kabupaten ?? "Garut" }})</b><br>cahyadsn/wilayah_boundaries');
                }

                // Kecamatan boundary (micro scope - highlighted)
                const kecBoundary = @json($kecamatanBoundary);
                if (kecBoundary && kecBoundary.length > 0) {
                    L.polygon(kecBoundary, {
                        color: '#059669',
                        weight: 4,
                        opacity: 0.95,
                        fillColor: '#10b981',
                        fillOpacity: 0.22,
                        dashArray: null
                    }).addTo(map).bindPopup('<b>Batas Wilayah Administrasi Kecamatan (Kecamatan {{ $profile->kecamatan ?? "Samarang" }})</b><br>Kabupaten {{ $profile->kabupaten ?? "Garut" }}');
                }

                // SPPG Central Marker with pulse ring
                const sppgIcon = L.divIcon({
                    className: 'custom-sppg-marker',
                    html: '<div style="position:relative;"><div style="position:absolute;top:-6px;left:-6px;width:44px;height:44px;border-radius:50%;background:#059669;opacity:0.3;" class="sppg-hub-pulse"></div><div style="background:#059669;color:#fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-weight:bold;box-shadow:0 3px 8px rgba(0,0,0,0.4);border:2px solid #fff;position:relative;z-index:2;">🏠</div></div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                const sppgMarker = L.marker([sppgLat, sppgLng], { icon: sppgIcon }).addTo(map);
                sppgMarker.bindPopup(`<b>${sppgName} (Pusat SPPG Hub)</b><br>Koordinat: ${sppgLat}, ${sppgLng}`);

                const institutions = @json($institutions);
                const latLngs = [[sppgLat, sppgLng]];
                let instMarkers = [];

                institutions.forEach(inst => {
                    if (inst.latitude && inst.longitude) {
                        const instLat = parseFloat(inst.latitude);
                        const instLng = parseFloat(inst.longitude);
                        latLngs.push([instLat, instLng]);

                        const isSekolah = inst.tipe === 'sekolah';
                        const instIcon = L.divIcon({
                            className: 'custom-inst-marker',
                            html: `<div style="background:${isSekolah ? '#2563eb' : '#10b981'};color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3);border:2px solid #fff;">📍</div>`,
                            iconSize: [28, 28],
                            iconAnchor: [14, 14]
                        });

                        const marker = L.marker([instLat, instLng], { icon: instIcon }).addTo(map);
                        marker.bindPopup(`
                            <b>${inst.nama}</b><br>
                            Tipe: ${inst.tipe} ${inst.jenjang ? '(' + inst.jenjang + ')' : ''}<br>
                            Jarak ke SPPG: <b>${inst.distance !== null ? inst.distance + ' KM' : '-'}</b><br>
                            Penerima Aktif: <b>${inst.beneficiaries_count} orang</b>
                        `);
                        instMarkers.push({ marker, lat: instLat, lng: instLng, data: inst });

                        // Draw routing line from SPPG to Institution
                        L.polyline([[sppgLat, sppgLng], [instLat, instLng]], {
                            color: isSekolah ? '#3b82f6' : '#10b981',
                            weight: 2.5,
                            opacity: 0.65,
                            dashArray: '5, 5'
                        }).addTo(map);
                    }
                });

                if (latLngs.length > 1) {
                    const bounds = L.latLngBounds(latLngs);
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                // Delivery Animation Logic
                let activeAnimations = [];

                function startSimulation() {
                    // Reset any running animations
                    stopSimulation();

                    document.getElementById('start-sim').style.display = 'none';
                    document.getElementById('stop-sim').style.display = 'inline-block';

                    instMarkers.forEach((item, index) => {
                        const carIcon = L.divIcon({
                            className: 'custom-car-marker',
                            html: '<div style="background:#f59e0b;color:#fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:15px;box-shadow:0 3px 8px rgba(0,0,0,0.4);border:2px solid #fff;">🚚</div>',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        });

                        const carMarker = L.marker([sppgLat, sppgLng], { icon: carIcon }).addTo(map);
                        activeAnimations.push(carMarker);

                        const duration = 3000; // 3 seconds per delivery run
                        const startTime = performance.now() + (index * 400); // Staggered delay

                        function animateFrame(now) {
                            if (now < startTime) {
                                activeAnimations.push(requestAnimationFrame(animateFrame));
                                return;
                            }

                            const elapsed = now - startTime;
                            const progress = Math.min(elapsed / duration, 1);

                            // Smooth step
                            const ease = progress < 0.5 ? 2 * progress * progress : 1 - Math.pow(-2 * progress + 2, 2) / 2;

                            const curLat = sppgLat + (item.lat - sppgLat) * ease;
                            const curLng = sppgLng + (item.lng - sppgLng) * ease;

                            carMarker.setLatLng([curLat, curLng]);

                            if (progress < 1) {
                                activeAnimations.push(requestAnimationFrame(animateFrame));
                            } else {
                                // Reached destination
                                carMarker.setIcon(L.divIcon({
                                    className: 'custom-car-done',
                                    html: '<div style="background:#10b981;color:#fff;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 3px 8px rgba(0,0,0,0.4);border:2px solid #fff;">✅</div>',
                                    iconSize: [30, 30],
                                    iconAnchor: [15, 15]
                                }));
                                carMarker.bindPopup(`<b>📦 Makanan Bergizi Tiba!</b><br>${item.data.nama}<br>Jarak: ${item.data.distance} KM`).openPopup();
                            }
                        }

                        activeAnimations.push(requestAnimationFrame(animateFrame));
                    });
                }

                function stopSimulation() {
                    activeAnimations.forEach(anim => {
                        if (typeof anim === 'number') {
                            cancelAnimationFrame(anim);
                        } else if (anim && anim.remove) {
                            anim.remove();
                        }
                    });
                    activeAnimations = [];
                    document.getElementById('stop-sim').style.display = 'none';
                    document.getElementById('start-sim').style.display = 'inline-block';
                }

                document.getElementById('start-sim').addEventListener('click', startSimulation);
                document.getElementById('stop-sim').addEventListener('click', function() {
                    stopSimulation();
                });

                // Auto start simulation after 1.5 seconds
                setTimeout(startSimulation, 1500);
            });
        </script>
    @endpush
</x-filament-panels::page>
