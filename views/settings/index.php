<?php $value = static fn(string $key, mixed $default = '') => e($settings[$key] ?? $default); ?>

<section class="panel">
    <form method="post" action="<?= e(url('settings/update')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="business_name">Business Name <span class="text-danger">*</span></label>
                <input class="form-control <?= isset($errors['business_name']) ? 'is-invalid' : '' ?>" id="business_name" name="business_name" value="<?= $value('business_name', 'Local Business') ?>" required>
                <?php if (isset($errors['business_name'])): ?><div class="invalid-feedback"><?= e($errors['business_name']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="business_phone">Business Phone</label>
                <input class="form-control" id="business_phone" name="business_phone" value="<?= $value('business_phone') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="business_email">Business Email</label>
                <input class="form-control <?= isset($errors['business_email']) ? 'is-invalid' : '' ?>" id="business_email" name="business_email" type="email" value="<?= $value('business_email') ?>">
                <?php if (isset($errors['business_email'])): ?><div class="invalid-feedback"><?= e($errors['business_email']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="currency">Currency</label>
                <input class="form-control" id="currency" name="currency" value="<?= $value('currency', 'NGN ') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="low_stock_default">Default Low Stock Level</label>
                <input class="form-control" id="low_stock_default" name="low_stock_default" type="number" min="0" value="<?= $value('low_stock_default', 5) ?>">
            </div>
            <div class="col-12">
                <label class="form-label" for="business_address">Business Address</label>
                <textarea class="form-control" id="business_address" name="business_address" rows="3"><?= $value('business_address') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label" for="receipt_footer">Receipt Footer</label>
                <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="2"><?= $value('receipt_footer', 'Thank you for your patronage.') ?></textarea>
            </div>
        </div>

        <button class="btn btn-primary mt-4" type="submit"><i class="bi bi-save"></i> Save Settings</button>
    </form>
</section>
