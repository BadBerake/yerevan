<?php 
$isEdit = isset($route['id']);
view('admin/header', ['title' => $isEdit ? 'Edit Route' : 'New Route']); 
?>

<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="/admin/routes" style="text-decoration: none; color: #64748b; font-size: 0.9rem;">← Back to Routes</a>
            <h2 style="margin-top: 0.5rem;"><?= $isEdit ? 'Edit Tour Route' : 'Create New Tour Route' ?></h2>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
        <!-- Route Basic Info -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <form action="/admin/routes/save" method="POST">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= $route['id'] ?>">
                <?php endif; ?>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Route Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($route['name'] ?? '') ?>" required placeholder="e.g. Morning Coffee Walk" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <!-- Localized Content for Route -->
                <div style="margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="margin: 0; font-size: 1rem;">🌍 Localized Info</h3>
                        <button type="button" onclick="autoTranslateRoute()" id="translate-route-btn" class="btn btn-outline" style="font-size: 0.75rem; padding: 4px 10px; border-color: var(--primary); color: var(--primary);">✨ Auto-Translate</button>
                    </div>
                    
                    <?php 
                    $langs = ['hy' => 'Armenian 🇦🇲', 'ru' => 'Russian 🇷🇺', 'fa' => 'Persian 🇮🇷', 'ar' => 'Arabic 🇸🇦'];
                    $name_trans = is_string($route['name_translations'] ?? []) ? json_decode($route['name_translations'], true) : ($route['name_translations'] ?? []);
                    $desc_trans = is_string($route['description_translations'] ?? []) ? json_decode($route['description_translations'], true) : ($route['description_translations'] ?? []);
                    ?>

                    <div style="display: flex; gap: 8px; margin-bottom: 1rem; overflow-x: auto; padding-bottom: 5px;">
                        <?php foreach($langs as $code => $label): ?>
                            <button type="button" onclick="showRouteLang('<?= $code ?>')" class="route-lang-tab" data-lang="<?= $code ?>" style="white-space: nowrap; padding: 5px 12px; border-radius: 15px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.8rem;">
                                <?= $label ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach($langs as $code => $label): ?>
                        <div id="route-panel-<?= $code ?>" class="route-lang-panel" style="display: none;">
                            <div style="margin-bottom: 0.8rem;">
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.2rem;">Name (<?= $code ?>)</label>
                                <input type="text" name="name_trans[<?= $code ?>]" value="<?= htmlspecialchars($name_trans[$code] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.2rem;">Description (<?= $code ?>)</label>
                                <textarea name="desc_trans[<?= $code ?>]" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 0.9rem;"><?= htmlspecialchars($desc_trans[$code] ?? '') ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Interest Tag</label>
                        <select name="interest_tag" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <option value="">General</option>
                            <option value="cafes" <?= (isset($route['interest_tag']) && $route['interest_tag'] == 'cafes') ? 'selected' : '' ?>>Coffee / Cafes</option>
                            <option value="restaurants" <?= (isset($route['interest_tag']) && $route['interest_tag'] == 'restaurants') ? 'selected' : '' ?>>Food / Restaurants</option>
                            <option value="events" <?= (isset($route['interest_tag']) && $route['interest_tag'] == 'events') ? 'selected' : '' ?>>Art / Culture</option>
                            <option value="nightlife" <?= (isset($route['interest_tag']) && $route['interest_tag'] == 'nightlife') ? 'selected' : '' ?>>Nightlife</option>
                            <option value="outdoor" <?= (isset($route['interest_tag']) && $route['interest_tag'] == 'outdoor') ? 'selected' : '' ?>>Outdoor</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Difficulty</label>
                        <select name="difficulty" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <option value="easy" <?= (isset($route['difficulty']) && $route['difficulty'] == 'easy') ? 'selected' : '' ?>>Easy</option>
                            <option value="medium" <?= (isset($route['difficulty']) && $route['difficulty'] == 'medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="hard" <?= (isset($route['difficulty']) && $route['difficulty'] == 'hard') ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Estimated Time</label>
                        <input type="text" name="estimated_time" value="<?= htmlspecialchars($route['estimated_time'] ?? '') ?>" placeholder="e.g. 2-3 hours" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Cover Image URL</label>
                        <input type="text" name="image_url" value="<?= htmlspecialchars($route['image_url'] ?? '') ?>" placeholder="https://..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Description</label>
                    <textarea name="description" rows="5" placeholder="Describe this tour..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;"><?= htmlspecialchars($route['description'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1rem; font-weight: 600;">
                    <?= $isEdit ? 'Update Route Details' : 'Create Route' ?>
                </button>
            </form>
        </div>

        <!-- Route Stops (Only visible when editing) -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <?php if ($isEdit): ?>
                <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; margin-bottom: 1rem;">Add Stop</h3>
                    <form action="/admin/routes/stops/add" method="POST">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Select Place</label>
                            <select name="item_id" required style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                <option value="">-- Choose --</option>
                                <?php foreach ($places as $place): ?>
                                    <option value="<?= $place['id'] ?>"><?= htmlspecialchars($place['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-bottom: 0.5rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Note/Tip (Optional)</label>
                            <textarea name="stop_note" rows="2" placeholder="e.g. Try their signature latte here!" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;"></textarea>
                        </div>

                        <!-- Localized Notes for Stop -->
                        <div style="margin-bottom: 1.5rem; background: #fefce8; padding: 1rem; border-radius: 8px; border: 1px solid #fef08a;">
                             <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #854d0e; margin-bottom: 0.5rem; text-transform: uppercase;">Localized Notes (AI-Ready)</label>
                             <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <?php foreach(['hy', 'ru', 'fa', 'ar'] as $code): ?>
                                    <input type="text" name="note_trans[<?= $code ?>]" placeholder="<?= strtoupper($code) ?> note" style="padding: 6px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 0.75rem;">
                                <?php endforeach; ?>
                             </div>
                        </div>

                        <button type="submit" class="btn btn-outline" style="width: 100%; padding: 10px;">Add to Route</button>
                    </form>
                </div>

                <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; margin-bottom: 1.5rem;">Current Stops</h3>
                    <?php if (empty($stops)): ?>
                        <p style="color: #94a3b8; font-size: 0.9rem; font-style: italic;">No stops added yet.</p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <?php foreach ($stops as $stop): ?>
                                <div style="display: flex; gap: 12px; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9; position: relative;">
                                    <div style="flex: 0 0 28px; height: 28px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                                        <?= $stop['order_index'] + 1 ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($stop['place_name']) ?></div>
                                        <?php if ($stop['stop_note']): ?>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;"><?= htmlspecialchars($stop['stop_note']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <form action="/admin/routes/stops/delete" method="POST" onsubmit="return confirm('Remove this stop?')">
                                        <input type="hidden" name="stop_id" value="<?= $stop['id'] ?>">
                                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1.2rem;">×</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="background: #eff6ff; padding: 1.5rem; border-radius: 12px; border: 1px dashed #3b82f6; color: #1e40af;">
                    <p style="margin: 0; font-size: 0.9rem;">After creating the route, you will be able to add specific places/stops to it.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function showRouteLang(lang) {
        document.querySelectorAll('.route-lang-panel').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.route-lang-tab').forEach(b => {
            b.style.background = 'white';
            b.style.color = '#64748b';
            b.style.borderColor = '#e2e8f0';
        });
        
        document.getElementById('route-panel-' + lang).style.display = 'block';
        const activeBtn = document.querySelector(`.route-lang-tab[data-lang="${lang}"]`);
        if (activeBtn) {
            activeBtn.style.background = 'var(--primary, #3b82f6)';
            activeBtn.style.color = 'white';
            activeBtn.style.borderColor = 'var(--primary, #3b82f6)';
        }
    }

    async function autoTranslateRoute() {
        const name = document.querySelector('input[name="name"]').value;
        const desc = document.querySelector('textarea[name="description"]').value;

        if (!name) {
            alert('Please enter an English route name first.');
            return;
        }

        const btn = document.getElementById('translate-route-btn');
        const originalText = btn.innerText;
        btn.innerText = '...';
        btn.disabled = true;

        try {
            const languages = ['hy', 'ru', 'fa', 'ar'];
            for (const lang of languages) {
                const nRes = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${lang}&dt=t&q=${encodeURIComponent(name)}`);
                if (nRes.ok) {
                    const nData = await nRes.json();
                    document.querySelector(`input[name="name_trans[${lang}]"]`).value = nData[0][0][0];
                }

                if (desc) {
                    const dRes = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${lang}&dt=t&q=${encodeURIComponent(desc)}`);
                    if (dRes.ok) {
                        const dData = await dRes.json();
                        document.querySelector(`textarea[name="desc_trans[${lang}]"]`).value = dData[0][0][0];
                    }
                }
            }
        } catch (e) {
            console.error(e);
            alert('Auto-translation failed.');
        } finally {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => showRouteLang('hy'));
</script>

<?php view('admin/footer'); ?>
