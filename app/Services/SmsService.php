<?php

namespace App\Services;

use App\Models\UserPhoneNumber;
use App\Models\PhoneVerificationCode;
use Illuminate\Support\Facades\Log;
use Exception;

class SmsService
{
    /**
     * Send verification code via SMS.
     * 
     * For now, this logs the code for testing.
     * In production, integrate with SMS gateway (Twilio, BulkSMS BD, SSL Wireless, etc.)
     */
    public function sendVerificationCode(UserPhoneNumber $phoneNumber, string $code): bool
    {
        try {
            $fullNumber = $phoneNumber->country_code . $phoneNumber->phone_number;
            $message = "Your BideshGomon verification code is: {$code}. Valid for 10 minutes. Do not share this code.";

            // Log for testing/development
            Log::info('SMS Verification Code', [
                'phone' => $fullNumber,
                'code' => $code,
                'message' => $message,
            ]);

            // TODO: Integrate with SMS gateway
            // Example for Bangladesh SMS providers:
            
            // Option 1: SSL Wireless
            // return $this->sendViaSslWireless($fullNumber, $message);
            
            // Option 2: Bulk SMS BD
            // return $this->sendViaBulkSmsBd($fullNumber, $message);
            
            // Option 3: Twilio (International)
            // return $this->sendViaTwilio($fullNumber, $message);

            // For now, return true in development
            // In production, this should actually send the SMS
            return true;

        } catch (Exception $e) {
            Log::error('Failed to send SMS verification code', [
                'phone' => $phoneNumber->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS via SSL Wireless (Bangladesh provider).
     * Uncomment and configure when ready.
     */
    // protected function sendViaSslWireless(string $phone, string $message): bool
    // {
    //     $apiToken = config('services.sms.ssl_wireless.token');
    //     $sid = config('services.sms.ssl_wireless.sid');
    //     
    //     $response = Http::post('https://smsplus.sslwireless.com/api/v3/send-sms', [
    //         'api_token' => $apiToken,
    //         'sid' => $sid,
    //         'msisdn' => $phone,
    //         'sms' => $message,
    //     ]);
    //     
    //     return $response->successful();
    // }

    /**
     * Send SMS via Twilio.
     * Uncomment and configure when ready.
     */
    // protected function sendViaTwilio(string $phone, string $message): bool
    // {
    //     $accountSid = config('services.twilio.account_sid');
    //     $authToken = config('services.twilio.auth_token');
    //     $fromNumber = config('services.twilio.from');
    //     
    //     $twilio = new \Twilio\Rest\Client($accountSid, $authToken);
    //     
    //     try {
    //         $twilio->messages->create($phone, [
    //             'from' => $fromNumber,
    //             'body' => $message,
    //         ]);
    //         return true;
    //     } catch (\Exception $e) {
    //         Log::error('Twilio SMS failed: ' . $e->getMessage());
    //         return false;
    //     }
    // }
}
