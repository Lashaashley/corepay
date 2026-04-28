<?php
// app/Services/JubiPayEmailService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JubiPayEmailService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $emailEndpoint;
    private string $sourceApplication;
    private string $fromEmail = 'no-reply@jubileeinsurance.com';
    private string $fromName  = 'Corepay';

    public function __construct()
    {
        $this->baseUrl           = config('services.jubipay.base_url');
        $this->username          = config('services.jubipay.username');
        $this->password          = config('services.jubipay.password');
        $this->emailEndpoint     = config('services.jubipay.email_endpoint');
          $this->sourceApplication = config('services.jubipay.source_application')  ?? 'DA';
    }


    // -------------------------------------------------------
    // PUBLIC: Call this from any controller
    // -------------------------------------------------------
    public function send(
    string  $email,
    string  $name,
    string  $subject,
    string  $message,
    string  $template    = 'general',
    array   $context     = [],
    ?string $attachmentPath     = null,   // ← new
    ?string $attachmentFilename = null    // ← new
): void {
    $accessToken = $this->getAccessToken();

    $this->dispatch(
        accessToken         : $accessToken,
        email               : $email,
        name                : $name,
        subject             : $subject,
        message             : $message,
        template            : $template,
        context             : $context,
        attachmentPath      : $attachmentPath,
        attachmentFilename  : $attachmentFilename
    );
}

    // -------------------------------------------------------
    // PRIVATE: Authenticate and return access token
    // -------------------------------------------------------
    private function getAccessToken(): string
    {
        $signinUrl = "{$this->baseUrl}/api/auth/signin";

        Log::info("JubiPayEmailService: Attempting authentication", [
            'url'      => $signinUrl,
            'username' => $this->username,
        ]);

        $response = Http::timeout(30)
            ->post($signinUrl, [
                'username' => $this->username,
                'password' => $this->password,
            ]);

        Log::info("JubiPayEmailService: Signin response received", [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if ($response->failed()) {
            Log::error("JubiPayEmailService: Authentication failed", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception(
                "JubiPay authentication failed: HTTP {$response->status()} — {$response->body()}"
            );
        }

        $data = $response->json();

        if (empty($data['accessToken'])) {
            Log::error("JubiPayEmailService: accessToken missing from response", [
                'response_keys' => array_keys($data ?? []),
            ]);
            throw new \Exception('JubiPay authentication response missing accessToken.');
        }

        Log::info("JubiPayEmailService: Token acquired successfully", [
            'token_type' => $data['tokenType'] ?? 'unknown',
            'expires_in' => $data['expires_in'] ?? 'unknown',
            'issued_at'  => $data['issued_at']  ?? 'unknown',
        ]);

        return $data['accessToken'];
    }


    // -------------------------------------------------------
    // PRIVATE: Dispatch the email via JubiPay
    // -------------------------------------------------------
    private function dispatch(
    string  $accessToken,
    string  $email,
    string  $name,
    string  $subject,
    string  $message,
    string  $template            = 'general',
    array   $context             = [],
    ?string $attachmentPath      = null,
    ?string $attachmentFilename  = null
): void {
    $endpoint = "{$this->baseUrl}{$this->emailEndpoint}";

    Log::info("JubiPayEmailService: Preparing payload", array_merge([
        'to'         => $email,
        'toName'     => $name,
        'subject'    => $subject,
        'template'   => $template,
        'endpoint'   => $endpoint,
        'attachment' => $attachmentFilename ?? 'none',
    ], $context));

    // Base multipart fields
    $multipart = [
        ['name' => 'to',                'contents' => $email],
        ['name' => 'from',              'contents' => $this->fromEmail],
        ['name' => 'message',           'contents' => $message],
        ['name' => 'subject',           'contents' => $subject],
        ['name' => 'toName',            'contents' => $name],
        ['name' => 'fromName',          'contents' => $this->fromName],
        ['name' => 'sourceApplication', 'contents' => $this->sourceApplication],
    ];

    // Attach PDF if provided
    if ($attachmentPath !== null) {
        if (!file_exists($attachmentPath)) {
            Log::error("JubiPayEmailService: Attachment file not found", [
                'path'     => $attachmentPath,
                'template' => $template,
            ]);
            throw new \Exception("Attachment file not found: {$attachmentPath}");
        }

        $multipart[] = [
            'name'     => 'file',
            'contents' => fopen($attachmentPath, 'r'),   // stream the file
            'filename' => $attachmentFilename ?? basename($attachmentPath),
        ];

        Log::info("JubiPayEmailService: Attachment added", [
            'filename' => $attachmentFilename ?? basename($attachmentPath),
            'path'     => $attachmentPath,
        ]);
    }

    $response = Http::timeout(30)
        ->withToken($accessToken)
        ->asMultipart()
        ->post($endpoint, $multipart);

    Log::info("JubiPayEmailService: Response received", [
        'template' => $template,
        'status'   => $response->status(),
        'body'     => $response->body(),
    ]);

    if ($response->failed()) {
        Log::error("JubiPayEmailService: Dispatch failed", [
            'template'  => $template,
            'status'    => $response->status(),
            'body'      => $response->body(),
            'recipient' => $email,
        ]);
        throw new \Exception(
            "JubiPay [{$template}] email failed: HTTP {$response->status()} — {$response->body()}"
        );
    }

    Log::info("JubiPayEmailService: Dispatched successfully", [
        'template'  => $template,
        'recipient' => $email,
        'status'    => $response->status(),
    ]);
}
}