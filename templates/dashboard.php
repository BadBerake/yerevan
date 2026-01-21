<?php view('header', ['title' => $title]); ?>

<div style="display: flex; gap: 2rem; padding: 2rem 0;">
    <!-- Sidebar -->
    <div style="flex: 0 0 250px; background: white; padding: 1.5rem; border-radius: 10px; height: fit-content; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 1rem;">
                <?php if(!empty($user['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar_url']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; background: var(--bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">👤</div>
                <?php endif; ?>
                
                <form action="/dashboard/update-profile" method="POST" enctype="multipart/form-data" id="avatarForm">
                    <label for="avatarInput" style="position: absolute; bottom: 0; right: 0; background: var(--primary); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: white;"><path d="M4 4h3l2-2h6l2 2h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2m8 3a5 5 0 0 0-5 5 5 5 0 0 0 5 5 5 5 0 0 0 5-5 5 5 0 0 0-5-5m0 2a3 3 0 0 1 3 3 3 3 0 0 1-3 3 3 3 0 0 1-3-3 3 3 0 0 1 3-3z"/></svg>
                    </label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;" onchange="document.getElementById('avatarForm').submit()">
                </form>
            </div>
            <h3>
                <?= htmlspecialchars($user['username']) ?>
                <?php if (!empty($user['is_verified'])): ?>
                    <span class="verified-badge" title="Verified User">
                         <svg viewBox="0 0 24 24" aria-label="Verified account">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </span>
                <?php endif; ?>
            </h3>
            <span style="font-size: 0.8rem; padding: 3px 8px; background: #eee; border-radius: 10px;"><?= htmlspecialchars($user['role']) ?></span>
        </div>
        
        <nav style="display: flex; flex-direction: column; gap: 5px;">
            <a href="/dashboard" class="btn btn-primary" style="text-align: left;"><?= __('overview') ?></a>
            <a href="/dashboard/reservations" class="btn btn-outline" style="text-align: left; border: none;"><?= __('my_reservations') ?></a>
            <a href="/dashboard/tickets" class="btn btn-outline" style="text-align: left; border: none;"><?= __('my_tickets') ?></a>
            <a href="/dashboard/support" class="btn btn-outline" style="text-align: left; border: none;"><?= __('support_tickets') ?></a>
            
            <?php if (empty($user['is_verified'])): ?>
                <form action="/verify/request" method="POST" style="margin-top: 10px;">
                    <button type="submit" class="btn btn-outline" style="width: 100%; text-align: left; border: 1px solid #ef4444; color: #ef4444; background: #fef2f2; font-weight: 600;">
                        <span style="margin-right: 5px;">✅</span> Get Verified
                    </button>
                    <div style="font-size: 0.7rem; color: #666; margin-top: 5px; text-align: center;">One-time fee: $9.99</div>
                </form>
            <?php else: ?>
                <div style="margin-top: 10px; padding: 10px; background: #fef2f2; border-radius: 8px; color: #ef4444; font-weight: 600; text-align: center; font-size: 0.9rem; border: 1px solid #fecaca; display: flex; align-items: center; justify-content: center; gap: 5px;">
                     <!-- Simple Red Checkmark -->
                    <svg viewBox="0 0 24 24" aria-label="Verified account" style="width: 20px; height: 20px;">
                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    Verified Account
                </div>
            <?php endif; ?>



            <hr style="margin: 10px 0; border-top: 1px solid #eee;">
            <a href="/logout" class="btn btn-outline" style="text-align: left; border: none; color: #ef4444;">
                <span style="margin-right: 5px;">🚪</span> <?= __('logout') ?>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1;">
        <h2 style="margin-bottom: 1.5rem;"><?= __('dashboard') ?></h2>
        
        <div class="grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 2rem; gap: 15px;">
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid var(--primary);">
                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase; letter-spacing: 1px;">Points</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary);"><?= number_format($user['points'] ?? 0) ?></div>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #10b981;">
                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase; letter-spacing: 1px;">Level</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #10b981;"><?= $user['level'] ?? 1 ?></div>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #f59e0b;">
                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase; letter-spacing: 1px;">Badges</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b;"><?= count($achievements ?? []) ?></div>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #a855f7;">
                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase; letter-spacing: 1px;">Reviews</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #a855f7;"><?= $stats['reviews_count'] ?? 0 ?></div>
            </div>
        </div>

        <!-- Level Progress -->
        <?php
        $currentPoints = $user['points'] ?? 0;
        $lvls = [0, 200, 500, 1000, 2000, 5000];
        $currentLevel = $user['level'] ?? 1;
        $nextLevelPoints = $lvls[$currentLevel] ?? $lvls[4];
        $prevLevelPoints = $lvls[$currentLevel - 1] ?? 0;
        $progress = ($currentPoints - $prevLevelPoints) / ($nextLevelPoints - $prevLevelPoints) * 100;
        $progress = min(100, max(0, $progress));
        ?>
        <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-weight: 700; color: #475569;">Level <?= $currentLevel ?> Progress</span>
                <span style="font-size: 0.85rem; color: #64748b;"><?= number_format($currentPoints) ?> / <?= number_format($nextLevelPoints) ?> Pts to Level <?= $currentLevel + 1 ?></span>
            </div>
            <div style="height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden;">
                <div style="width: <?= $progress ?>%; height: 100%; background: linear-gradient(90deg, #6366f1, #a855f7); border-radius: 6px; transition: width 1s ease-out;"></div>
            </div>
        </div>

        <!-- My Achievements -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0;">🏆 My Badges</h3>
                <a href="/leaderboard" style="font-size: 0.9rem; color: var(--primary); text-decoration: none; font-weight: 600;">View Leaderboard →</a>
            </div>
            <?php if (empty($achievements)): ?>
                <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <p style="color: #64748b; margin: 0;">You haven't earned any badges yet. Start exploring to unlock achievements!</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 20px;">
                    <?php foreach ($achievements as $ach): ?>
                        <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div style="width: 50px; height: 50px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 2px solid #facc15;">
                                <i class="<?= $ach['icon'] ?>" style="color: #ca8a04; font-size: 1.5rem;"></i>
                            </div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 4px;"><?= htmlspecialchars($ach['name']) ?></div>
                            <div style="font-size: 0.7rem; color: #64748b;"><?= date('M d, Y', strtotime($ach['earned_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recently Viewed -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">🕒 <?= __('recently_viewed') ?? 'Recently Viewed' ?></h3>
            <?php if (empty($history)): ?>
                <p style="color: #64748b; font-style: italic;"><?= __('no_history_yet') ?? 'Your browsing history will appear here.' ?></p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
                    <?php foreach ($history as $item): ?>
                        <a href="/place/<?= $item['id'] ?>" style="text-decoration: none; color: inherit;">
                            <div style="border-radius: 8px; overflow: hidden; border: 1px solid #eee;">
                                <img src="<?= $item['image_url'] ?: '/public/img/placeholder.jpg' ?>" style="width: 100%; height: 100px; object-fit: cover;">
                                <div style="padding: 8px;">
                                    <div style="font-weight: 600; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($item['title']) ?></div>
                                    <div style="font-size: 0.75rem; color: #666;"><?= date('M d', strtotime($item['viewed_at'])) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- My Favorites -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">❤️ <?= __('my_favorites') ?? 'My Favorites' ?></h3>
            <?php if (empty($favorites)): ?>
                <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <p style="color: #64748b;"><?= __('no_favorites_yet') ?></p>
                    <a href="/explore" class="btn btn-outline" style="font-size: 0.9rem; margin-top: 10px;"><?= __('explore_now') ?></a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem;">
                    <?php foreach ($favorites as $item): ?>
                        <a href="/place/<?= $item['id'] ?>" style="text-decoration: none; color: inherit;">
                            <div style="border-radius: 12px; overflow: hidden; border: 1px solid #eee; position: relative;">
                                <img src="<?= $item['image_url'] ?: '/public/img/placeholder.jpg' ?>" style="width: 100%; height: 140px; object-fit: cover;">
                                <div style="padding: 12px;">
                                    <div style="font-size: 0.7rem; color: var(--primary); font-weight: 700; text-transform: uppercase;"><?= htmlspecialchars($item['category_name']) ?></div>
                                    <div style="font-weight: 700; font-size: 1rem; margin-top: 2px;"><?= htmlspecialchars($item['title']) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- My Reservations -->
        <div id="reservations" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">📅 My Reservations</h3>
            <?php if (empty($bookings)): ?>
                <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <p style="color: #64748b; margin-bottom: 10px;">You haven't made any table reservations yet.</p>
                    <a href="/explore" class="btn btn-outline" style="font-size: 0.9rem;">Find a Place to Book</a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($bookings as $booking): ?>
                        <div style="border: 1px solid #eee; border-radius: 12px; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <div style="width: 80px; height: 80px; border-radius: 10px; overflow: hidden;">
                                    <img src="<?= $booking['image_url'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 4px;"><?= htmlspecialchars($booking['place_name']) ?></div>
                                    <div style="color: #64748b; font-size: 0.9rem; margin-bottom: 4px;">
                                        🕒 <?= date('F d, Y • H:i', strtotime($booking['booking_date'] . ' ' . $booking['booking_time'])) ?>
                                    </div>
                                    <div style="color: #64748b; font-size: 0.9rem;">
                                        👥 <?= $booking['guest_count'] ?> Guests
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <?php if($booking['status'] == 'pending'): ?>
                                    <span style="background: #fff7ed; color: #c2410c; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #ffedd5;">Pending Confirmation</span>
                                <?php elseif($booking['status'] == 'confirmed'): ?>
                                    <span style="background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #d1fae5;">Confirmed</span>
                                <?php elseif($booking['status'] == 'cancelled'): ?>
                                    <span style="background: #fef2f2; color: #b91c1c; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #fecaca;">Cancelled</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <h3><?= __('profile_settings') ?? 'Profile Settings' ?></h3>
            <form action="/dashboard/update-profile" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4b5563;">Email</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f3f4f6; color: #6b7280;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Username</label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f3f4f6; color: #6b7280;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Phone</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+374 XX XXXXXX" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Country</label>
                        <select name="country" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: white;">
                            <option value="">Select Country</option>
                            <?php 
                            $countries = [
                                'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
                                'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi',
                                'Cabo Verde', 'Cambodia', 'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo (Congo-Brazzaville)', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czechia (Czech Republic)',
                                'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic',
                                'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini (fmr. "Swaziland")', 'Ethiopia',
                                'Fiji', 'Finland', 'France',
                                'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
                                'Haiti', 'Holy See', 'Honduras', 'Hungary',
                                'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy',
                                'Jamaica', 'Japan', 'Jordan',
                                'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan',
                                'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
                                'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar (formerly Burma)',
                                'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway',
                                'Oman',
                                'Pakistan', 'Palau', 'Palestine State', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal',
                                'Qatar',
                                'Romania', 'Russia', 'Rwanda',
                                'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria',
                                'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu',
                                'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States of America', 'Uruguay', 'Uzbekistan',
                                'Vanuatu', 'Venezuela', 'Vietnam',
                                'Yemen',
                                'Zambia', 'Zimbabwe'
                            ];
                            foreach($countries as $c): 
                            ?>
                                <option value="<?= $c ?>" <?= ($user['country'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Bio / Description</label>
                    <textarea name="bio" rows="3" placeholder="Tell us a bit about yourself..." style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; resize: vertical;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>
                
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 25px;">Save Changes</button>
                </div>
            </form>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3><?= __('recent_activity') ?></h3>
            <p style="color: #666; font-style: italic;"><?= __('no_recent_activity') ?></p>
        </div>
    </div>
</div>

<?php view('footer'); ?>
