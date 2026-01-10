<?php view('header', ['title' => $item['title']]); ?>

<!-- Immersive Header -->
<div style="height: 60vh; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('<?= htmlspecialchars($item['image_url'] ?? '') ?>') no-repeat center/cover; position: relative; margin-bottom: -100px;">
    <div class="container" style="height: 100%; display: flex; align-items: flex-end; padding-bottom: 140px;">
        <div style="color: white; max-width: 800px;">
            <div style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; opacity: 0.9;">
                <?= htmlspecialchars($item['category_name'] ?? 'Place') ?>
            </div>
            <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.1;"><?= htmlspecialchars(Lang::t($item['title_translations'], $item['title'])) ?></h1>
            <div style="display: flex; gap: 20px; font-size: 1.1rem; opacity: 0.9;">
                <span style="display: flex; align-items: center; gap: 5px;">📍 <?= htmlspecialchars($item['address']) ?></span>
                <span style="display: flex; align-items: center; gap: 5px;">⭐ 4.8 (120 reviews)</span>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding: 0 1.5rem; position: relative; z-index: 10;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
        
        <!-- Main Content -->
        <div style="background: white; border-radius: 20px; padding: 2.5rem; box-shadow: var(--shadow-lg);">
            <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-main);">About this place</h2>
            <div style="line-height: 1.8; color: var(--text-muted); font-size: 1.05rem;">
                <?= nl2br(htmlspecialchars(Lang::t($item['description_translations'], $item['description']))) ?>
            </div>

            <?php
            // Decode amenities
            $amenitiesList = [];
            if (!empty($item['amenities'])) {
                $decoded = json_decode($item['amenities'], true);
                $amenitiesList = is_array($decoded) ? $decoded : [];
            }
            
            $amenityLabels = [
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

            <?php if (!empty($amenitiesList)): ?>
            <div style="margin-top: 3rem;">
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Amenities</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <?php foreach($amenitiesList as $amenity): ?>
                        <?php if (isset($amenityLabels[$amenity])): ?>
                            <div style="display: flex; gap: 10px; color: var(--text-muted);"><span style="color: var(--primary);">✓</span> <?= $amenityLabels[$amenity] ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($gallery)): ?>
            <div style="margin-top: 3rem;">
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem;"><?= __('gallery') ?? 'Gallery' ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    <?php foreach($gallery as $img): ?>
                        <div style="border-radius: 12px; overflow: hidden; height: 160px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div style="margin-top: 4rem;">
                <h3 style="font-size: 1.8rem; margin-bottom: 2rem;">Reviews (<?= count($reviews) ?>)</h3>
                
                <?php if (!empty($reviews)): ?>
                    <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 3rem;">
                        <?php foreach($reviews as $review): ?>
                            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 16px; border-left: 4px solid var(--primary);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-main); margin-bottom: 5px;"><?= htmlspecialchars($review['username']) ?></div>
                                        <div style="display: flex; gap: 3px;">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <span style="color: <?= $i <= $review['rating'] ? '#fbbf24' : '#e5e7eb' ?>; font-size: 1.2rem;">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                    </div>
                                </div>
                                <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">
                                    <?= nl2br(htmlspecialchars($review['comment'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Write Review Form -->
                <?php global $auth; ?>
                <?php if ($auth->isLoggedIn()): ?>
                    <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: var(--shadow-lg); border: 1px solid #f1f5f9;">
                        <h4 style="margin-top: 0; font-size: 1.3rem; margin-bottom: 1.5rem;">Write a Review</h4>
                        
                        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'review_submitted'): ?>
                            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
                                ✓ Review submitted successfully!
                            </div>
                        <?php endif; ?>
                        
                        <form action="/reviews/submit" method="POST">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 10px;">Rating</label>
                                <div style="display: flex; gap: 5px; font-size: 2rem;">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <label style="cursor: pointer;">
                                            <input type="radio" name="rating" value="<?= $i ?>" required style="display: none;" onchange="updateStars(this)">
                                            <span class="star" data-value="<?= $i ?>" style="color: #e5e7eb; transition: color 0.2s;">★</span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 10px;">Your Review</label>
                                <textarea name="comment" rows="4" required placeholder="Share your experience..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; resize: vertical;"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1rem;">Submit Review</button>
                        </form>
                        
                        <script>
                        const stars = document.querySelectorAll('.star');
                        stars.forEach(star => {
                            star.addEventListener('click', function() {
                                const value = parseInt(this.dataset.value);
                                stars.forEach((s, idx) => {
                                    s.style.color = (idx < value) ? '#fbbf24' : '#e5e7eb';
                                });
                                this.previousElementSibling.checked = true;
                            });
                        });
                        
                        function updateStars(input) {
                            const value = parseInt(input.value);
                            stars.forEach((s, idx) => {
                                s.style.color = (idx < value) ? '#fbbf24' : '#e5e7eb';
                            });
                        }
                        </script>
                    </div>
                <?php else: ?>
                    <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; text-align: center; border: 2px dashed #cbd5e1;">
                        <p style="margin: 0; color: var(--text-muted);">
                            <a href="/login" style="color: var(--primary); font-weight: 600;">Login</a> to write a review
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div style="position: sticky; top: 120px; height: fit-content;">
            
            <?php 
            // Check for contact info from new columns or standard fields
            $phone = $item['phone'] ?? '';
            $website = $item['website'] ?? '';
            $instagram = $item['instagram'] ?? '';
            $whatsapp = $item['whatsapp'] ?? '';
            
            // Generate WhatsApp link from phone if phone looks like mobile and whatsapp is empty
            if (empty($whatsapp) && preg_match('/^\+374[0-9]{8}$/', str_replace(' ', '', $phone))) {
                 $whatsapp = str_replace([' ', '+'], '', $phone);
            }
            ?>
            
            <?php if(!empty($phone) || !empty($website) || !empty($instagram) || !empty($whatsapp)): ?>
            <div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-lg); border: 1px solid #f1f5f9; margin-bottom: 2rem;">
                <h3 style="margin-top: 0; font-size: 1.2rem; margin-bottom: 1rem;">Contact</h3>
                <div style="display: grid; gap: 10px;">
                    <?php if(!empty($phone)): ?>
                        <a href="tel:<?= htmlspecialchars($phone) ?>" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #f1f5f9; color: var(--text-main); border-radius: 10px; text-decoration: none; font-weight: 500; transition: background 0.2s;">
                            <span>📞</span> <?= htmlspecialchars($phone) ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($website)): ?>
                        <a href="<?= htmlspecialchars(strpos($website, 'http') === 0 ? $website : 'https://'.$website) ?>" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #e2e8f0; color: var(--text-main); border-radius: 10px; text-decoration: none; font-weight: 500;">
                            <span>🌐</span> Website
                        </a>
                    <?php endif; ?>
                    
                    <?php if(!empty($instagram)): ?>
                        <a href="https://instagram.com/<?= htmlspecialchars($instagram) ?>" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #fww; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; border-radius: 10px; text-decoration: none; font-weight: 500;">
                            <span>📸</span> Instagram
                        </a>
                    <?php endif; ?>

                    <?php if(!empty($whatsapp)): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #25D366; color: white; border-radius: 10px; text-decoration: none; font-weight: 500;">
                            <span>💬</span> WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-lg); border: 1px solid #f1f5f9;">
                <h3 style="margin-top: 0; font-size: 1.4rem;">Make a Reservation</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Secure your spot at <?= htmlspecialchars($item['title']) ?>.</p>
                
                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'reservation_submitted'): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
                        🎉 Reservation Request Sent! We'll confirm shortly.
                    </div>
                <?php endif; ?>

                <?php if($auth->isLoggedIn()): ?>
                    <?php $user = $auth->getUser(); ?>
                    <form action="/reservations/submit" method="POST">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit;">
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit;">
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Phone</label>
                            <input type="tel" name="phone" required placeholder="+374 XX XXXXXX" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit;">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Date</label>
                                <input type="date" name="date" required min="<?= date('Y-m-d') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit;">
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Time</label>
                                <input type="time" name="time" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit;">
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Number of Guests</label>
                            <select name="guests" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; background: white; font-family: inherit;">
                                <option value="1">1 Person</option>
                                <option value="2" selected>2 People</option>
                                <option value="3">3 People</option>
                                <option value="4">4 People</option>
                                <option value="5">5 People</option>
                                <option value="6">6 People</option>
                                <option value="7">7 People</option>
                                <option value="8">8+ People</option>
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Special Requests (Optional)</label>
                            <textarea name="special_requests" rows="3" placeholder="Any dietary restrictions, special occasions, seating preferences..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit; resize: vertical;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1rem;">Reserve Table</button>
                        <div style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">No payment required • Free cancellation</div>
                    </form>
                <?php else: ?>
                    <div style="background: #f8fafc; padding: 2rem; border-radius: 12px; text-align: center; border: 2px dashed #cbd5e1;">
                        <p style="margin: 0; color: var(--text-muted); margin-bottom: 1rem;">Please log in to make a reservation</p>
                        <a href="/login" class="btn btn-primary">Login</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 16px; border: 1px dashed #cbd5e1; text-align: center;">
                <div style="font-weight: 600; margin-bottom: 0.5rem;">Opening Hours</div>
                <div style="color: var(--text-muted); font-size: 0.9rem;">
                    <?php if(!empty($item['working_hours'])): ?>
                        <?= htmlspecialchars($item['working_hours']) ?>
                    <?php else: ?>
                        <?= htmlspecialchars($item['opening_hours'] ?? 'Daily: 10:00 AM - 10:00 PM') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div style="height: 4rem;"></div>

<?php view('footer'); ?>
