<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $this->view('settings/index', [
            'title' => 'Business Settings',
            'settings' => (new Setting())->allKeyed(),
            'errors' => [],
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $settings = [
            'business_name' => trim((string) input('business_name', '')),
            'business_address' => trim((string) input('business_address', '')),
            'business_phone' => trim((string) input('business_phone', '')),
            'business_email' => trim((string) input('business_email', '')),
            'currency' => trim((string) input('currency', 'NGN ')),
            'low_stock_default' => (string) max(0, (int) input('low_stock_default', 5)),
            'receipt_footer' => trim((string) input('receipt_footer', 'Thank you for your patronage.')),
        ];

        $errors = [];
        if ($settings['business_name'] === '') {
            $errors['business_name'] = 'Business name is required.';
        }
        if ($settings['business_email'] !== '' && !filter_var($settings['business_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['business_email'] = 'Enter a valid email address.';
        }

        if ($errors !== []) {
            $this->view('settings/index', ['title' => 'Business Settings', 'settings' => $settings, 'errors' => $errors]);
            return;
        }

        (new Setting())->updateMany($settings);
        set_flash('success', 'Settings updated successfully.');
        redirect(url('settings'));
    }
}
