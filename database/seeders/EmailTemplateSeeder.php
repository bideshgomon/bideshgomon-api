<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'verification_approved',
                'name' => 'Agency Verification Approved',
                'subject' => 'Congratulations! Your Agency Verification is Approved',
                'category' => 'verification',
                'variables' => ['user_name', 'agency_name'],
                'body' => '<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #10b981;">🎉 Verification Approved!</h2>
        <p>Dear {{user_name}},</p>
        <p>We are pleased to inform you that your agency <strong>{{agency_name}}</strong> has been successfully verified!</p>
        <p>You can now access all agency features and start offering services to clients.</p>
        <p>Best regards,<br>Bidesh Gomon Team</p>
    </div>
</body>
</html>',
            ],
            [
                'slug' => 'verification_rejected',
                'name' => 'Agency Verification Rejected',
                'subject' => 'Agency Verification Update Required',
                'category' => 'verification',
                'variables' => ['user_name', 'agency_name', 'reason'],
                'body' => '<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #ef4444;">Verification Status Update</h2>
        <p>Dear {{user_name}},</p>
        <p>We have reviewed your agency verification request for <strong>{{agency_name}}</strong>.</p>
        <p><strong>Reason:</strong> {{reason}}</p>
        <p>Please update your information and resubmit for verification.</p>
        <p>Best regards,<br>Bidesh Gomon Team</p>
    </div>
</body>
</html>',
            ],
            [
                'slug' => 'wallet_credited',
                'name' => 'Wallet Credited',
                'subject' => 'Funds Added to Your Wallet - ৳{{amount}}',
                'category' => 'wallet',
                'variables' => ['user_name', 'amount', 'payment_method', 'balance'],
                'body' => '<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #10b981;">💰 Wallet Credited</h2>
        <p>Dear {{user_name}},</p>
        <p>Your wallet has been credited with <strong>৳{{amount}}</strong> via {{payment_method}}.</p>
        <p>Current Balance: <strong>৳{{balance}}</strong></p>
        <p>Thank you for using Bidesh Gomon!</p>
    </div>
</body>
</html>',
            ],
            [
                'slug' => 'withdrawal_completed',
                'name' => 'Withdrawal Completed',
                'subject' => 'Withdrawal Completed - ৳{{amount}}',
                'category' => 'wallet',
                'variables' => ['user_name', 'amount', 'account_type', 'balance'],
                'body' => '<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #3b82f6;">💸 Withdrawal Completed</h2>
        <p>Dear {{user_name}},</p>
        <p>Your withdrawal request of <strong>৳{{amount}}</strong> to {{account_type}} has been completed successfully.</p>
        <p>Remaining Balance: <strong>৳{{balance}}</strong></p>
        <p>Best regards,<br>Bidesh Gomon Team</p>
    </div>
</body>
</html>',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
