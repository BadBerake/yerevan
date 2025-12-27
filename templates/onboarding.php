<?php view('header', ['title' => __('onboarding_title')]); ?>

<div class="onboarding-container" style="min-height: 100vh; background: #f8fafc; padding: 4rem 1rem;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-bottom: 1rem;"><?= __('welcome_yerevango') ?></h1>
        <p style="font-size: 1.2rem; color: #64748b; margin-bottom: 3rem;"><?= __('onboarding_subtitle') ?></p>

        <form action="/onboarding/save" method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 4rem;">
                <!-- Food & Drink -->
                <label class="interest-card">
                    <input type="checkbox" name="interests[]" value="restaurants" style="display: none;">
                    <div class="card-content">
                        <span class="emoji">🍕</span>
                        <h3><?= __('foodie') ?></h3>
                        <p><?= __('foodie_desc') ?></p>
                    </div>
                </label>

                <!-- Coffee -->
                <label class="interest-card">
                    <input type="checkbox" name="interests[]" value="cafes" style="display: none;">
                    <div class="card-content">
                        <span class="emoji">☕</span>
                        <h3><?= __('coffee_lover') ?></h3>
                        <p><?= __('coffee_desc') ?></p>
                    </div>
                </label>

                <!-- Events -->
                <label class="interest-card">
                    <input type="checkbox" name="interests[]" value="events" style="display: none;">
                    <div class="card-content">
                        <span class="emoji">🎭</span>
                        <h3><?= __('art_culture') ?></h3>
                        <p><?= __('art_desc') ?></p>
                    </div>
                </label>

                <!-- Nightlife -->
                <label class="interest-card">
                    <input type="checkbox" name="interests[]" value="nightlife" style="display: none;">
                    <div class="card-content">
                        <span class="emoji">🍸</span>
                        <h3><?= __('nightlife_onboarding') ?></h3>
                        <p><?= __('nightlife_desc') ?></p>
                    </div>
                </label>

                <!-- Nature -->
                <label class="interest-card">
                    <input type="checkbox" name="interests[]" value="outdoor" style="display: none;">
                    <div class="card-content">
                        <span class="emoji">🌳</span>
                        <h3><?= __('outdoors') ?></h3>
                        <p><?= __('outdoors_desc') ?></p>
                    </div>
                </label>

                <!-- Shopping -->
                <label class="interest-card">
                    <input type="checkbox" name="interests[]" value="shopping" style="display: none;">
                    <div class="card-content">
                        <span class="emoji">🛍️</span>
                        <h3><?= __('shopping') ?></h3>
                        <p><?= __('shopping_desc') ?></p>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 15px 60px; font-size: 1.2rem; border-radius: 50px; font-weight: 700; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);">
                <?= __('continue_dashboard') ?>
            </button>
            <div style="margin-top: 1.5rem;">
                <a href="/dashboard" style="color: #94a3b8; text-decoration: none; font-size: 0.9rem;"><?= __('skip_for_now') ?></a>
            </div>
        </form>
    </div>
</div>

<style>
.interest-card {
    cursor: pointer;
}

.interest-card .card-content {
    background: white;
    padding: 2rem 1.5rem;
    border-radius: 20px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
}

.interest-card input:checked + .card-content {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1);
}

.interest-card .emoji {
    font-size: 3rem;
    display: block;
    margin-bottom: 1rem;
}

.interest-card h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 700;
}

.interest-card p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.4;
}

.interest-card:hover .card-content {
    border-color: #cbd5e1;
}
</style>

<?php view('footer'); ?>
