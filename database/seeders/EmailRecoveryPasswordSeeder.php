<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailRecoveryPassword;
use Illuminate\Support\Str;

class EmailRecoveryPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailTemplates = [
            'Reset Password',
            'Registration Confirmation',
            'Welcome Email',
            'Password Reset Request',
            'Password Successfully Changed',
            'Email Opt-In',
            'Account Deactivated',
            'Document Purchase Confirmation',
            'Download Link Email',
            'Free Document Access Notification',
            'Invoice or Receipt',
            'Failed Payment Notification',
            'Onboarding Email Series',
            'Support Ticket Confirmation',
            'Support Ticket Answer',
        ];

        foreach ($emailTemplates as $name) {
            
            $emailType = Str::slug($name);
            
            EmailRecoveryPassword::updateOrCreate(
                ['email_type' => $emailType], // condition to check existence
                [
                    'email_name' => $name,
                    'subject' => $name,
                    'heading' => $name,
                    'body' => $name . ' body content',
                    'button_text' => 'Click Here',
                    'footer' => 'Thank you',
                ]
            );
        }
    }
}
