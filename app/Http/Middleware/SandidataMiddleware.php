<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SandidataMiddleware
{
    private static bool $initialized = false;
    private static string $certFile      = '';
    private static string $keyFile       = '';
    private static string $certPassword  = '';
    private static string $baseUrl       = '';

    // ----------------------------------------------------------------
    // Init — hanya sekali per request cycle
    // ----------------------------------------------------------------
    private static function init(): void
    {
        if (self::$initialized) {
            return;  // ← guard: tidak init ulang
        }

        self::$baseUrl      = rtrim(config('seal.base_url', ''));
        self::$certFile     = rtrim(config('seal.cert_file', ''));
        self::$keyFile      = rtrim(config('seal.key_file', ''));
        self::$certPassword = rtrim(config('seal.cert_password', ''));

        if (empty(self::$baseUrl)) {
            throw new \Exception('SEAL_BASE_URL belum dikonfigurasi');
        }

        if (!file_exists(self::$certFile)) {
            throw new \Exception('SEAL cert tidak ditemukan: ' . self::$certFile);
        }

        if (!file_exists(self::$keyFile)) {
            throw new \Exception('SEAL key tidak ditemukan: ' . self::$keyFile);
        }

        self::$initialized = true;

        Log::debug('SandidataMiddleware initialized', [
            'baseUrl' => self::$baseUrl,
        ]);
    }

    // ----------------------------------------------------------------
    // sendRequest
    // ----------------------------------------------------------------
    private static function sendRequest(string $url, string $payload): array
    {
        self::init();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
            CURLOPT_SSLCERT        => self::$certFile,
            CURLOPT_SSLKEY         => self::$keyFile,
            CURLOPT_SSLKEYPASSWD   => self::$certPassword,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $t0       = microtime(true);
        $response = curl_exec($ch);
        $duration = round((microtime(true) - $t0) * 1000, 2);
        $error    = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);

        if ($error) {
            Log::error('SEAL cURL error', [
                'url'      => $url,
                'error'    => $error,
                'duration' => $duration . 'ms',
            ]);
        } else {
            Log::debug('SEAL request OK', [
                'url'      => $url,
                'duration' => $duration . 'ms',
            ]);
        }

        return [$response, $error];
    }

    // ----------------------------------------------------------------
    // Public API
    // ----------------------------------------------------------------
    public static function seal(string $plaintext): array
    {
        self::init();  // ← init sebelum pakai $baseUrl

        $payload = json_encode([
            'Plaintext' => [['text' => $plaintext]]
        ]);

        return self::sendRequest(self::$baseUrl . '/seal', $payload);
    }

    public static function unseal(string $ciphertext): array
    {
        self::init();  // ← init sebelum pakai $baseUrl

        $payload = json_encode([
            'Ciphertext' => [['text' => $ciphertext]]
        ]);

        return self::sendRequest(self::$baseUrl . '/unseal', $payload);
    }

    // ----------------------------------------------------------------
    // Helper publik — enkripsi single value
    // ----------------------------------------------------------------
    public static function encryptValue(string $value): string
    {
        if (empty($value)) {
            return $value;
        }

        // Skip kalau sudah terenkripsi
        if (str_starts_with($value, 's0:')) {
            return $value;
        }

        [$response, $error] = self::seal($value);

        if ($error || !$response) {
            Log::error('SEAL encryptValue gagal', ['error' => $error]);
            return $value; // fallback plaintext
        }

        $json = json_decode($response, true);
        return $json['Ciphertext'][0]['text'] ?? $value;
    }

    // ----------------------------------------------------------------
    // Helper publik — dekripsi single value
    // ----------------------------------------------------------------
    public static function decryptValue(string $value): string
    {
        if (empty($value) || !str_starts_with($value, 's0:')) {
            return $value;
        }

        [$response, $error] = self::unseal($value);

        if ($error || !$response) {
            Log::error('SEAL decryptValue gagal', ['error' => $error]);
            return $value;
        }

        $json = json_decode($response, true);
        return $json['Plaintext'][0]['text'] ?? $value;
    }

    // ----------------------------------------------------------------
    // Middleware handle
    // ----------------------------------------------------------------
    public function handle(Request $request, Closure $next, string ...$fields): mixed
    {
        Log::info('SandidataMiddleware START', [
            'url'    => $request->url(),
            'fields' => $fields,
        ]);

        foreach ($fields as $field) {
            $this->encryptField($request, $field);
        }

        $response = $next($request);

        Log::info('SandidataMiddleware END');

        return $response;
    }

    // ----------------------------------------------------------------
    // Enkripsi field di request
    // ----------------------------------------------------------------
    protected function encryptField(Request $request, string $field): void
    {
        if (!$request->has($field)) {
            return;
        }

        $value = $request->input($field);

        // Skip null, empty, array
        if (empty($value) || is_array($value)) {
            return;
        }

        $encrypted = self::encryptValue((string) $value);
        $request->merge([$field => $encrypted]);

        Log::info("Sandidata: field {$field} dienkripsi");
    }


    public static function sealFile(string $filePath): array
    {
        self::init();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::$baseUrl . '/sealfile',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'file' => new \CURLFile(
                    $filePath,
                    mime_content_type($filePath),
                    basename($filePath)
                ),
            ],
            CURLOPT_SSLCERT        => self::$certFile,
            CURLOPT_SSLKEY         => self::$keyFile,
            CURLOPT_SSLKEYPASSWD   => self::$certPassword,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 60, // file lebih besar, timeout lebih lama
        ]);

        $t0       = microtime(true);
        $response = curl_exec($ch);
        $duration = round((microtime(true) - $t0) * 1000, 2);
        $error    = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);

        if ($error) {
            Log::error('SEAL sealFile error', [
                'file'     => basename($filePath),
                'error'    => $error,
                'duration' => $duration . 'ms',
            ]);
        } else {
            Log::debug('SEAL sealFile OK', [
                'file'     => basename($filePath),
                'duration' => $duration . 'ms',
            ]);
        }

        // Response adalah binary file .enc
        return [$response, $error];
    }


    public static function unsealFile(string $filePath): array
    {
        self::init();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::$baseUrl . '/unsealfile',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'file' => new \CURLFile(
                    $filePath,
                    'application/octet-stream',
                    basename($filePath)
                ),
            ],
            CURLOPT_SSLCERT        => self::$certFile,
            CURLOPT_SSLKEY         => self::$keyFile,
            CURLOPT_SSLKEYPASSWD   => self::$certPassword,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 60,
        ]);

        $t0       = microtime(true);
        $response = curl_exec($ch);
        $duration = round((microtime(true) - $t0) * 1000, 2);
        $error    = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);

        if ($error) {
            Log::error('SEAL unsealFile error', [
                'file'     => basename($filePath),
                'error'    => $error,
                'duration' => $duration . 'ms',
            ]);
        } else {
            Log::debug('SEAL unsealFile OK', [
                'file'     => basename($filePath),
                'duration' => $duration . 'ms',
            ]);
        }

        // Response adalah binary file asli (PDF)
        return [$response, $error];
    }
}
