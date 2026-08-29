<?php
declare(strict_types=1);

return [
    'mode' => getenv('AA_PAYMENT_MODE') ?: 'sandbox',
    'cash_on_delivery' => ['enabled' => true],
    'visa' => ['enabled' => true, 'public_key' => getenv('AA_VISA_PUBLIC_KEY') ?: '', 'secret_key' => getenv('AA_VISA_SECRET_KEY') ?: ''],
    'mastercard' => ['enabled' => true, 'public_key' => getenv('AA_MASTERCARD_PUBLIC_KEY') ?: '', 'secret_key' => getenv('AA_MASTERCARD_SECRET_KEY') ?: ''],
    'debit_card' => ['enabled' => true, 'public_key' => getenv('AA_CARD_PUBLIC_KEY') ?: '', 'secret_key' => getenv('AA_CARD_SECRET_KEY') ?: ''],
    'jazzcash' => ['enabled' => true, 'merchant_id' => getenv('AA_JAZZCASH_MERCHANT_ID') ?: '', 'password' => getenv('AA_JAZZCASH_PASSWORD') ?: '', 'integrity_salt' => getenv('AA_JAZZCASH_SALT') ?: ''],
    'easypaisa' => ['enabled' => true, 'store_id' => getenv('AA_EASYPAISA_STORE_ID') ?: '', 'hash_key' => getenv('AA_EASYPAISA_HASH_KEY') ?: ''],
    'bank_transfer' => [
        'enabled' => true,
        'account_title' => 'AA Packages',
        'bank_name' => 'Sample Bank',
        'iban' => 'PK00SAMP0000000000000000',
    ],
];
