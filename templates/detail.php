<?php view('header', ['title' => $item['title']]); ?>
<?php view('components/structured-data', ['item' => $item, 'reviews' => $reviews ?? []]); ?>

<!-- Immersive Header -->
<div class="detail-header lazy-bg" data-src="<?= htmlspecialchars($item['image_url'] ?? '') ?>" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8)); background-position: center; background-size: cover;">
    <div class="container detail-header-content">
        <div style="color: white; max-width: 800px;">
            <div style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; opacity: 0.9;">
                <?= htmlspecialchars($item['category_name'] ?? 'Place') ?>
            </div>
            <h1 style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; margin-bottom: 1rem; line-height: 1.1;"><?= htmlspecialchars(Lang::t($item['title_translations'], $item['title'])) ?></h1>
            <div style="display: flex; flex-wrap: wrap; gap: 20px; font-size: 1.1rem; opacity: 0.9;">
                <span style="display: flex; align-items: center; gap: 5px;">📍 <?= htmlspecialchars($item['address'] ?? '') ?></span>
                <span style="display: flex; align-items: center; gap: 5px;">⭐ 4.8 (120 reviews)</span>
            </div>
        </div>
    </div>
    
    <!-- Favorite Button Floating Action -->
    <button id="favBtn" onclick="toggleFavorite(<?= $item['id'] ?>)" style="position: absolute; bottom: -25px; right: 40px; z-index: 20; background: white; border: none; width: 50px; height: 50px; border-radius: 50%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s;">
        <svg id="favIcon" viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: <?= ($is_favorited ?? false) ? '#ef4444' : 'none' ?>; stroke: #ef4444; stroke-width: 2px;">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
    </button>
</div>

<script>
async function toggleFavorite(itemId) {
    const btn = document.getElementById('favBtn');
    const icon = document.getElementById('favIcon');
    
    // Animation feedback
    btn.style.transform = 'scale(0.9)';
    setTimeout(() => btn.style.transform = 'scale(1)', 150);

    try {
        const response = await fetch('/favorite/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ item_id: itemId })
        });
        
        const data = await response.json();
        
        if (data.status === 'added') {
            icon.style.fill = '#ef4444';
        } else if (data.status === 'removed') {
            icon.style.fill = 'none';
        } else if (data.status === 'error' && data.message === 'Not logged in') {
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>

<div class="container" style="padding: 0 1.5rem;">
    <?php view('components/breadcrumbs', ['item' => $item]); ?>
    <div class="detail-grid">
        
        <!-- Main Content -->
        <div class="detail-main">
            <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-main);">About this place</h2>
            <div style="line-height: 1.8; color: var(--text-muted); font-size: 1.05rem; margin-bottom: 2rem;">
                <?= nl2br(htmlspecialchars(Lang::t($item['description_translations'], $item['description']))) ?>
            </div>

            <?php if ($item['category_name'] == 'Events' || isset($item['event_date'])): ?>
            <div id="bookingWidget" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 2rem; margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem;">Book Your Tickets</h3>
                        <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 0.9rem;">
                            <i class="far fa-calendar-alt"></i> <?= date('F d, Y • H:i', strtotime($item['event_date'])) ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                            <?= $item['ticket_price'] > 0 ? number_format($item['ticket_price']) . ' AMD' : 'FREE' ?>
                        </span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">per person</span>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px;">Number of Tickets</label>
                        <select id="ticketCount" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 12px; background: white;">
                            <?php for($i=1; $i<=10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> <?= $i == 1 ? 'Ticket' : 'Tickets' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div style="flex: 1.5;">
                        <button onclick="bookTicket(<?= $item['id'] ?>)" id="bookBtn" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 12px; font-weight: 700;">
                            Book Now
                        </button>
                    </div>
                </div>
                
                <div id="bookingMessage" style="margin-top: 1rem; padding: 1rem; border-radius: 12px; display: none;"></div>
            </div>

            <script>
            async function bookTicket(eventId) {
                const btn = document.getElementById('bookBtn');
                const msg = document.getElementById('bookingMessage');
                const count = document.getElementById('ticketCount').value;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                try {
                    const response = await fetch('/api/events/book', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ event_id: eventId, count: count })
                    });
                    const data = await response.json();

                    msg.style.display = 'block';
                    if (data.status === 'success') {
                        msg.style.background = '#f0fdf4';
                        msg.style.color = '#15803d';
                        msg.style.border = '1px solid #bbf7d0';
                        msg.innerHTML = `<strong>Success!</strong> Your booking is confirmed. Code: <strong>${data.booking_code}</strong>. Check your dashboard for details.`;
                        btn.innerHTML = 'Booked ✓';
                    } else {
                        msg.style.background = '#fef2f2';
                        msg.style.color = '#b91c1c';
                        msg.style.border = '1px solid #fecaca';
                        msg.innerHTML = `<strong>Error:</strong> ${data.message || 'Something went wrong'}`;
                        btn.disabled = false;
                        btn.innerHTML = 'Book Now';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    btn.disabled = false;
                    btn.innerHTML = 'Book Now';
                }
            }
            </script>
            <?php endif; ?>

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
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px;">
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
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div id="reviews" style="margin-top: 4rem;">
                <h3 style="font-size: 1.8rem; margin-bottom: 2rem;">Reviews</h3>
                
                <!-- Rating Summary -->
                <?php view('components/rating-summary', [
                    'item' => $item,
                    'reviewService' => $reviewService
                ]); ?>

                <!-- Review Items -->
                <div class="reviews-list" style="margin-top: 3rem;">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach($reviews as $review): ?>
                            <?php view('components/review-card', [
                                'review' => $review,
                                'userVotes' => $userVotes ?? []
                            ]); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; background: #f8fafc; border-radius: 20px; border: 1px dashed #cbd5e1;">
                            <p style="color: var(--text-muted);"><?= __('no_reviews_yet') ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Write Review Form -->
                <?php view('components/review-form', [
                    'item' => $item
                ]); ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="detail-sidebar">
            
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
                
                <!-- Share Buttons integrated in contact box for better flow -->
                <?php view('components/share-buttons', ['item' => $item]); ?>
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
                    <form id="reservationForm" onsubmit="submitReservation(event)">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit; background: #f1f5f9; cursor: not-allowed;">
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main);">Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit; background: #f1f5f9; cursor: not-allowed;">
                            </div>
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
                        
                        <div id="reservationMsg" style="margin-bottom: 15px; display: none; padding: 10px; border-radius: 8px;"></div>

                        <button type="submit" id="reserveBtn" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1rem;">Reserve Table</button>
                        <div style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">No payment required • Free cancellation</div>
                    </form>

                    <script>
                    async function submitReservation(e) {
                        e.preventDefault();
                        const form = e.target;
                        const btn = document.getElementById('reserveBtn');
                        const msg = document.getElementById('reservationMsg');
                        const formData = new FormData(form);
                        
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        msg.style.display = 'none';

                        try {
                            const response = await fetch('/api/bookings/create', {
                                method: 'POST',
                                body: formData
                            });
                            
                            const data = await response.json();
                            
                            msg.style.display = 'block';
                            if (data.status === 'success') {
                                msg.style.background = '#dcfce7';
                                msg.style.color = '#166534';
                                msg.innerHTML = '🎉 Reservation Confirmed!';
                                btn.innerHTML = 'Reserved ✓';
                                setTimeout(() => window.location.href = '/dashboard', 2000);
                            } else {
                                msg.style.background = '#fee2e2';
                                msg.style.color = '#991b1b';
                                msg.innerHTML = data.message || 'Error occurred';
                                btn.disabled = false;
                                btn.innerHTML = 'Reserve Table';
                            }
                        } catch (err) {
                            console.error(err);
                            msg.style.display = 'block';
                            msg.style.background = '#fee2e2';
                            msg.style.color = '#991b1b';
                            msg.innerHTML = 'Network error. Please try again.';
                            btn.disabled = false;
                            btn.innerHTML = 'Reserve Table';
                        }
                    }
                    </script>
                <?php else: ?>
                    <div style="background: #f8fafc; padding: 2rem; border-radius: 12px; text-align: center; border: 2px dashed #cbd5e1;">
                        <p style="margin: 0; color: var(--text-muted); margin-bottom: 1rem;">Please log in to make a reservation</p>
                        <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary">Login</a>
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
