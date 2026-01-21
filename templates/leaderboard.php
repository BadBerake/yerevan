<?php 
$communityLeaderboard = $communityLeaderboard ?? [];
$title = $title ?? 'Leaderboard';
view('header', ['title' => $title]); 
?>

<div class="leaderboard-page" style="padding: 4rem 0; background: #f8fafc;">
    <div class="container">
        <?php if (empty($communityLeaderboard)): ?>
            <div style="background: #fff; padding: 3rem; border-radius: 20px; text-align: center; border: 1px dashed #cbd5e1;">
                <p style="color: #64748b;">No explorers found yet. Be the first to earn points!</p>
            </div>
        <?php else: ?>
            <!-- Hero Section -->
            <div style="text-align: center; margin-bottom: 4rem;">
                <div style="display: inline-block; background: rgba(217, 0, 18, 0.1); color: var(--primary); padding: 8px 16px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">
                    🏆 Community Rankings
                </div>
                <h1 style="font-size: 3rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem; letter-spacing: -1px;">Top Contributors</h1>
                <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Join the ranks of Yerevan's most active explorers. Earn points by writing reviews, booking events, and engaging with the community.</p>
            </div>

            <!-- Top 3 Podium -->
            <div class="podium" style="display: flex; justify-content: center; align-items: flex-end; gap: 2rem; margin-bottom: 5rem; flex-wrap: wrap;">
                <?php if (count($communityLeaderboard) >= 2): ?>
                    <!-- 2nd Place -->
                    <div class="podium-item" style="text-align: center; width: 200px;">
                        <div style="position: relative; margin-bottom: 15px;">
                            <img src="<?= $communityLeaderboard[1]['avatar_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($communityLeaderboard[1]['username']) . '&background=random' ?>" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid #cbd5e1; object-fit: cover;">
                            <div style="position: absolute; bottom: -5px; right: 60px; background: #cbd5e1; color: #475569; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem;">2</div>
                        </div>
                        <h3 style="font-weight: 700; margin-bottom: 5px;"><?= htmlspecialchars($communityLeaderboard[1]['username']) ?></h3>
                        <div style="color: var(--primary); font-weight: 800;"><?= number_format($communityLeaderboard[1]['points']) ?> ✨</div>
                    </div>
                <?php endif; ?>

                <?php if (count($communityLeaderboard) >= 1): ?>
                    <!-- 1st Place -->
                    <div class="podium-item" style="text-align: center; width: 240px; transform: translateY(-30px);">
                        <div style="position: relative; margin-bottom: 15px;">
                            <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); font-size: 2rem;">👑</div>
                            <img src="<?= $communityLeaderboard[0]['avatar_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($communityLeaderboard[0]['username']) . '&background=random' ?>" style="width: 120px; height: 120px; border-radius: 50%; border: 6px solid #facc15; object-fit: cover;">
                            <div style="position: absolute; bottom: -5px; right: 70px; background: #facc15; color: #854d0e; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">1</div>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 5px;"><?= htmlspecialchars($communityLeaderboard[0]['username']) ?></h3>
                        <div style="color: var(--primary); font-size: 1.2rem; font-weight: 800;"><?= number_format($communityLeaderboard[0]['points']) ?> ✨</div>
                    </div>
                <?php endif; ?>

                <?php if (count($communityLeaderboard) >= 3): ?>
                    <!-- 3rd Place -->
                    <div class="podium-item" style="text-align: center; width: 200px;">
                        <div style="position: relative; margin-bottom: 15px;">
                            <img src="<?= $communityLeaderboard[2]['avatar_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($communityLeaderboard[2]['username']) . '&background=random' ?>" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid #92400e; object-fit: cover; opacity: 0.8;">
                            <div style="position: absolute; bottom: -5px; right: 60px; background: #92400e; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem;">3</div>
                        </div>
                        <h3 style="font-weight: 700; margin-bottom: 5px;"><?= htmlspecialchars($communityLeaderboard[2]['username']) ?></h3>
                        <div style="color: var(--primary); font-weight: 800;"><?= number_format($communityLeaderboard[2]['points']) ?> ✨</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Full Table -->
            <div style="background: white; border-radius: 24px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                            <th style="padding: 1.5rem; color: #64748b; font-weight: 600; font-size: 0.9rem;">Rank</th>
                            <th style="padding: 1.5rem; color: #64748b; font-weight: 600; font-size: 0.9rem;">Explorer</th>
                            <th style="padding: 1.5rem; color: #64748b; font-weight: 600; font-size: 0.9rem;">Level</th>
                            <th style="padding: 1.5rem; color: #64748b; font-weight: 600; font-size: 0.9rem; text-align: right;">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($communityLeaderboard as $index => $user): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.5rem; font-weight: 700; color: #475569;">
                                    #<?= $index + 1 ?>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="<?= $user['avatar_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=random' ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($user['username']) ?></div>
                                            <div style="font-size: 0.75rem; color: #94a3b8;">Yerevan Explorer</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Level <?= $user['level'] ?></span>
                                </td>
                                <td style="padding: 1.5rem; text-align: right; font-weight: 800; color: var(--primary); font-size: 1.1rem;">
                                    <?= number_format($user['points']) ?> <span style="font-size: 0.8rem; font-weight: 400; color: #94a3b8;">✨</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- How to earn -->
        <div style="margin-top: 5rem; text-align: center;">
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 3rem;">How to Earn Points?</h2>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); text-align: center; border: 1px solid #f1f5f9;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">✍️</div>
                    <h3 style="font-weight: 700; margin-bottom: 0.5rem;">Write Reviews</h3>
                    <p style="color: #64748b; font-size: 0.9rem;">Share your experience and help others. <strong>+50 points</strong></p>
                </div>
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); text-align: center; border: 1px solid #f1f5f9;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎫</div>
                    <h3 style="font-weight: 700; margin-bottom: 0.5rem;">Book Events</h3>
                    <p style="color: #64748b; font-size: 0.9rem;">Attend festivals and concerts. <strong>+100 points</strong></p>
                </div>
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); text-align: center; border: 1px solid #f1f5f9;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">👥</div>
                    <h3 style="font-weight: 700; margin-bottom: 0.5rem;">Join Communities</h3>
                    <p style="color: #64748b; font-size: 0.9rem;">Connect with like-minded people. <strong>+15 points</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('footer'); ?>
