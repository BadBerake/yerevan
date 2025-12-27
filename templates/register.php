<?php view('header', ['title' => $title]); ?>

<div style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Yerevan_Cascade_night.jpg/1280px-Yerevan_Cascade_night.jpg') no-repeat center bottom/cover; padding: 4rem 0;">
    <div style="max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);"><?= __('create_account') ?></h2>
    
    <form action="/register" method="POST">
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('username') ?></label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>
 
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('email') ?></label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('password') ?></label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;"><?= __('sign_up') ?></button>
    </form>
    
    <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
        <?= __('already_have_account') ?> <a href="/login" style="color: var(--primary);"><?= __('login_here') ?></a>
    </div>
</div>

    </div>
</div>

<?php view('footer'); ?>
