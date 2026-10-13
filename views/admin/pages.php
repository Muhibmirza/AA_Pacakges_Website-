<section class="admin-card">
    <div class="card-head">
        <div>
            <h2>About &amp; Contact Pages</h2>
            <p class="muted-copy">Edit the main heading and introductory content shown on each public page.</p>
        </div>
    </div>
</section>

<div class="admin-grid two-col">
    <?php foreach ($managedPages as $managedPage): ?>
        <form class="admin-card page-editor" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_page">
            <input type="hidden" name="slug" value="<?= e($managedPage['slug']) ?>">
            <div class="card-head">
                <div>
                    <span class="eyebrow"><?= e(ucfirst($managedPage['slug'])) ?> page</span>
                    <h2>Edit <?= e(ucfirst($managedPage['slug'])) ?></h2>
                </div>
                <a class="btn btn-outline" href="<?= route($managedPage['slug']) ?>" target="_blank" rel="noopener">Preview</a>
            </div>
            <label>Page heading
                <input name="title" maxlength="190" value="<?= e($managedPage['title']) ?>" required>
            </label>
            <label>Introductory content
                <textarea name="content" rows="10" maxlength="10000" required><?= e(page_text($managedPage['content'])) ?></textarea>
            </label>
            <button class="btn btn-primary" type="submit">Save <?= e(ucfirst($managedPage['slug'])) ?> Page</button>
        </form>
    <?php endforeach; ?>
</div>

<section class="admin-card">
    <h2>Contact Details</h2>
    <p class="muted-copy">Phone, email, office address, postal code, hours, and map settings are managed under Site Settings.</p>
    <a class="btn btn-outline" href="<?= admin_route('settings') ?>">Open Site Settings</a>
</section>
