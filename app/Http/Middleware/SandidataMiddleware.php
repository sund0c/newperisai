<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SandidataMiddleware
{
    private static $certFile;
    private static $keyFile;
    private static $certPassword;
    private static $baseUrl;

    /**
     * Inisialisasi konfigurasi SEAL
     */
    private static function init()
    {
        // Kalau sudah diinisialisasi, skip
        if (!empty(self::$baseUrl)) {
            return;
        }

        self::$baseUrl       = rtrim(config('seal.base_url'));
        self::$certFile      = rtrim(config('seal.cert_file'));
        self::$keyFile       = rtrim(config('seal.key_file'));
        self::$certPassword  = rtrim(config('seal.cert_password'));

        Log::info('Sandidata init debug', [
            'baseUrl' => self::$baseUrl,
            'baseUrl_length' => strlen(self::$baseUrl),
            'last_char_hex' => dechex(ord(substr(self::$baseUrl, -1))),
        ]);

        // Validasi
        if (empty(self::$baseUrl)) {
            throw new \Exception('SEAL_BASE_URL belum di-set di .env');
        }

        if (empty(self::$certFile) || !file_exists(self::$certFile)) {
            throw new \Exception('SEAL_CERT_CRT tidak ditemukan: ' . self::$certFile);
        }

        if (empty(self::$keyFile) || !file_exists(self::$keyFile)) {
            throw new \Exception('SEAL_CERT_KEY tidak ditemukan: ' . self::$keyFile);
        }
    }

    /**
     * Send request ke API SEAL BSSN
     */
    private static function sendRequest($url, $payload)
    {
        self::init();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ],
            CURLOPT_SSLCERT => self::$certFile,
            CURLOPT_SSLKEY => self::$keyFile,
            CURLOPT_SSLKEYPASSWD => self::$certPassword,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $error = curl_errno($ch) ? curl_error($ch) : null;

        if ($error) {
            Log::error('SEAL cURL Error: ' . $error);
        }

        curl_close($ch);

        return [$response, $error];
    }

    /**
     * ENKRIPSI - Plaintext → Ciphertext
     */
    public static function seal($plaintext)
    {
        self::init();

        $payload = json_encode([
            'Plaintext' => [['text' => $plaintext]]
        ]);

        return self::sendRequest(self::$baseUrl . '/seal', $payload);
    }

    /**
     * DEKRIPSI - Ciphertext → Plaintext
     */
    public static function unseal($ciphertext)
    {
        self::init();
        $payload = json_encode([
            'Ciphertext' => [['text' => $ciphertext]]
        ]);

        return self::sendRequest(self::$baseUrl . '/unseal', $payload);
    }

    /**
     * Handle middleware
     */
    public function handle(Request $request, Closure $next, string ...$fields)
    {
        Log::info('SandidataMiddleware START', [
            'url' => $request->url(),
            'fields' => $fields,
            'original_input' => $request->all()
        ]);

        // ENKRIPSI
        foreach ($fields as $field) {
            $this->encryptField($request, $field);
        }

        Log::info('SandidataMiddleware AFTER ENCRYPT', [
            'modified_input' => $request->all()
        ]);

        $response = $next($request);

        // DEKRIPSI untuk view
        if ($response instanceof \Illuminate\View\View) {
            $data = $response->getData();
            foreach ($fields as $field) {
                $data = $this->decryptFieldInData($data, $field);
            }
            foreach ($data as $key => $value) {
                $response->with($key, $value);
            }
        }

        Log::info('SandidataMiddleware END');

        return $response;
    }

    protected function encryptField(Request $request, string $field): void
    {
        if (!$request->has($field)) {
            Log::warning("Sandidata: Field {$field} tidak ada di request");
            return;
        }

        $value = $request->input($field);

        Log::info("Sandidata: Processing field {$field}", [
            'value' => $value,
            'is_encrypted' => is_string($value) && str_starts_with($value, 's0:')
        ]);

        if (empty($value)) {
            Log::info("Sandidata: Field {$field} kosong, skip");
            return;
        }

        // Skip jika sudah terenkripsi
        if (is_string($value) && str_starts_with($value, 's0:')) {
            Log::info("Sandidata: Field {$field} sudah terenkripsi, skip");
            return;
        }

        try {
            [$response, $error] = self::seal($value);

            if ($error || !$response) {
                Log::error("Sandidata: Gagal enkripsi field {$field}", ['error' => $error]);
                return;
            }

            $json = json_decode($response, true);
            $encrypted = $json['Ciphertext'][0]['text'] ?? null;

            if ($encrypted) {
                // PENTING: Merge ke request
                $request->merge([$field => $encrypted]);

                Log::info("Sandidata: Field {$field} BERHASIL dienkripsi", [
                    'encrypted' => $encrypted
                ]);
            } else {
                Log::error("Sandidata: Response SEAL tidak valid untuk {$field}");
            }
        } catch (\Exception $e) {
            Log::error("Sandidata: Exception untuk {$field}", [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Decrypt field in data
     */
    protected function decryptFieldInData($data, string $field)
    {
        if (is_object($data)) {
            if (isset($data->$field) && is_string($data->$field) && str_starts_with($data->$field, 's0:')) {
                $data->$field = $this->decryptValue($data->$field);
                Log::info("Sandidata: Field {$field} didekripsi untuk view");
            }
            // Recursive untuk relations
            foreach (get_object_vars($data) as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $data->$key = $this->decryptFieldInData($value, $field);
                }
            }
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === $field && is_string($value) && str_starts_with($value, 's0:')) {
                    $data[$key] = $this->decryptValue($value);
                } elseif (is_array($value) || is_object($value)) {
                    $data[$key] = $this->decryptFieldInData($value, $field);
                }
            }
        }

        return $data;
    }

    /**
     * Decrypt single value
     */
    protected function decryptValue(string $value): string
    {
        try {
            [$response, $error] = self::unseal($value);

            if ($error || !$response) {
                Log::error("Sandidata: Gagal dekripsi - {$error}");
                return $value;
            }

            $json = json_decode($response, true);
            return $json['Plaintext'][0]['text'] ?? $value;
        } catch (\Exception $e) {
            Log::error("Sandidata Decryption Exception: " . $e->getMessage());
            return $value;
        }
    }
}
