    </main>
    
    <?php
    // Fetch Footer Settings
    global $db;
    try {
        $stmt = $db->query("SELECT key, value FROM settings WHERE category IN ('footer', 'social')");
        $footer_settings = [];
        foreach ($stmt->fetchAll() as $s) {
            $footer_settings[$s['key']] = $s['value'];
        }
    } catch (Exception $e) {
        $footer_settings = []; // Fallback
    }

    // Fallbacks
    $f_email = !empty($footer_settings['footer_email']) ? $footer_settings['footer_email'] : 'info@yerevango.am';
    $f_phone = !empty($footer_settings['footer_phone']) ? $footer_settings['footer_phone'] : '+374 10 123456';
    $f_address = !empty($footer_settings['footer_address']) ? $footer_settings['footer_address'] : 'Mashtots Ave 1, Yerevan';
    $f_about = !empty($footer_settings['footer_about']) ? $footer_settings['footer_about'] : __('platform_description');
    $s_instagram = !empty($footer_settings['social_instagram']) ? $footer_settings['social_instagram'] : '#';
    $s_facebook = !empty($footer_settings['social_facebook']) ? $footer_settings['social_facebook'] : '#';
    $s_whatsapp = !empty($footer_settings['social_whatsapp']) ? $footer_settings['social_whatsapp'] : '#';
    ?>
    
    <footer style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 4rem 0 2rem; margin-top: 6rem;">
        <div class="container">
            <div class="grid" style="margin-bottom: 3rem;">
                
                <div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; font-weight: 700;">YerevanGo 🇦🇲</h3>
                    <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
                        <?= htmlspecialchars($f_about) ?>
                    </p>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="<?= htmlspecialchars($s_instagram) ?>" target="_blank" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">📸</a>
                        <a href="<?= htmlspecialchars($s_whatsapp) ?>" target="_blank" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">💬</a>
                        <a href="<?= htmlspecialchars($s_facebook) ?>" target="_blank" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">📘</a>
                    </div>
                </div>
                
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600;"><?= __('quick_links') ?></h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="/" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'"><?= __('home') ?></a>
                        <a href="/restaurants" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'"><?= __('restaurants') ?></a>
                        <a href="/cafes" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'"><?= __('cafes') ?></a>
                        <a href="/events" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'"><?= __('events') ?></a>
                        <a href="/map" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'"><?= __('map') ?></a>
                    </div>
                </div>
                
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600;"><?= __('newsletter') ?? 'Newsletter' ?></h3>
                    <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1rem; line-height: 1.5;">Get updates on the best of Yerevan.</p>
                    <form id="newsletterForm" style="display: flex; gap: 8px;">
                        <input type="email" name="email" placeholder="Your email" required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); color: white; outline: none; font-size: 0.85rem;">
                        <button type="submit" style="padding: 10px 15px; border-radius: 8px; background: var(--primary); color: white; border: none; cursor: pointer; font-weight: 700;">Join</button>
                    </form>
                    <div id="newsletterMsg" style="margin-top: 10px; font-size: 0.8rem; display: none;"></div>
                    
                    <script>
                    document.getElementById('newsletterForm').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const form = e.target;
                        const msg = document.getElementById('newsletterMsg');
                        const formData = new FormData(form);
                        
                        try {
                            const response = await fetch('/api/newsletter/subscribe', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            msg.style.display = 'block';
                            msg.style.color = data.status === 'success' ? '#4ade80' : '#f87171';
                            msg.textContent = data.message;
                            
                            if (data.status === 'success') {
                                form.reset();
                            }
                        } catch (err) {
                            console.error(err);
                        }
                    });
                    </script>
                </div>
                
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600;"><?= __('contact') ?></h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="mailto:<?= htmlspecialchars($f_email) ?>" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'">📧 <?= htmlspecialchars($f_email) ?></a>
                        <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $f_phone)) ?>" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#cbd5e1'">📞 <?= htmlspecialchars($f_phone) ?></a>
                        <span style="color: #94a3b8; font-size: 0.9rem;">📍 <?= htmlspecialchars($f_address) ?></span>
                    </div>
                </div>
                
            </div>
            
            <div style="text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); color: #64748b; font-size: 0.85rem;">
                <div style="margin-bottom: 1rem; display: flex; justify-content: center; flex-wrap: wrap; gap: 10px;">
                    <a href="/about" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'"><?= __('about_us') ?></a>
                    <span style="color: #475569;">•</span>
                    <a href="/privacy" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'"><?= __('privacy_policy') ?></a>
                    <span style="color: #475569;">•</span>
                    <a href="/terms" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'"><?= __('terms_conditions') ?></a>
                </div>
                &copy; <?= date('Y') ?> <?= __('platform_credits') ?>
            </div>
        </div>
    </footer>
</body>
</html>
