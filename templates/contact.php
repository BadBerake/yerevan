<?php view('header', ['title' => __('contact_support')]); ?>
 
 <div class="container" style="max-width: 600px; margin: 2rem auto; padding: 0 1rem;">
    <h1 style="text-align: center; margin-bottom: 2rem;"><?= __('contact_support') ?></h1>
    
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <form action="/contact" method="POST">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('subject') ?></label>
                <select name="subject" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option><?= __('general_inquiry') ?></option>
                    <option><?= __('report_issue') ?></option>
                    <option><?= __('business_partnership') ?></option>
                    <option><?= __('account_support') ?></option>
                </select>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('email') ?></label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;"><?= __('message') ?></label>
                <textarea name="message" rows="6" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
            </div>
            
            <button class="btn btn-primary" style="width: 100%; padding: 12px;"><?= __('send_ticket') ?></button>
         </form>
     </div>
 </div>

<?php view('footer'); ?>
