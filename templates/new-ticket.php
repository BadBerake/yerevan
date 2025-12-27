<?php view('header', ['title' => 'New Support Ticket']); ?>

<div class="container" style="max-width: 600px; margin: 3rem auto; padding: 0 1rem;">
    <h1 style="text-align: center; margin-bottom: 2rem;">Create New Ticket</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <form action="/dashboard/support/new" method="POST">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Subject</label>
                <input type="text" name="subject" required placeholder="Briefly describe the issue..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Message</label>
                <textarea name="message" rows="8" required placeholder="Provide full details here..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-primary" style="flex: 1; padding: 12px;">Submit Ticket</button>
                <a href="/dashboard/support" class="btn btn-outline" style="padding: 12px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php view('footer'); ?>
