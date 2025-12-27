<?php view('header', ['title' => $title]); ?>

<div style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Republic_Square_Yerevan.jpg/1280px-Republic_Square_Yerevan.jpg') no-repeat center center/cover; padding: 4rem 0;">
    <div style="max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);"><?= __('login_title') ?></h2>
    
    <form action="/login" method="POST">
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('email') ?></label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('password') ?></label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;"><?= __('sign_in') ?></button>
    </form>
    
    <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
        <?= __('no_account') ?> <a href="/register" style="color: var(--primary);"><?= __('register_here') ?></a>
    </div>
</div>

    </div>
</div>

<?php view('footer'); ?>
