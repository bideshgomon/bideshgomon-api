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
     * Uses Twilio Verify API if configured (recommended for production),
     * otherwise falls back to standard SMS or logging.
     */
    public function sendVerificationCode(UserPhoneNumber $phoneNumber, string $code): bool
    {
        $fullNumber = $this->formatToE164($phoneNumber->country_code, $phoneNumber->phone_number);

        try {
            // Prefer Twilio Verify API if service SID is configured
            if ($this->twilioVerifyConfigured()) {
                $sent = $this->sendViaTwilioVerify($fullNumber);
                if ($sent) {
                    Log::info('Twilio Verify SMS sent', ['phone' => $fullNumber]);
                    return true;
                }
            }

            // Fallback: Standard Twilio SMS
            if ($this->twilioConfigured()) {
                $message = "Your BideshGomon verification code is: {$code}. Valid for 10 minutes. Do not share this code.";
                $sent = $this->sendViaTwilio($fullNumber, $message);
                if ($sent) {
                    Log::info('Twilio SMS sent', ['phone' => $fullNumber]);
                    return true;
                }
            }

            // Fallback: log only (development)
            Log::info('SMS Verification Code (LOG ONLY FALLBACK)', [
                'phone' => $fullNumber,
                'code' => $code,
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send SMS verification code', [
                'phone' => $fullNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function twilioConfigured(): bool
    {
        return !empty(config('services.twilio.account_sid')) 
            && !empty(config('services.twilio.auth_token')) 
            && !empty(config('services.twilio.from'));
    }

    public function twilioVerifyConfigured(): bool
    {
        return !empty(config('services.twilio.account_sid')) 
            && !empty(config('services.twilio.auth_token')) 
            && !empty(config('services.twilio.verify_service_sid'));
    }

    protected function formatToE164(string $countryCode, string $localNumber): string
    {
        // Strip non-digits
        $digits = preg_replace('/\D+/', '', $localNumber);
        $countryDigits = preg_replace('/\D+/', '', $countryCode);
        // Bangladesh specific: ensure leading 0 removed when constructing international format
        if ($countryDigits === '880' && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        return '+' . $countryDigits . $digits;
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
     * Send SMS via Twilio Verify API.
     * This handles verification codes automatically with better deliverability.
     */
    protected function sendViaTwilioVerify(string $phone): bool
    {
        try {
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $verifySid = config('services.twilio.verify_service_sid');

            if (!$accountSid || !$authToken || !$verifySid) {
                Log::warning('Twilio Verify not fully configured');
                return false;
            }

            $twilio = new \Twilio\Rest\Client($accountSid, $authToken);
            
            $verification = $twilio->verify->v2
                ->services($verifySid)
                ->verifications
                ->create($phone, 'sms');

            Log::info('Twilio Verify initiated', [
                'phone' => $phone,
                'status' => $verification->status,
                'sid' => $verification->sid,
            ]);

            return $verification->status === 'pending';
        } catch (\Exception $e) {
            Log::error('Twilio Verify failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Verify code using Twilio Verify API.
     */
    public function verifyTwilioCode(string $phone, string $code): bool
    {
        try {
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $verifySid = config('services.twilio.verify_service_sid');

            if (!$accountSid || !$authToken || !$verifySid) {
                return false;
            }

            $twilio = new \Twilio\Rest\Client($accountSid, $authToken);
            
            $verificationCheck = $twilio->verify->v2
                ->services($verifySid)
                ->verificationChecks
                ->create([
                    'to' => $phone,
                    'code' => $code,
                ]);

            Log::info('Twilio Verify check', [
                'phone' => $phone,
                'status' => $verificationCheck->status,
            ]);

            return $verificationCheck->status === 'approved';
        } catch (\Exception $e) {
            Log::error('Twilio Verify check failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS via Twilio.
     * Uncomment and configure when ready.
     */
    protected function sendViaTwilio(string $phone, string $message): bool
    {
        try {
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $fromNumber = config('services.twilio.from');

            if (!$accountSid || !$authToken || !$fromNumber) {
                Log::warning('Twilio not fully configured; falling back to log', [
                    'account_sid' => (bool) $accountSid,
                    'auth_token' => $authToken ? '***' : null,
                    'from' => $fromNumber,
                ]);
                return false;
            }

            $twilio = new \Twilio\Rest\Client($accountSid, $authToken);
            $twilio->messages->create($phone, [
                'from' => $fromNumber,
                'body' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio SMS failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
