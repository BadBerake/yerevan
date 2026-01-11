<?php view('header', ['title' => __('route_planner')]); ?>

<!-- Leaflet & Plugins -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<style>
    .transport-container {
        display: flex;
        height: calc(100vh - 80px); /* Adjust based on header */
        position: relative;
        overflow: hidden;
    }

    /* Sidebar controls */
    .transport-sidebar {
        width: 400px;
        background: #fff;
        z-index: 1000;
        box-shadow: 2px 0 15px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
    }

    .transport-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .transport-header h2 { margin: 0; font-size: 1.5rem; }
    .transport-header p { margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem; }

    .route-form {
        padding: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
    }

    .input-group {
        position: relative;
        margin-bottom: 1rem;
    }

    .input-group i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .route-input {
        width: 100%;
        padding: 12px 12px 12px 40px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s;
        outline: none;
    }

    .route-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-route {
        width: 100%;
        padding: 12px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-route:hover { background: #2563eb; }

    /* Results List */
    .transport-list {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: #f8fafc;
    }

    .route-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .route-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        border-color: #e2e8f0;
    }

    .route-badge {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
    }

    .route-details { flex: 1; }
    .route-name { font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .route-path { font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 6px; }
    .route-meta { margin-top: 8px; font-size: 0.8rem; color: #94a3b8; display: flex; gap: 12px; }
    
    .map-wrapper { flex: 1; position: relative; z-index: 1; }
    #map { width: 100%; height: 100%; }

    /* Mobile */
    @media (max-width: 768px) {
        .transport-container { flex-direction: column; }
        .transport-sidebar { width: 100%; height: 50%; }
        .map-wrapper { height: 50%; }
    }
</style>

<div class="transport-container">
    <div class="transport-sidebar">
        <div class="transport-header">
            <h2>🗺️ <?= __('route_planner') ?></h2>
            <p><?= __('find_best_way') ?></p>
        </div>
        
        <div class="route-form">
            <div class="input-group">
                <i class="fas fa-circle" style="color: #3b82f6; font-size: 0.8rem;"></i>
                <input type="text" id="start-input" class="route-input" placeholder="<?= __('origin_placeholder') ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-map-marker-alt" style="color: #ef4444;"></i>
                <input type="text" id="end-input" class="route-input" placeholder="<?= __('destination_placeholder') ?>">
            </div>
            <button class="btn-route" onclick="findRoute()">
                <i class="fas fa-search-location"></i> <?= __('find_route') ?>
            </button>
        </div>

        <div class="transport-list">
            <h4 style="margin: 0 0 1rem 0; color: #475569; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;"><?= __('popular_routes') ?></h4>
            
            <?php foreach ($routes as $route): ?>
            <div class="route-card" onclick="showRouteOnMap('<?= $route['route_number'] ?>')">
                <div class="route-badge" style="background: <?= $route['color'] ?>;">
                    <?php if ($route['type'] == 'metro') echo 'M'; else echo $route['route_number']; ?>
                </div>
                <div class="route-details">
                    <div class="route-name">
                        <?= ucfirst($route['type']) ?> <?= $route['route_number'] ?>
                    </div>
                    <div class="route-path">
                        <?= $route['origin'] ?> <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i> <?= $route['destination'] ?>
                    </div>
                    <div class="route-meta">
                        <span><i class="far fa-clock"></i> <?= $route['frequency'] ?></span>
                        <span><i class="fas fa-tag"></i> <?= $route['price'] ?></span>
                    </div>
                </div>
                <i class="fas fa-chevron-right" style="color: #cbd5e1;"></i>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="map-wrapper">
        <div id="map"></div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    // Initialize Map
    var map = L.map('map', { zoomControl: false }).setView([40.1872, 44.5152], 13);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Geocoder Instances
    const geocoder = L.Control.Geocoder.nominatim();
    let routingControl = null;

    // Search Function
    async function findRoute() {
        const startQuery = document.getElementById('start-input').value;
        const endQuery = document.getElementById('end-input').value;

        if (!startQuery || !endQuery) {
            alert("<?= __('enter_origin_dest') ?>");
            return;
        }

        // Simple geocoding helper using Nominatim API (Free)
        async function geocode(query) {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ' Yerevan')}`);
            const data = await response.json();
            return data[0];
        }

        const startLoc = await geocode(startQuery);
        const endLoc = await geocode(endQuery);

        if (!startLoc || !endLoc) {
            alert("<?= __('location_not_found') ?>");
            return;
        }

        // Remove previous route
        if (routingControl) {
            map.removeControl(routingControl);
        }

        // Add Routing Machine (OSRM)
        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(startLoc.lat, startLoc.lon),
                L.latLng(endLoc.lat, endLoc.lon)
            ],
            routeWhileDragging: true,
            geocoder: L.Control.Geocoder.nominatim(),
            show: false // Hide default instructions panel to keep UI clean
        }).addTo(map);
        
        // Custom Popup for Public Transport Fallback
        L.popup()
            .setLatLng([(parseFloat(startLoc.lat) + parseFloat(endLoc.lat))/2, (parseFloat(startLoc.lon) + parseFloat(endLoc.lon))/2])
            .setContent(`
                <div style="text-align: center;">
                    <strong>🗺️ <?= __('route_visualized') ?></strong><br>
                    <small style="color: grey;"><?= __('walking_driving_path') ?></small><br><br>
                    <a href="https://yandex.com/maps/10262/yerevan/?rtext=${startLoc.lat},${startLoc.lon}~${endLoc.lat},${endLoc.lon}&rtt=mt" 
                       target="_blank"  
                       style="background: #fc3f1d; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-block;">
                       🚍 <?= __('see_bus_connections') ?>
                    </a>
                </div>
            `)
            .openOn(map);
    }

    // Mock functionality for Popular Routes "Show on Map"
    function showRouteOnMap(routeNum) {
        // Hardcoded coordinates for demo
        const routes = {
            '100': [[40.1858, 44.5150], [40.1553, 44.3980]], // Center -> Airport
            '1': [[40.1983, 44.4752], [40.1804, 44.5721]],   // Ajapnyak -> Jrvej
            'Metro': [[40.1950, 44.4886], [40.1507, 44.4849]] // Barekamutyun -> Garegin Nzhdeh
        };

        const points = routes[routeNum];
        if (points) {
            if (routingControl) map.removeControl(routingControl);
            
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(points[0]),
                    L.latLng(points[1])
                ],
                show: false,
                lineOptions: {
                    styles: [{color: 'red', opacity: 0.6, weight: 4}]
                }
            }).addTo(map);
        }
    }
</script>

<?php view('footer'); ?>
