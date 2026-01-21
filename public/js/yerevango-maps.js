/**
 * Yerevango Maps - Advanced Leaflet Map Library
 * Provides reusable map functionality with clustering, fullscreen, search, and more
 */

class YerevangoMap {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.options = {
            center: options.center || [40.1872, 44.5152], // Yerevan center
            zoom: options.zoom || 13,
            enableClustering: options.enableClustering !== false,
            enableSearch: options.enableSearch !== false,
            enableFullscreen: options.enableFullscreen !== false,
            enableLocate: options.enableLocate !== false,
            enableLayerSwitch: options.enableLayerSwitch !== false,
            markers: options.markers || [],
            onMarkerClick: options.onMarkerClick || null,
            userMarker: options.userMarker !== false,
            customTileLayer: options.customTileLayer || null,
            routingEnabled: options.routingEnabled || false
        };

        this.map = null;
        this.markerClusterGroup = null;
        this.currentLayer = 'street';
        this.layers = {};
        this.routingControl = null;

        this.init();
    }

    init() {
        // Initialize map without default zoom control
        this.map = L.map(this.containerId, {
            zoomControl: false,
            gestureHandling: true // Better mobile experience
        }).setView(this.options.center, this.options.zoom);

        // Add zoom control to top-right
        L.control.zoom({ position: 'topright' }).addTo(this.map);

        // Setup tile layers
        this.setupLayers();

        // Add controls
        if (this.options.enableLayerSwitch) this.addLayerSwitcher();
        if (this.options.enableFullscreen) this.addFullscreenControl();
        if (this.options.enableLocate) this.addLocateControl();
        if (this.options.enableSearch) this.addSearchControl();

        // Setup markers
        if (this.options.enableClustering) {
            this.setupMarkerClustering();
        }

        // Add user location marker
        if (this.options.userMarker) {
            this.addUserLocation();
        }

        // Add initial markers
        if (this.options.markers.length > 0) {
            this.addMarkers(this.options.markers);
        }

        return this.map;
    }

    setupLayers() {
        // Street View (CartoDB Voyager)
        this.layers.street = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 19
        });

        // Dark Mode (CartoDB Dark Matter)
        this.layers.dark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 19
        });

        // Satellite View (ESRI World Imagery)
        this.layers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri',
            maxZoom: 19
        });

        // Add default layer
        if (this.options.customTileLayer) {
            this.options.customTileLayer.addTo(this.map);
        } else {
            this.layers.street.addTo(this.map);
        }
    }

    addLayerSwitcher() {
        const layerControl = L.control({ position: 'topright' });

        layerControl.onAdd = (map) => {
            const div = L.DomUtil.create('div', 'yerevango-layer-switcher leaflet-bar');
            div.innerHTML = `
                <button class="layer-btn active" data-layer="street" title="Street View">
                    <i class="fas fa-map"></i>
                </button>
                <button class="layer-btn" data-layer="dark" title="Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="layer-btn" data-layer="satellite" title="Satellite">
                    <i class="fas fa-satellite"></i>
                </button>
            `;

            // Prevent map clicks when interacting with control
            L.DomEvent.disableClickPropagation(div);
            L.DomEvent.disableScrollPropagation(div);

            // Add click handlers
            div.querySelectorAll('.layer-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const layer = e.currentTarget.getAttribute('data-layer');
                    this.switchLayer(layer);

                    // Update active state
                    div.querySelectorAll('.layer-btn').forEach(b => b.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                });
            });

            return div;
        };

        layerControl.addTo(this.map);
    }

    switchLayer(layerName) {
        // Remove current layer
        if (this.currentLayer && this.layers[this.currentLayer]) {
            this.map.removeLayer(this.layers[this.currentLayer]);
        }

        // Add new layer
        if (this.layers[layerName]) {
            this.layers[layerName].addTo(this.map);
            this.currentLayer = layerName;
        }
    }

    addFullscreenControl() {
        if (typeof L.control.fullscreen !== 'undefined') {
            L.control.fullscreen({
                position: 'topright',
                title: 'Fullscreen',
                titleCancel: 'Exit Fullscreen'
            }).addTo(this.map);
        }
    }

    addLocateControl() {
        if (typeof L.control.locate !== 'undefined') {
            L.control.locate({
                position: 'topright',
                strings: {
                    title: "Find Me"
                },
                locateOptions: {
                    enableHighAccuracy: true
                },
                icon: 'fas fa-location-arrow',
                iconLoading: 'fas fa-spinner fa-spin'
            }).addTo(this.map);
        } else {
            // Fallback: custom locate button
            this.addCustomLocateControl();
        }
    }

    addCustomLocateControl() {
        const locateControl = L.control({ position: 'topright' });

        locateControl.onAdd = (map) => {
            const div = L.DomUtil.create('div', 'leaflet-bar');
            div.innerHTML = `
                <a href="#" class="leaflet-control-locate" title="Find Me" role="button">
                    <i class="fas fa-location-arrow"></i>
                </a>
            `;

            L.DomEvent.disableClickPropagation(div);

            div.querySelector('a').addEventListener('click', (e) => {
                e.preventDefault();
                this.locateUser();
            });

            return div;
        };

        locateControl.addTo(this.map);
    }

    locateUser() {
        this.map.locate({ setView: true, maxZoom: 16, enableHighAccuracy: true });

        this.map.once('locationfound', (e) => {
            // Add blue circle for accuracy
            L.circle(e.latlng, {
                radius: e.accuracy / 2,
                color: '#3b82f6',
                fillColor: '#60a5fa',
                fillOpacity: 0.2,
                weight: 2
            }).addTo(this.map);

            // Add blue marker
            const blueIcon = this.createCustomIcon('blue');
            L.marker(e.latlng, { icon: blueIcon })
                .addTo(this.map)
                .bindPopup("<div style='text-align: center; font-weight: 600;'>📍 You are here</div>")
                .openPopup();
        });

        this.map.once('locationerror', (e) => {
            alert('Could not find your location: ' + e.message);
        });
    }

    addSearchControl() {
        if (typeof L.Control.Geocoder !== 'undefined') {
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false,
                position: 'topleft',
                placeholder: 'Search location...',
                errorMessage: 'Location not found'
            }).on('markgeocode', (e) => {
                const latlng = e.geocode.center;
                this.map.setView(latlng, 16);

                L.marker(latlng)
                    .addTo(this.map)
                    .bindPopup(e.geocode.name)
                    .openPopup();
            }).addTo(this.map);
        }
    }

    setupMarkerClustering() {
        if (typeof L.markerClusterGroup !== 'undefined') {
            this.markerClusterGroup = L.markerClusterGroup({
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                maxClusterRadius: 80,
                iconCreateFunction: (cluster) => {
                    const count = cluster.getChildCount();
                    let size = 'small';
                    if (count > 10) size = 'medium';
                    if (count > 50) size = 'large';

                    return L.divIcon({
                        html: `<div><span>${count}</span></div>`,
                        className: `yerevango-cluster yerevango-cluster-${size}`,
                        iconSize: L.point(40, 40)
                    });
                }
            });
            this.markerClusterGroup.addTo(this.map);
        }
    }

    addMarkers(markers) {
        markers.forEach(markerData => {
            if (markerData.latitude && markerData.longitude) {
                const marker = this.createMarker(markerData);

                if (this.markerClusterGroup) {
                    this.markerClusterGroup.addLayer(marker);
                } else {
                    marker.addTo(this.map);
                }
            }
        });
    }

    createMarker(data) {
        const icon = this.createCustomIcon('red');
        const marker = L.marker([data.latitude, data.longitude], { icon });

        // Create enhanced popup
        const popupContent = this.createPopupContent(data);
        marker.bindPopup(popupContent, {
            maxWidth: 300,
            className: 'yerevango-popup'
        });

        // Handle marker click
        marker.on('click', () => {
            if (this.options.onMarkerClick) {
                this.options.onMarkerClick(data, marker);
            }
        });

        return marker;
    }

    createPopupContent(data) {
        const imageUrl = data.image_url || '/public/img/placeholder.jpg';
        const title = data.title || 'Untitled';
        const category = data.category_name || 'Place';
        const address = data.address || 'Address not available';
        const description = data.description || '';
        const id = data.id;

        return `
            <div class="popup-content">
                <div class="popup-image" style="background-image: url('${this.escapeHtml(imageUrl)}');">
                    <div class="popup-badge">${this.escapeHtml(category)}</div>
                </div>
                <div class="popup-body">
                    <h3 class="popup-title">${this.escapeHtml(title)}</h3>
                    <p class="popup-address"><i class="fas fa-map-marker-alt"></i> ${this.escapeHtml(address)}</p>
                    ${description ? `<p class="popup-desc">${this.escapeHtml(description.substring(0, 100))}${description.length > 100 ? '...' : ''}</p>` : ''}
                    ${id ? `<a href="/place/${id}" class="popup-btn">View Details</a>` : ''}
                </div>
            </div>
        `;
    }

    createCustomIcon(color = 'red') {
        const colors = {
            red: { bg: '#ef4444', border: '#dc2626' },
            blue: { bg: '#3b82f6', border: '#2563eb' },
            green: { bg: '#10b981', border: '#059669' }
        };

        const colorSet = colors[color] || colors.red;

        if (color === 'blue') {
            // Simple circle for user location
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background: ${colorSet.bg}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
        }

        // Pin shape for places
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background: ${colorSet.bg}; width: 32px; height: 32px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.4);"><div style="width: 10px; height: 10px; background: white; border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg);"></div></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });
    }

    addUserLocation() {
        this.map.locate({ setView: false, maxZoom: 14 });

        this.map.on('locationfound', (e) => {
            L.circle(e.latlng, {
                radius: e.accuracy / 2,
                color: '#3b82f6',
                fillColor: '#60a5fa',
                fillOpacity: 0.2,
                weight: 2
            }).addTo(this.map);

            const blueIcon = this.createCustomIcon('blue');
            L.marker(e.latlng, { icon: blueIcon })
                .addTo(this.map)
                .bindPopup("<div style='text-align: center; font-weight: 600;'>📍 You are here</div>");
        });
    }

    // Routing methods for transport page
    addRouting(waypoints) {
        if (typeof L.Routing !== 'undefined') {
            if (this.routingControl) {
                this.map.removeControl(this.routingControl);
            }

            this.routingControl = L.Routing.control({
                waypoints: waypoints,
                routeWhileDragging: true,
                geocoder: L.Control.Geocoder.nominatim(),
                show: false,
                lineOptions: {
                    styles: [{ color: '#3b82f6', opacity: 0.8, weight: 6 }]
                }
            }).addTo(this.map);

            return this.routingControl;
        }
    }

    clearRouting() {
        if (this.routingControl) {
            this.map.removeControl(this.routingControl);
            this.routingControl = null;
        }
    }

    // Utility methods
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    flyTo(latlng, zoom = 16) {
        this.map.flyTo(latlng, zoom);
    }

    getMap() {
        return this.map;
    }

    destroy() {
        if (this.map) {
            this.map.remove();
        }
    }
}

// Make available globally
window.YerevangoMap = YerevangoMap;
