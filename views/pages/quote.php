<div class="container page-wrap">
    <section class="page-title reveal"><span class="eyebrow">Bulk and custom printing</span><h1>Request a packaging quote</h1><p>Tell us what you need and our team will prepare pricing for your quantity and printing requirements.</p></section>
    <form class="form-card reveal" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if ($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
        <div class="form-grid two">
            <label>Company / business name<input name="business_name"></label>
            <label>Contact name<input name="contact_name" required></label>
            <label>Phone<input name="phone" required></label>
            <label>Email<input type="email" name="email" required></label>
            <label>Box type<select name="box_category" required><?php foreach ($categories as $cat): ?><option><?= e($cat['name']) ?></option><?php endforeach; ?></select></label>
            <label>Size<input name="box_size" placeholder="e.g. 10 x 10 x 4 inch"></label>
            <label>Quantity needed<input type="number" min="1" name="quantity" required></label>
            <label class="checkbox-line"><input type="checkbox" name="custom_printing" value="1"> Custom printing required</label>
            <label class="wide">Upload logo (JPG/PNG/WEBP)<input type="file" name="logo_upload" accept=".jpg,.jpeg,.png,.webp" data-image-preview></label>
            <div class="upload-preview wide" data-preview-box></div>
            <label class="wide">Message<textarea name="message" rows="5"></textarea></label>
        </div>
        <button class="btn btn-primary" type="submit">Submit Quote Request</button>
    </form>
</div>

