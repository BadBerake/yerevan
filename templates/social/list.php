<?php view('header', ['title' => __('explore_communities')]); ?>
 
 <div class="container" style="max-width: 1200px; margin: 3rem auto; padding: 0 1.5rem;">
     <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
         <div>
            <h1 style="font-size: 2.5rem; margin: 0; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?= __('explore_communities') ?></h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem;"><?= __('communities_subtitle') ?></p>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary" style="padding: 12px 24px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.2rem;">+</span> <?= __('create_community') ?>
        </button>
     </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 3rem;">
        <form action="/communities" method="GET" style="display: flex; gap: 10px; max-width: 600px;">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="<?= __('search_communities_ph') ?>" style="flex: 1; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 14px; outline: none; box-shadow: var(--shadow-sm); font-size: 1rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0 25px; border-radius: 14px;"><?= __('search_btn') ?></button>
        </form>
    </div>

    <!-- Communities Grid -->
    <?php if (empty($communities)): ?>
        <div style="text-align: center; padding: 5rem 2rem; background: #f8fafc; border-radius: 24px; border: 2px dashed #e2e8f0;">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">🏘️</div>
            <h3 style="margin-bottom: 1rem;"><?= __('no_communities_found') ?></h3>
            <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto;"><?= __('no_communities_desc') ?></p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($communities as $community): ?>
                <div style="background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid #f1f5f9; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-md)'">
                    <div style="height: 160px; background: #f1f5f9 url('<?= $community['image_url'] ?: '/public/img/placeholder.jpg' ?>') center/cover no-repeat; position: relative;">
                        <?php if ($community['is_member']): ?>
                            <div style="position: absolute; top: 15px; right: 15px; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid #bbf7d0;"><?= __('member_badge') ?></div>
                        <?php endif; ?>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($community['name']) ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.7rem;">
                            <?= htmlspecialchars($community['description'] ?: __('no_description')) ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.85rem; color: #64748b; font-weight: 500;">👥 <?= $community['member_count'] ?> <?= __('members_count') ?></span>
                            <a href="/community/<?= $community['slug'] ?>" class="btn <?= $community['is_member'] ? 'btn-outline' : 'btn-primary' ?>" style="padding: 8px 20px; border-radius: 10px; font-size: 0.9rem;">
                                <?= $community['is_member'] ? __('open_btn') : __('join_btn') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="createModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; padding: 2.5rem; border-radius: 24px; width: 100%; max-width: 500px; box-shadow: var(--shadow-2xl);">
        <h2 style="margin-top: 0; margin-bottom: 0.5rem;"><?= __('create_community') ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;"><?= __('start_discussion_space') ?></p>
        
        <form action="/communities/create" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?= __('community_name_label') ?></label>
                <input type="text" name="name" required placeholder="<?= __('community_name_ph') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?= __('description_label') ?></label>
                <textarea name="description" rows="3" placeholder="<?= __('description_ph') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit;"></textarea>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;"><?= __('cover_image_label') ?></label>
                <input type="file" name="image" accept="image/*, image/webp" style="width: 100%;">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 2; padding: 12px; border-radius: 12px;"><?= __('create_now_btn') ?></button>
                <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn btn-outline" style="flex: 1; padding: 12px; border-radius: 12px;"><?= __('cancel_btn') ?></button>
            </div>
        </form>
    </div>
</div>

<?php view('footer'); ?>
