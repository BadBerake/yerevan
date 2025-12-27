<?php 
$isEdit = isset($item['id']);
$title = $isEdit ? 'Edit Item: ' . $item['title'] : 'Add New Item';
$action = $isEdit ? '/admin/items/update' : '/admin/items/store';
view('admin/header', ['title' => $title]); 
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h2><?= htmlspecialchars($title) ?></h2>
</div>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); max-width: 800px;">
    <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Category</label>
                <select name="category_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($item['category_id']) && $item['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($item['slug'] ?? '') ?>" placeholder="leave-empty-to-auto-generate" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #888;">Leave empty to generate from title</small>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($item['address'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($item['phone'] ?? '') ?>" placeholder="+374..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Instagram</label>
                <input type="text" name="instagram" value="<?= htmlspecialchars($item['instagram'] ?? '') ?>" placeholder="username" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">WhatsApp</label>
                <input type="text" name="whatsapp" value="<?= htmlspecialchars($item['whatsapp'] ?? '') ?>" placeholder="+374..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Opening Hours</label>
            <input type="text" name="opening_hours" value="<?= htmlspecialchars($item['opening_hours'] ?? '') ?>" placeholder="e.g. Mon-Sun: 10:00 AM - 12:00 PM" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1.5rem;">
             <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Latitude</label>
                <input type="text" id="lat-field" name="latitude" value="<?= htmlspecialchars($item['latitude'] ?? '') ?>" placeholder="e.g. 40.1872" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Longitude</label>
                <input type="text" id="lng-field" name="longitude" value="<?= htmlspecialchars($item['longitude'] ?? '') ?>" placeholder="e.g. 44.5152" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Location Picker (Click on map to set coordinates)</label>
            <div id="adminMap" style="height: 300px; border-radius: 8px; border: 1px solid #ddd;"></div>
            <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">📍 Tip: Drag the marker or click anywhere on the map to update the coordinates above.</p>
        </div>

        <!-- Leaflet for Admin Map Picker -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var initialLat = <?= !empty($item['latitude']) ? $item['latitude'] : '40.1792' ?>;
                var initialLng = <?= !empty($item['longitude']) ? $item['longitude'] : '44.5152' ?>;
                
                var map = L.map('adminMap').setView([initialLat, initialLng], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                var marker = L.marker([initialLat, initialLng], {
                    draggable: true
                }).addTo(map);

                function updateFields(lat, lng) {
                    document.getElementById('lat-field').value = lat.toFixed(6);
                    document.getElementById('lng-field').value = lng.toFixed(6);
                }

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    updateFields(e.latlng.lat, e.latlng.lng);
                });

                marker.on('dragend', function(e) {
                    var position = marker.getLatLng();
                    updateFields(position.lat, position.lng);
                });
            });
        </script>

        <?php
        // Decode existing amenities
        $selectedAmenities = [];
        if (isset($item['amenities'])) {
            $decoded = json_decode($item['amenities'], true);
            $selectedAmenities = is_array($decoded) ? $decoded : [];
        }
        
        $availableAmenities = [
            'wifi' => 'Free Wi-Fi',
            'parking' => 'Parking Available',
            'cards' => 'Credit Cards Accepted',
            'outdoor' => 'Outdoor Seating',
            'family' => 'Family Friendly',
            'music' => 'Live Music',
            'pets' => 'Pet Friendly',
            'delivery' => 'Delivery Available'
        ];
        ?>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.8rem; font-weight: 500;">Amenities</label>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                <?php foreach($availableAmenities as $key => $label): ?>
                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="checkbox" name="amenities[]" value="<?= $key ?>" 
                               <?= in_array($key, $selectedAmenities) ? 'checked' : '' ?>
                               style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="font-size: 0.9rem;"><?= $label ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem;">🌍 Localized Content</h3>
                <button type="button" onclick="autoTranslate()" id="translate-btn" class="btn btn-outline" style="font-size: 0.8rem; padding: 5px 12px; border-color: var(--primary); color: var(--primary);">✨ Auto-Translate (AI)</button>
            </div>
            
            <?php 
            $langs = ['hy' => 'Armenian 🇦🇲', 'ru' => 'Russian 🇷🇺', 'fa' => 'Persian 🇮🇷', 'ar' => 'Arabic 🇸🇦'];
            $title_trans = is_string($item['title_translations'] ?? []) ? json_decode($item['title_translations'], true) : ($item['title_translations'] ?? []);
            $desc_trans = is_string($item['description_translations'] ?? []) ? json_decode($item['description_translations'], true) : ($item['description_translations'] ?? []);
            ?>

            <div style="display: flex; gap: 10px; margin-bottom: 1rem; overflow-x: auto; padding-bottom: 5px;" id="lang-tabs">
                <?php foreach($langs as $code => $label): ?>
                    <button type="button" onclick="showLang('<?= $code ?>')" class="lang-tab-btn" data-lang="<?= $code ?>" style="white-space: nowrap; padding: 6px 15px; border-radius: 20px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;">
                        <?= $label ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php foreach($langs as $code => $label): ?>
                <div id="panel-<?= $code ?>" class="lang-panel" style="display: none;">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">Title (<?= $label ?>)</label>
                        <input type="text" name="title_trans[<?= $code ?>]" value="<?= htmlspecialchars($title_trans[$code] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.3rem;">Description (<?= $label ?>)</label>
                        <textarea name="desc_trans[<?= $code ?>]" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;"><?= htmlspecialchars($desc_trans[$code] ?? '') ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
            function showLang(lang) {
                document.querySelectorAll('.lang-panel').forEach(p => p.style.display = 'none');
                document.querySelectorAll('.lang-tab-btn').forEach(b => {
                    b.style.background = 'white';
                    b.style.color = '#64748b';
                    b.style.borderColor = '#e2e8f0';
                });
                
                document.getElementById('panel-' + lang).style.display = 'block';
                const activeBtn = document.querySelector(`.lang-tab-btn[data-lang="${lang}"]`);
                if (activeBtn) {
                    activeBtn.style.background = 'var(--primary, #3b82f6)';
                    activeBtn.style.color = 'white';
                    activeBtn.style.borderColor = 'var(--primary, #3b82f6)';
                }
            }

            async function autoTranslate() {
                const title = document.querySelector('input[name="title"]').value;
                const desc = document.querySelector('textarea[name="description"]').value;

                if (!title) {
                    alert('Please enter an English title first.');
                    return;
                }

                const btn = document.getElementById('translate-btn');
                const originalText = btn.innerText;
                btn.innerText = 'Translating...';
                btn.disabled = true;

                try {
                    const languages = ['hy', 'ru', 'fa', 'ar'];
                    for (const lang of languages) {
                        const tRes = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${lang}&dt=t&q=${encodeURIComponent(title)}`);
                        if (tRes.ok) {
                            const tData = await tRes.json();
                            document.querySelector(`input[name="title_trans[${lang}]"]`).value = tData[0][0][0];
                        }

                        if (desc) {
                            const dRes = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${lang}&dt=t&q=${encodeURIComponent(desc)}`);
                            if (dRes.ok) {
                                const dData = await dRes.json();
                                document.querySelector(`textarea[name="desc_trans[${lang}]"]`).value = dData[0][0][0];
                            }
                        }
                    }
                    alert('Translation complete! Please review and save.');
                } catch (e) {
                    console.error(e);
                    alert('Auto-translation failed. You can still enter translations manually.');
                } finally {
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            }
            document.addEventListener('DOMContentLoaded', () => showLang('hy'));
        </script>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Description</label>
            <textarea name="description" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Main Image</label>
             <?php if (!empty($item['image_url'])): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Current Image" style="height: 150px; border-radius: 8px; object-fit: cover;">
                </div>
            <?php endif; ?>
            <input type="file" name="main_image" accept="image/*, image/webp" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #666;">Upload a new image to replace the current one.</small>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Gallery Images</label>
            <input type="file" name="gallery[]" multiple accept="image/*, image/webp" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #666;">Hold Cmd/Ctrl to select multiple images.</small>

            <?php if (!empty($gallery)): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 15px;">
                    <?php foreach($gallery as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px;">
                            <button type="button" onclick="deleteGalleryImage(<?= $img['id'] ?>, <?= $item['id'] ?>)" style="position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.8); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer;">&times;</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <script>
        function deleteGalleryImage(imageId, itemId) {
            if (!confirm('Delete this image?')) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/images/delete';
            
            const imageInput = document.createElement('input');
            imageInput.type = 'hidden';
            imageInput.name = 'image_id';
            imageInput.value = imageId;
            
            const itemInput = document.createElement('input');
            itemInput.type = 'hidden';
            itemInput.name = 'item_id';
            itemInput.value = itemId;
            
            form.appendChild(imageInput);
            form.appendChild(itemInput);
            document.body.appendChild(form);
            form.submit();
        }
        </script>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><?= $isEdit ? 'Save Changes' : 'Create Item' ?></button>
            <a href="/admin/items" class="btn btn-outline" style="margin-left: 10px; border: none;">Cancel</a>
        </div>
    </form>
</div>

<?php view('admin/footer'); ?>
