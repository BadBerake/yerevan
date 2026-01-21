<?php view('header', ['title' => __('map')]); ?>

<div class="map-layout">
    <!-- Sidebar Detail View -->
    <div class="map-sidebar" id="map-sidebar">
        <!-- Handle for mobile swipe (hidden on desktop) -->
        <div class="sidebar-handle" style="display: none;"></div>
        
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
    const places = <?= json_encode($items ?? []) ?>;
    const sidebar = document.getElementById('map-sidebar');
    const currentLang = "<?= Lang::current() ?>";
    
    // Helper function to get localized content
    function getLocalizedContent(item, field) {
        const translationField = field + '_translations';
        if (item[translationField]) {
            try {
                const translations = typeof item[translationField] === 'string' 
                    ? JSON.parse(item[translationField]) 
                    : item[translationField];
                
                if (translations && translations[currentLang]) {
                    return translations[currentLang];
                }
            } catch(e) {
                console.error('Translation parse error:', e);
            }
        }
        return item[field] || '';
    }

    // Initialize map with Yerevango Maps library
    const mapInstance = new YerevangoMap('map', {
        center: [40.1872, 44.5152],
        zoom: 13,
        enableClustering: true,
        enableSearch: true,
        enableFullscreen: true,
        enableLocate: true,
        enableLayerSwitch: true,
        userMarker: true,
        markers: places.map(place => ({
            ...place,
            title: getLocalizedContent(place, 'title'),
            description: getLocalizedContent(place, 'description')
        })),
        onMarkerClick: function(markerData, marker) {
            const title = getLocalizedContent(markerData, 'title');
            const desc = getLocalizedContent(markerData, 'description');
            const addr = markerData.address || 'Address not available';
            
            // Safe escaping helper
            const esc = (str) => {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            };

            // Update Sidebar
            sidebar.innerHTML = `
                <div class="sidebar-hero" style="background-image: url('${esc(markerData.image_url || '/public/img/placeholder.jpg')}');">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 20px; color: white;">
                         <span style="background: var(--primary); padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">${esc(markerData.category_name || 'Place')}</span>
                    </div>
                </div>
                <div class="sidebar-content">
                    <div class="sidebar-title">${esc(title)}</div>
                    <div class="sidebar-meta">📍 ${esc(addr)}</div>
                    <div class="sidebar-desc">
                        ${esc(desc.substring(0, 150))}${desc.length > 150 ? '...' : ''}
                    </div>
                    <a href="/place/${markerData.id}" class="btn btn-primary" style="width: 100%;"><?= __('view_details') ?></a>
                </div>
            `;
            
            mapInstance.flyTo([markerData.latitude, markerData.longitude], 16);
        }
    });

    // Mobile bottom sheet handling
    if (window.innerWidth <= 768) {
        const sidebarHandle = sidebar.querySelector('.sidebar-handle');
        if (sidebarHandle) {
            sidebarHandle.style.display = 'block';
            
            let startY = 0;
            let currentY = 0;
            
            sidebarHandle.addEventListener('touchstart', (e) => {
                startY = e.touches[0].clientY;
            });
            
            sidebarHandle.addEventListener('touchmove', (e) => {
                currentY = e.touches[0].clientY;
                const diff = currentY - startY;
                
                if (diff > 0) {
                    sidebar.style.transform = `translateY(${diff}px)`;
                }
            });
            
            sidebarHandle.addEventListener('touchend', () => {
                const diff = currentY - startY;
                
                if (diff > 100) {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                }
                
                sidebar.style.transform = '';
                startY = 0;
                currentY = 0;
            });
        }
    }
});
</script>

<?php view('footer'); ?>
