<?php view('header', ['title' => __('route_planner')]); ?>

<!-- Leaflet Routing Plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

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

        <div id="route-list-view" class="transport-list">
            <h4 style="margin: 0 0 1rem 0; color: #475569; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;"><?= __('popular_routes') ?></h4>
            
            <?php foreach ($routes as $route): ?>
            <div class="route-card" onclick="openRouteDetails(<?= $route['id'] ?>)">
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
                </div>
                <i class="fas fa-chevron-right" style="color: #cbd5e1;"></i>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="route-detail-view" class="transport-list" style="display: none;">
            <button onclick="closeRouteDetails()" style="background: none; border: none; color: #64748b; cursor: pointer; display: flex; align-items: center; gap: 5px; margin-bottom: 1rem; padding: 0; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Back to Routes
            </button>
            <div id="detail-content"></div>
        </div>
    </div>

    <div class="map-wrapper">
        <div id="map"></div>
    </div>
</div>

<script>
    // Pass routes data to JS
    const transportRoutes = <?= json_encode($routes) ?>;
    let mapInstance = null;
    let routingControl = null;

    // Initialize map with Yerevango Maps library
    document.addEventListener('DOMContentLoaded', function() {
        mapInstance = new YerevangoMap('map', {
            center: [40.1872, 44.5152],
            zoom: 13,
            enableClustering: false, // Disable for transport view
            enableSearch: true,
            enableFullscreen: true,
            enableLocate: true,
            enableLayerSwitch: true,
            userMarker: false,
            routingEnabled: true
        });
    });

    function openRouteDetails(id) {
        const route = transportRoutes.find(r => r.id == id);
        if (!route) return;

        // Switch Views
        document.getElementById('route-list-view').style.display = 'none';
        document.getElementById('route-detail-view').style.display = 'block';

        // Parse Stops
        let stops = [];
        try {
            stops = JSON.parse(route.stops);
        } catch (e) {
            stops = route.stops.split(',');
        }

        // Build Stops Timeline HTML
        let stopsHtml = '';
        stops.forEach((stop, index) => {
            stopsHtml += `
            <div style="display: flex; gap: 15px; position: relative;">
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 12px; height: 12px; background: ${route.color}; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 2px ${route.color}; z-index: 2;"></div>
                    ${index !== stops.length - 1 ? `<div style="width: 2px; flex: 1; background: #e2e8f0; margin-top: -2px; margin-bottom: -2px;"></div>` : ''}
                </div>
                <div style="padding-bottom: 20px;">
                    <div style="font-weight: 500; color: #334155;">${stop.trim()}</div>
                </div>
            </div>`;
        });

        // Render Content
        const content = document.getElementById('detail-content');
        content.innerHTML = `
            <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="route-badge" style="background: ${route.color}; width: 56px; height: 56px; font-size: 1.3rem;">
                        ${route.type == 'metro' ? 'M' : route.route_number}
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.2rem; color: #1e293b;">${route.type.charAt(0).toUpperCase() + route.type.slice(1)} ${route.route_number}</h2>
                        <div style="color: #64748b; font-size: 0.9rem;">${route.origin} - ${route.destination}</div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #f8fafc; padding: 1rem; border-radius: 8px;">
                    <div>
                        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Price</div>
                        <div style="font-weight: 600; color: #334155;">${route.price}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Frequency</div>
                        <div style="font-weight: 600; color: #334155;">${route.frequency}</div>
                    </div>
                    <div style="grid-column: span 2;">
                        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Working Hours</div>
                        <div style="font-weight: 600; color: #334155;">${route.working_hours}</div>
                    </div>
                </div>
            </div>
            
            <h3 style="font-size: 1rem; color: #475569; margin-bottom: 1rem;">Route Stops</h3>
            <div style="padding-left: 5px;">
                ${stopsHtml}
            </div>
        `;

        showRouteOnMap(route.route_number);
    }

    function closeRouteDetails() {
        document.getElementById('route-list-view').style.display = 'block';
        document.getElementById('route-detail-view').style.display = 'none';
    }

    // Search Function
    async function findRoute() {
        const startQuery = document.getElementById('start-input').value;
        const endQuery = document.getElementById('end-input').value;

        if (!startQuery || !endQuery) {
            alert("<?= __('enter_origin_dest') ?>");
            return;
        }

        // Simple geocoding helper using Nominatim API
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

        // Use Yerevango Map routing
        mapInstance.clearRouting();
        routingControl = mapInstance.addRouting([
            L.latLng(startLoc.lat, startLoc.lon),
            L.latLng(endLoc.lat, endLoc.lon)
        ]);
        
        // Custom Popup for Public Transport
        const map = mapInstance.getMap();
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
            mapInstance.clearRouting();
            
            routingControl = mapInstance.addRouting([
                L.latLng(points[0]),
                L.latLng(points[1])
            ]);
        }
    }
</script>

<?php view('footer'); ?>
