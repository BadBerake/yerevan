<?php
/**
 * Search Bar Component
 */
?>
<div class="search-container">
    <form id="search-form" action="/search" method="GET">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input 
                type="text" 
                id="search-input" 
                name="q" 
                placeholder="<?= __('search_placeholder') ?? 'Search for restaurants, events, places...' ?>" 
                autocomplete="off"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            >
            <button type="submit" class="btn btn-primary" style="padding: 8px 20px; border-radius: 50px;">
                <?= __('search') ?>
            </button>
        </div>
        <div id="search-suggestions" class="search-suggestions"></div>
    </form>
</div>
