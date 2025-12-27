<?php view('header', ['title' => __('about_us')]); ?>
 
 <div style="background: linear-gradient(135deg, var(--color-arm-blue), var(--color-arm-red)); color: white; padding: 4rem 0; text-align: center;">
     <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 1rem;"><?= __('about_yerevango') ?></h1>
        <p style="font-size: 1.2rem; opacity: 0.9;"><?= __('about_hero_subtitle') ?></p>
     </div>
 </div>
 
 <div class="container" style="max-width: 900px; margin: 3rem auto; padding: 0 1.5rem;">
     <div style="background: white; padding: 3rem; border-radius: 20px; box-shadow: var(--shadow-lg); margin-bottom: 3rem;">
        <h2 style="color: var(--primary); margin-bottom: 1.5rem;"><?= __('our_mission') ?></h2>
         <p style="line-height: 1.8; color: var(--text-muted); font-size: 1.1rem;">
            <?= __('mission_desc') ?>
         </p>
     </div>
 
     <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
         <div style="background: white; padding: 2rem; border-radius: 16px; box-shadow: var(--shadow-lg); text-align: center;">
             <div style="font-size: 3rem; margin-bottom: 1rem;">✓</div>
            <h3 style="color: var(--primary); margin-bottom: 1rem;"><?= __('curated_selection') ?></h3>
             <p style="color: var(--text-muted); line-height: 1.6;">
                <?= __('curated_desc') ?>
             </p>
         </div>
 
         <div style="background: white; padding: 2rem; border-radius: 16px; box-shadow: var(--shadow-lg); text-align: center;">
             <div style="font-size: 3rem; margin-bottom: 1rem;">🇦🇲</div>
            <h3 style="color: var(--primary); margin-bottom: 1rem;"><?= __('local_focus') ?></h3>
             <p style="color: var(--text-muted); line-height: 1.6;">
                <?= __('local_desc') ?>
             </p>
         </div>
 
         <div style="background: white; padding: 2rem; border-radius: 16px; box-shadow: var(--shadow-lg); text-align: center;">
             <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
            <h3 style="color: var(--primary); margin-bottom: 1rem;"><?= __('community_driven') ?></h3>
             <p style="color: var(--text-muted); line-height: 1.6;">
                <?= __('community_desc') ?>
             </p>
         </div>
     </div>
 
     <div style="background: linear-gradient(135deg, var(--primary), var(--color-arm-red)); color: white; padding: 3rem; border-radius: 20px; text-align: center; box-shadow: var(--shadow-lg);">
        <h3 style="font-size: 2rem; margin-bottom: 1rem;"><?= __('join_community') ?></h3>
         <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9;">
            <?= __('join_community_desc') ?>
         </p>
         <a href="/register" style="display: inline-block; background: white; color: var(--primary); padding: 15px 40px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <?= __('register_now') ?>
         </a>
     </div>
 </div>
 
<div style="height: 4rem;"></div>
 
<?php view('footer'); ?>
