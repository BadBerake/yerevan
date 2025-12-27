<?php view('header', ['title' => __('map')]); ?>

<div class="map-layout">
    <!-- Sidebar Detail View -->
    <div class="map-sidebar" id="map-sidebar">
        <div class="sidebar-empty">
            <span style="font-size: 3rem;">🗺️</span>
            <h3><?= __('explore_map') ?? 'Explore the Map' ?></h3>
            <p><?= __('select_place_on_map') ?? 'Select a place on the map to see details here.' ?></p>
        </div>
        <!-- Content will be injected here via JS -->
    </div>

    <!-- Map Container -->
    <div class="map-view" id="map"></div>
</div>

<style>
/* Page Specific Styles */
.map-layout {
    display: flex;
    height: calc(100vh - 80px); /* Full height minus header */
    width: 100%;
    overflow: hidden;
}

.map-sidebar {
    width: 400px;
    background: white;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    z-index: 2;
    overflow-y: auto;
    padding: 0;
    position: relative;
    transition: transform 0.3s ease;
}

.map-view {
    flex: 1;
    z-index: 1;
}

.sidebar-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    padding: 2rem;
    color: var(--text-muted);
}

/* Sidebar Card Content */
.sidebar-hero {
    height: 200px;
    background-size: cover;
    background-position: center;
    position: relative;
}
.sidebar-content { padding: 1.5rem; }
.sidebar-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-main); }
.sidebar-meta { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem; }
.sidebar-desc { font-size: 0.95rem; line-height: 1.6; color: #475569; margin-bottom: 2rem; }

@media (max-width: 768px) {
    .map-layout { flex-direction: column; height: calc(100vh - 60px); }
    .map-sidebar { width: 100%; height: 40%; order: 2; }
    .map-view { height: 60%; order: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Map
    var map = L.map('map', { zoomControl: false }).setView([40.1872, 44.5152], 13);
    L.control.zoom({ position: 'topright' }).addTo(map);

    // Premium Tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);


    // User Location with Blue Marker
    map.locate({setView: true, maxZoom: 14});
    map.on('locationfound', function(e) {
        // Blue circle for accuracy
        L.circle(e.latlng, {
            radius: e.accuracy / 2,
            color: '#3b82f6',
            fillColor: '#60a5fa',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(map);
        
        // Blue marker for user location
        var blueIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="background: #3b82f6; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        L.marker(e.latlng, {icon: blueIcon})
            .addTo(map)
            .bindPopup("<div style='text-align: center; font-weight: 600;'>📍 <?= __('you_are_here') ?? 'You are here' ?></div>");
    });

    // Places Data with Red Markers
    var places = <?= json_encode($items ?? []) ?>;
    var sidebar = document.getElementById('map-sidebar');
    
    // Create custom red icon for places
    var redIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background: #ef4444; width: 32px; height: 32px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.4);"><div style="width: 10px; height: 10px; background: white; border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg);"></div></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 32]
    });

    places.forEach(function(place) {
        if (place.latitude && place.longitude) {
            var marker = L.marker([place.latitude, place.longitude], {icon: redIcon}).addTo(map);

            marker.on('click', function() {
                // Determine localized content
                const currentLang = "<?= Lang::current() ?>";
                let title = place.title || '';
                let desc = place.description || '';
                let addr = place.address || 'Address not available';

                try {
                    const titleTrans = typeof place.title_translations === 'string' ? JSON.parse(place.title_translations) : place.title_translations;
                    const descTrans = typeof place.description_translations === 'string' ? JSON.parse(place.description_translations) : place.description_translations;
                    
                    if (titleTrans && titleTrans[currentLang]) title = titleTrans[currentLang];
                    if (descTrans && descTrans[currentLang]) desc = descTrans[currentLang];
                } catch(e) { console.error("Translation parse error"); }

                // Safe escaping helper
                const esc = (str) => {
                    if (!str) return '';
                    return str.replace(/[&<>"']/g, m => ({
                        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                    })[m]);
                };

                // Update Sidebar
                sidebar.innerHTML = `
                    <div class="sidebar-hero" style="background-image: url('${esc(place.image_url || '/public/img/placeholder.jpg')}');">
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 20px; color: white;">
                             <span style="background: var(--primary); padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">${esc(place.category_name || 'Place')}</span>
                        </div>
                    </div>
                    <div class="sidebar-content">
                        <div class="sidebar-title">${esc(title)}</div>
                        <div class="sidebar-meta">📍 ${esc(addr)}</div>
                        <div class="sidebar-desc">
                            ${esc(desc.substring(0, 150))}${desc.length > 150 ? '...' : ''}
                        </div>
                        <a href="/place/${place.id}" class="btn btn-primary" style="width: 100%;"><?= __('view_details') ?></a>
                    </div>
                `;
                
                map.flyTo([place.latitude, place.longitude], 16);
            });
        }
    });
});
</script>

<?php view('footer'); ?>
