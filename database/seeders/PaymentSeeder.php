<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;
use App\Models\PaymentGatewaySetting;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // بيانات بوابات الدفع
        $gateways = [
            [
                'name' => 'cash',
                'image' => 'images/payment/cash.png',
                'description' => ['en' => 'Cash on Delivery', 'ar' => 'الدفع عند التسليم'],
                'fees_type' => 'fixed',
                'fees' => '5',
                'status' => '1',
            ],
            // [
            //     'name' => 'bank',
            //     'image' => 'images/payment/bank.png',
            //     'description' => ['en' => 'Bank Transfer', 'ar' => 'التحويل البنكي'],
            //     'fees_type' => 'percentage',
            //     'fees' => '0',
            //     'status' => '0',
            //     'settings' => [
            //         'IBAN' => 'FR76XXX1500000000000000000000',
            //         'SWIFT' => 'SJH5XXX',
            //         'BANK_NAME' => 'Bank Name',
            //     ]
            // ],
            [
                'name' => 'kashier',
                'image' => 'images/payment/kashier.png',
                'description' => ['en' => 'Visa - MasterCard - Debit Card - Vodafone Cash', 'ar' => 'فيزا - ماستركارد - ديبيت كارد - فودافون كاش'],
                'fees_type' => 'percentage',
                'fees' => '2.9',
                'status' => '0',
                'settings' => [
                    'KASHIER_ACCOUNT_KEY' => 'MID-18804-488',
                    'KASHIER_IFRAME_KEY' => '3f9d2df5-3287-42b8-8d4c-b94c2d536a56',
                    'KASHIER_TOKEN' => 'f5d433ad3517c5457dac7cb6189a2055$2fa2412fc64dc1b6fc907384bb6fa136582c49d3f68331446c51f059aa86371daea7341fb1c20bd70b0471f747f0f2b6'
                ]
            ],
            // [
            //     'name' => 'stripe',
            //     'image' => 'images/payment/stripe.png',
            //     'description' => ['en' => 'Visa - MasterCard - Debit Card - Stripe', 'ar' => 'فيزا - ماستركارد - ديبيت كارد - استرايب'],
            //     'fees_type' => 'percentage',
            //     'fees' => '3.9',
            //     'status' => '0',
            //     'settings' => [
            //         'STRIPE_API_KEY' => 'pk_test_TYooMQauvdEDq54NiTphI7jx',
            //         'STRIPE_API_SECRET' => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc'
            //     ]
            // ],
            // [
            //     'name' => 'paymob',
            //     'image' => 'images/payment/paymob.png',
            //     'description' => ['en' => 'Visa - MasterCard - Debit Card - Paymob', 'ar' => 'فيزا - ماستركارد - ديبيت كارد - بايموب'],
            //     'fees_type' => 'percentage',
            //     'fees' => '3.4',
            //     'status' => '0',
            //     'settings' => [
            //         'PAYMOB_API_KEY' => 'pk_test_TYooMQauvdEDq54NiTphI7jx',
            //         'PAYMOB_INTEGRATION_ID' => '12088433',
            //         'PAYMOB_IFRAME_ID' => '12088433',
            //         'PAYMOB_HMAC' => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc'
            //     ]
            // ],
            // [
            //     'name' => 'paypal',
            //     'image' => 'images/payment/paypal.png',
            //     'description' => ['en' => 'Paypal', 'ar' => 'بايبال'],
            //     'fees_type' => 'percentage',
            //     'fees' => '4.9',
            //     'status' => '0',
            //     'settings' => [
            //         'PAYPAL_CLIENT_ID' => 'sb-12088433@business.example.com',
            //         'PAYPAL_SECRET' => 'EJH5XXX',
            //     ]
            // ],
            // [
            //     'name' => 'hyperpay',
            //     'image' => 'images/payment/hyperpay.png',
            //     'description' => ['en' => 'Visa - MasterCard - Debit Card - Hyperpay', 'ar' => 'فيزا - ماستركارد - ديبيت كارد - هابرباي'],
            //     'fees_type' => 'percentage',
            //     'fees' => '3.7',
            //     'status' => '0',
            //     'settings' => [
            //         'HYPERPAY_BASE_URL' => 'https://eu-test.oppwa.com',
            //         'HYPERPAY_URL' => 'https://eu-test.oppwa.com/v1/checkouts',
            //         'HYPERPAY_TOKEN' => '8a829418564c13bb01564c6d631d144c',
            //         'HYPERPAY_CREDIT_ID' => '8a829418564c13bb01564c6d631d144c',
            //         'HYPERPAY_MADA_ID' => '8a829418564c13bb01564c6d631d144c',
            //         'HYPERPAY_APPLE_ID' => '8a829418564c13bb01564c6d631d144c',
            //     ]
            // ],
            // [
            //     'name' => 'tap',
            //     'image' => 'images/payment/tap.png',
            //     'description' => ['en' => 'Tap Payments', 'ar' => 'تاب للمدفوعات'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.5',
            //     'status' => '0',
            //     'settings' => [
            //         'TAP_SECRET_KEY' => 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
            //         'TAP_PUBLIC_KEY' => 'pk_test_EtHFV4BuPQokJT6jiROls87Y',
            //     ]
            // ],
            // [
            //     'name' => 'fawry',
            //     'image' => 'images/payment/fawry.png',
            //     'description' => ['en' => 'Fawry Pay', 'ar' => 'فوري'],
            //     'fees_type' => 'percentage',
            //     'fees' => '3.0',
            //     'status' => '0',
            //     'settings' => [
            //         'FAWRY_SECRET' => 'your-fawry-secret',
            //         'FAWRY_MERCHANT' => 'your-merchant-id'
            //     ]
            // ],
            // [
            //     'name' => 'thawani',
            //     'image' => 'images/payment/thawani.png',
            //     'description' => ['en' => 'Thawani Payment Gateway', 'ar' => 'ثواني'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.8',
            //     'status' => '0',
            //     'settings' => [
            //         'THAWANI_API_KEY' => 'your-api-key',
            //         'THAWANI_PUBLISHABLE_KEY' => 'your-publishable-key'
            //     ]
            // ],
            // [
            //     'name' => 'opay',
            //     'image' => 'images/payment/opay.png',
            //     'description' => ['en' => 'OPay Payment Solutions', 'ar' => 'أوباي'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.7',
            //     'status' => '0',
            //     'settings' => [
            //         'OPAY_SECRET_KEY' => 'your-secret-key',
            //         'OPAY_PUBLIC_KEY' => 'your-public-key',
            //         'OPAY_MERCHANT_ID' => 'your-merchant-id'
            //     ]
            // ],
            // [
            //     'name' => 'paytabs',
            //     'image' => 'images/payment/paytabs.png',
            //     'description' => ['en' => 'PayTabs Payment Gateway', 'ar' => 'باي تابس'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.9',
            //     'status' => '0',
            //     'settings' => [
            //         'PAYTABS_PROFILE_ID' => 'your-profile-id',
            //         'PAYTABS_SERVER_KEY' => 'your-server-key'
            //     ]
            // ],
            // [
            //     'name' => 'binance',
            //     'image' => 'images/payment/binance.png',
            //     'description' => ['en' => 'Binance Pay', 'ar' => 'باينانس'],
            //     'fees_type' => 'percentage',
            //     'fees' => '1.5',
            //     'status' => '0',
            //     'settings' => [
            //         'BINANCE_API' => 'your-api-key',
            //         'BINANCE_SECRET' => 'your-secret-key'
            //     ]
            // ],
            // [
            //     'name' => 'nowpayments',
            //     'image' => 'images/payment/nowpayments.png',
            //     'description' => ['en' => 'NOWPayments', 'ar' => 'ناو بايمنتس'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.0',
            //     'status' => '0',
            //     'settings' => [
            //         'NOWPAYMENTS_API_KEY' => 'your-api-key'
            //     ]
            // ],
            // [
            //     'name' => 'payeer',
            //     'image' => 'images/payment/payeer.png',
            //     'description' => ['en' => 'Payeer Payment System', 'ar' => 'باير'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.5',
            //     'status' => '0',
            //     'settings' => [
            //         'PAYEER_MERCHANT_ID' => 'your-merchant-id',
            //         'PAYEER_API_KEY' => 'your-api-key',
            //         'PAYEER_ADDITIONAL_API_KEY' => 'your-additional-key'
            //     ]
            // ],
            // [
            //     'name' => 'perfectmoney',
            //     'image' => 'images/payment/perfectmoney.png',
            //     'description' => ['en' => 'Perfect Money', 'ar' => 'برفكت مني'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.0',
            //     'status' => '0',
            //     'settings' => [
            //         'PERFECT_MONEY_ID' => 'UXXXXXXX',
            //         'PERFECT_MONEY_PASSPHRASE' => 'your-passphrase'
            //     ]
            // ],
            // [
            //     'name' => 'telr',
            //     'image' => 'images/payment/telr.png',
            //     'description' => ['en' => 'Telr Payment Gateway', 'ar' => 'تيلر'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.8',
            //     'status' => '0',
            //     'settings' => [
            //         'TELR_MERCHANT_ID' => 'your-merchant-id',
            //         'TELR_API_KEY' => 'your-api-key'
            //     ]
            // ],
            // [
            //     'name' => 'clickpay',
            //     'image' => 'images/payment/clickpay.png',
            //     'description' => ['en' => 'ClickPay Payment Solutions', 'ar' => 'كليك باي'],
            //     'fees_type' => 'percentage',
            //     'fees' => '2.6',
            //     'status' => '0',
            //     'settings' => [
            //         'CLICKPAY_SERVER_KEY' => 'your-server-key',
            //         'CLICKPAY_PROFILE_ID' => 'your-profile-id'
            //     ]
            // ]
        ];

        // إنشاء أو تحديث السجلات
        foreach ($gateways as $gateway) {
            $paymentGateway = PaymentGateway::firstOrCreate(['name' => $gateway['name']], [
                'image' => $gateway['image'],
                'description' => $gateway['description'],
                'fees_type' => $gateway['fees_type'],
                'fees' => $gateway['fees'],
                'status' => $gateway['status']
            ]);

            // التحقق من وجود الإعدادات قبل التكرار عليها
            if (isset($gateway['settings'])) {
                foreach ($gateway['settings'] as $key => $value) {
                    PaymentGatewaySetting::firstOrCreate(
                        ['gateway_id' => $paymentGateway->id, 'key' => $key],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
