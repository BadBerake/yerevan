<?php view('header', ['title' => 'Add New Place']); ?>

<div style="max-width: 800px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <h2 style="margin-bottom: 2rem;">Add New Place / Event</h2>
    
    <form action="/add-place" method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name / Title</label>
                <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Category</label>
                <select name="category_id" required style="width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="1">Restaurant</option>
                    <option value="2">Cafe</option>
                    <option value="3">Event</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Address</label>
            <input type="text" name="address" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Description</label>
            <textarea name="description" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Image URL (Optional)</label>
            <input type="text" name="image_url" placeholder="https://..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div style="padding: 1rem; background: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 5px; margin-bottom: 2rem;">
            Note: All submissions are subject to admin approval before appearing on the site.
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Submit for Review</button>
    </form>
</div>

<?php view('footer'); ?>
