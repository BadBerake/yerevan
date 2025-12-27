<?php view('admin/header', ['title' => 'Site Settings']); ?>

<div class="header-section" style="margin-bottom: 2rem;">
    <div>
        <h2>Site Settings</h2>
        <p style="color: var(--text-muted);">Manage global site information and footer fields</p>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 1px solid #bbf7d0;">
        ✓ Settings updated successfully!
    </div>
<?php endif; ?>

<form action="/admin/settings/update" method="POST" style="max-width: 800px;">
    
    <!-- Footer Information -->
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: var(--shadow-sm); border: 1px solid #f1f5f9; margin-bottom: 2rem;">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2rem;">🦶</span> Footer Content
        </h3>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Footer About Description</label>
            <textarea name="settings[footer_about]" rows="3" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.95rem;"><?= htmlspecialchars($settings['footer_about'] ?? '') ?></textarea>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Contact Email</label>
                <input type="email" name="settings[footer_email]" value="<?= htmlspecialchars($settings['footer_email'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Contact Phone</label>
                <input type="text" name="settings[footer_phone]" value="<?= htmlspecialchars($settings['footer_phone'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
            </div>
        </div>
        
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Physical Address</label>
            <input type="text" name="settings[footer_address]" value="<?= htmlspecialchars($settings['footer_address'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
        </div>
    </div>
    
    <!-- Social Media Links -->
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: var(--shadow-sm); border: 1px solid #f1f5f9; margin-bottom: 2rem;">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2rem;">📱</span> Social Media & Links
        </h3>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 100px; font-weight: 600; font-size: 0.85rem;">Instagram</div>
                <input type="url" name="settings[social_instagram]" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/..." style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 100px; font-weight: 600; font-size: 0.85rem;">Facebook</div>
                <input type="url" name="settings[social_facebook]" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/..." style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 100px; font-weight: 600; font-size: 0.85rem;">WhatsApp</div>
                <input type="url" name="settings[social_whatsapp]" value="<?= htmlspecialchars($settings['social_whatsapp'] ?? '') ?>" placeholder="https://wa.me/..." style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 1rem;">Save Changes</button>
        <button type="reset" class="btn btn-outline" style="padding: 12px 20px;">Reset</button>
    </div>
    
</form>

<?php view('admin/footer'); ?>
