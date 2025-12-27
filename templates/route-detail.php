<?php view('header', ['title' => $route['name']]); ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<style>
    /* Custom Styling for Directions Panel */
    .leaflet-routing-container {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        border: none !important;
        font-family: inherit !important;
    }
    .leaflet-routing-alt {
        max-height: 500px !important;
        overflow-y: auto !important;
    }
    .leaflet-routing-alt h2 { font-size: 1rem !important; }
    .directions-container {
        margin-top: 2rem;
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: none;
    }
    .directions-title {
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>
<div class="route-header" style="background: white; padding: 3rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <span style="background: #eff6ff; color: #3b82f6; padding: 6px 14px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
                    <?= htmlspecialchars($route['interest_tag'] ?: 'Explore') ?>
                </span>
                <span style="color: #94a3b8; font-size: 0.9rem;">• <?= count($stops) ?> <?= __('locations') ?></span>
            </div>
            <h1 style="font-size: 2.8rem; font-weight: 800; margin: 0 0 1rem 0; color: #1e293b;"><?= htmlspecialchars(Lang::t($route['name_translations'], $route['name'])) ?></h1>
            <p style="font-size: 1.1rem; color: #64748b; margin: 0; line-height: 1.6; max-width: 800px;"><?= htmlspecialchars(Lang::t($route['description_translations'], $route['description'])) ?></p>
        </div>
        <div style="display: flex; gap: 1.5rem; margin-bottom: 10px;">
            <div style="text-align: center;">
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Time</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: #1e293b;">🕑 <?= $route['estimated_time'] ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Difficulty</div>
                <div style="font-weight: 700; font-size: 1.1rem; color: #1e293b;"><?= ucfirst($route['difficulty']) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="route-content" style="background: #f8fafc; padding: 3rem 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 3rem; align-items: start;">
            
            <!-- Map Section -->
            <div style="position: sticky; top: 100px;">
                <div style="position: relative;">
                    <div id="routeMap" style="height: 600px; border-radius: 24px; box-shadow: var(--shadow-lg); border: 4px solid white;"></div>
                    <div style="position: absolute; bottom: 20px; right: 20px; z-index: 1000; background: white; padding: 10px 15px; border-radius: 12px; box-shadow: var(--shadow-md); font-size: 0.8rem; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px;">
                        <span style="color: #3b82f6;">●</span> <?= __('live_gps_path') ?>
                    </div>
                </div>
            </div>

            <!-- Steps Section -->
            <div>
                <h3 style="margin: 0 0 2rem 0; display: flex; align-items: center; gap: 12px;">
                    <span style="background: var(--primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">🚩</span>
                    <?= __('tour_routes') ?>
                </h3>
                
                <div class="steps-list" style="display: flex; flex-direction: column; gap: 2.5rem; border-left: 2px dashed #e2e8f0; padding-left: 2rem; margin-left: 1rem;">
                    <?php foreach ($stops as $idx => $stop): ?>
                        <div class="step-item" id="step-<?= $idx ?>" style="position: relative;">
                            <!-- Indicator -->
                            <div class="step-indicator" style="position: absolute; left: calc(-2rem - 11px); top: 0; width: 20px; height: 20px; background: white; border: 4px solid var(--primary); border-radius: 50%; z-index: 5;"></div>
                            
                            <div class="step-card" style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-sm); border: 2px solid transparent; transition: all 0.3s;">
                                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                                    <?php if ($stop['image_url']): ?>
                                        <img src="<?= $stop['image_url'] ?>" style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div>
                                        <h4 style="margin: 0 0 4px 0; font-size: 1.1rem;"><?= htmlspecialchars(Lang::t($stop['title_translations'], $stop['title'])) ?></h4>
                                        <p style="margin: 0; font-size: 0.85rem; color: #94a3b8;"><?= htmlspecialchars($stop['address']) ?></p>
                                    </div>
                                </div>
                                
                                <div class="arrival-notice" style="display: none; background: #dcfce7; color: #166534; padding: 8px 12px; border-radius: 10px; margin-bottom: 1rem; font-size: 0.85rem; font-weight: 700; align-items: center; gap: 8px;">
                                    <span>📍 <?= __('arrival_badge') ?></span>
                                </div>

                                <?php if ($stop['stop_note']): ?>
                                    <div class="stop-note" style="background: #fffbeb; padding: 10px 15px; border-radius: 10px; border-left: 4px solid #fbbf24; font-size: 0.9rem; color: #92400e;">
                                        <strong><?= __('expert_tip') ?>:</strong> <?= htmlspecialchars(Lang::t($stop['note_translations'], $stop['stop_note'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="/place/<?= $stop['item_id'] ?>" style="display: inline-block; margin-top: 1rem; font-size: 0.85rem; font-weight: 700; color: var(--primary); text-decoration: none;"><?= __('view_details') ?> →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="navControl" style="margin-top: 3rem; background: #1e293b; border-radius: 20px; padding: 2rem; color: white; text-align: center;">
                    <h4 style="margin: 0 0 0.5rem 0;"><?= __('ready_to_go') ?></h4>
                    <p style="opacity: 0.8; font-size: 0.9rem; margin-bottom: 1.5rem;"><?= __('follow_real_time') ?></p>
                    <button onclick="startNavigation()" id="navBtn" class="btn btn-primary" style="width: 100%; padding: 15px; font-weight: 700; font-size: 1.1rem; border-radius: 14px; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);">
                        🚀 <?= __('start_navigation') ?>
                    </button>
                    <div id="navStatus" style="display: none; margin-top: 1rem; font-size: 0.85rem; color: #22c55e; font-weight: 600;">
                        ● <?= __('tracking_status') ?>
                    </div>
                </div>

                <div class="directions-container" id="directionsPanel">
                    <div class="directions-title">🧭 <?= __('tour_routes') ?> - <?= __('live_gps_path') ?></div>
                    <div id="routingInstructions"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Global variables for navigation
    let map = null;
    let stops = [];
    let userMarker = null;
    let watchId = null;
    let routingControl = null;

    const langStrings = {
        start: <?= json_encode("🚀 " . __('start_navigation')) ?>,
        stop: <?= json_encode("🛑 " . __('stop_navigation')) ?>,
        tracking: <?= json_encode("● " . __('tracking_status')) ?>,
        waiting: <?= json_encode("● " . __('gps_waiting')) ?>,
    };

    function startNavigation() {
        console.log("Navigation requested...");
        
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser");
            return;
        }

        const navBtn = document.getElementById('navBtn');
        const navStatus = document.getElementById('navStatus');
        const directionsPanel = document.getElementById('directionsPanel');

        if (watchId) {
            console.log("Stopping navigation...");
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
            navBtn.innerText = langStrings.start;
            
            // Reset Styles
            navBtn.style.background = "var(--primary)";
            navBtn.style.color = "white";
            navBtn.style.borderColor = "var(--primary)";
            
            navStatus.style.display = 'none';
            directionsPanel.style.display = 'none';

            if (userMarker && map) map.removeLayer(userMarker);
            return;
        }

        if (!map) {
            console.error("Map not initialized");
            return;
        }

        console.log("Starting Geolocation watch...");
        navBtn.innerText = langStrings.stop;
        
        // Active Styles (Red background, white text)
        navBtn.style.background = "#ef4444";
        navBtn.style.color = "white";
        navBtn.style.borderColor = "#ef4444";

        navStatus.innerText = langStrings.waiting;
        navStatus.style.display = 'block';
        directionsPanel.style.display = 'block';

        watchId = navigator.geolocation.watchPosition(
            (position) => {
                console.log("Position update:", position.coords.latitude, position.coords.longitude);
                const { latitude, longitude } = position.coords;
                const userPos = [latitude, longitude];

                navStatus.innerText = langStrings.tracking;

                if (!userMarker) {
                    userMarker = L.circleMarker(userPos, {
                        radius: 12,
                        fillColor: "#3b82f6",
                        color: "white",
                        weight: 4,
                        fillOpacity: 1
                    }).addTo(map).bindTooltip(<?= json_encode(__('you_are_here')) ?>, { permanent: false });
                } else {
                    userMarker.setLatLng(userPos);
                }

                map.flyTo(userPos, 17, { animate: true, duration: 1.5 });
                checkProximity(latitude, longitude);
            },
            (err) => {
                console.error("Geo Error:", err);
                let msg = <?= json_encode(__('location_denied')) ?>;
                alert(msg);
                
                // Reset UI
                watchId = null;
                navBtn.innerText = langStrings.start;
                navBtn.style.background = "var(--primary)";
                navBtn.style.color = "white";
                navStatus.style.display = 'none';
                directionsPanel.style.display = 'none';
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
        );
    }

    function checkProximity(userLat, userLng) {
        if (!stops || stops.length === 0) return;
        stops.forEach((stop, index) => {
            if (!stop.latitude || !stop.longitude) return;
            
            const stopLatLng = L.latLng(parseFloat(stop.latitude), parseFloat(stop.longitude));
            const userLatLng = L.latLng(userLat, userLng);
            const distance = userLatLng.distanceTo(stopLatLng); // meters

            const stepElement = document.getElementById(`step-${index}`);
            if (!stepElement) return;

            const card = stepElement.querySelector('.step-card');
            const notice = stepElement.querySelector('.arrival-notice');
            const indicator = stepElement.querySelector('.step-indicator');

            if (distance < 50) { // User is within 50 meters
                card.style.borderColor = "#22c55e";
                card.style.background = "#f0fdf4";
                notice.style.display = "flex";
                indicator.style.background = "#22c55e";
                indicator.style.borderColor = "#166534";
                
                if (notice.dataset.active !== "true") {
                    stepElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    notice.dataset.active = "true";
                }
            } else {
                card.style.borderColor = "transparent";
                card.style.background = "white";
                notice.style.display = "none";
                indicator.style.background = "white";
                indicator.style.borderColor = "var(--primary)";
                notice.dataset.active = "false";
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Parse stops and filter those with valid numeric coordinates
        const allStops = <?= json_encode($stops) ?: '[]' ?>;
        stops = allStops.filter(s => {
            const lat = parseFloat(s.latitude);
            const lng = parseFloat(s.longitude);
            return !isNaN(lat) && !isNaN(lng);
        });

        if (stops.length === 0) {
            console.error("No valid coordinates found for this route.");
            const mapEl = document.getElementById('routeMap');
            if (mapEl) {
                mapEl.innerHTML = `<div style="display: flex; height: 100%; align-items: center; justify-content: center; background: #f8fafc; color: #94a3b8; text-align: center; padding: 2rem;"><div><div style="font-size: 3rem; margin-bottom: 1rem;">📍</div><h3>${<?= json_encode(__('coords_missing')) ?>}</h3><p>${<?= json_encode(__('no_valid_coords')) ?>}</p></div></div>`;
            }
            return;
        }

        // Initialize Map with first valid stop
        const firstLat = parseFloat(stops[0].latitude);
        const firstLng = parseFloat(stops[0].longitude);
        map = L.map('routeMap').setView([firstLat, firstLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const currentLang = "<?= Lang::current() ?>";
        const esc = (str) => {
            if (!str) return '';
            return str.replace(/[&<>"']/g, m => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            })[m]);
        };

        const waypoints = [];
        stops.forEach((stop, index) => {
            const lat = parseFloat(stop.latitude);
            const lng = parseFloat(stop.longitude);
            const latlng = L.latLng(lat, lng);
            waypoints.push(latlng);

            // Localize title for popup
            let stopTitle = stop.title || '';
            try {
                const titleTrans = typeof stop.title_translations === 'string' ? JSON.parse(stop.title_translations) : stop.title_translations;
                if (titleTrans && titleTrans[currentLang]) stopTitle = titleTrans[currentLang];
            } catch(e) {}

            L.marker(latlng).addTo(map)
                .bindPopup(`
                    <div style="width: 150px; font-family: 'Inter', sans-serif;">
                        <img src="${esc(stop.image_url)}" style="width: 100%; height: 80px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                        <div style="font-weight: 700; color: #1e293b;">#${index + 1} ${esc(stopTitle)}</div>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">${esc(stop.address)}</div>
                    </div>
                `);
        });

        if (waypoints.length > 1) {
            console.log("Initializing routing with", waypoints.length, "waypoints");
            // Initialize Routing Control
            routingControl = L.Routing.control({
                waypoints: waypoints,
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1',
                    profile: 'walking' 
                }),
                lineOptions: {
                    styles: [{ color: '#3b82f6', weight: 6, opacity: 0.8 }]
                },
                createMarker: function() { return null; }, // Markers already created manually
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: true,
                showAlternatives: false,
                show: false, // Don't show the default routing container on the map
                collapsible: true
            }).addTo(map);

            routingControl.on('routesfound', function(e) {
                console.log("Route found:", e.routes[0]);
                // Move directions to our side panel
                const container = routingControl.getContainer();
                const instructionsPanel = document.getElementById('routingInstructions');
                if (container && instructionsPanel) {
                    instructionsPanel.innerHTML = '';
                    instructionsPanel.appendChild(container);
                }
            });

            routingControl.on('routingerror', function(e) {
                console.error("Routing error:", e.error);
                // Fallback: draw simple polyline if routing fails
                const polyline = L.polyline(waypoints, { 
                    color: '#3b82f6', 
                    weight: 5, 
                    opacity: 0.7, 
                    dashArray: '10, 10' 
                }).addTo(map);
                map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
            });
        }
    });
</script>

<?php view('footer'); ?>
