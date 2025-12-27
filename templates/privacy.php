<?php view('header', ['title' => 'Privacy Policy']); ?>

<div style="background: linear-gradient(135deg, var(--color-arm-blue), var(--color-arm-red)); color: white; padding: 4rem 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 1rem;">Privacy Policy - YerevanGo</h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">Your privacy is important to us</p>
    </div>
</div>

<div class="container" style="max-width: 900px; margin: 3rem auto; padding: 0 1.5rem;">
    <div style="background: white; padding: 3rem; border-radius: 20px; box-shadow: var(--shadow-lg);">
        
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Last updated: <?= date('F d, Y') ?></p>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">1. Information We Collect</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                When you use Yerevango, we collect information that you provide directly to us, including:
            </p>
            <ul style="margin-left: 1.5rem; line-height: 1.8; color: var(--text-muted);">
                <li>Account information (name, email, password)</li>
                <li>Profile information and preferences</li>
                <li>Reviews and ratings you submit</li>
                <li>Reservation and booking details</li>
                <li>Communications with us</li>
            </ul>
        </section>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">2. How We Use Your Information</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                We use the information we collect to:
            </p>
            <ul style="margin-left: 1.5rem; line-height: 1.8; color: var(--text-muted);">
                <li>Provide, maintain, and improve our services</li>
                <li>Process your reservations and bookings</li>
                <li>Send you updates and promotional materials</li>
                <li>Respond to your comments and questions</li>
                <li>Protect against fraudulent or illegal activity</li>
            </ul>
        </section>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">3. Information Sharing</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                We do not sell your personal information. We may share your information with:
            </p>
            <ul style="margin-left: 1.5rem; line-height: 1.8; color: var(--text-muted);">
                <li>Restaurants and venues for reservation purposes</li>
                <li>Service providers who assist our operations</li>
                <li>Law enforcement when required by law</li>
            </ul>
        </section>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">4. Data Security</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                We implement appropriate security measures to protect your personal information. However, no method of transmission over the internet is 100% secure.
            </p>
        </section>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">5. Your Rights</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                You have the right to:
            </p>
            <ul style="margin-left: 1.5rem; line-height: 1.8; color: var(--text-muted);">
                <li>Access your personal data</li>
                <li>Correct inaccurate data</li>
                <li>Request deletion of your data</li>
                <li>Object to processing of your data</li>
                <li>Export your data</li>
            </ul>
        </section>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">6. Cookies</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                We use cookies and similar technologies to improve your experience, analyze usage, and deliver personalized content.
            </p>
        </section>

        <section style="margin-bottom: 3rem;">
            <h2 style="color: var(--primary); margin-bottom: 1rem;">7. Contact Us</h2>
            <p style="line-height: 1.8; color: var(--text-muted);">
                If you have questions about this Privacy Policy, please contact us at:
            </p>
            <p style="line-height: 1.8; color: var(--text-muted); margin-top: 1rem;">
                <strong>Email:</strong> privacy@yerevango.am<br>
                <strong>Address:</strong> Mashtots Ave 1, Yerevan, Armenia
            </p>
        </section>

        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border-left: 4px solid var(--primary); margin-top: 3rem;">
            <p style="margin: 0; color: var(--text-muted);">
                <strong>Note:</strong> We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.
            </p>
        </div>
    </div>
</div>

<div style="height: 4rem;"></div>

<?php view('footer'); ?>
