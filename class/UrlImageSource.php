<?php

namespace XoopsModules\Memegen;

// (for future use)
class UrlImageSource extends ImageSource
{
    private $url;
    private $tempFile;
    private $allowedHosts = [];
    private $maxFileSize = 10485760; // 10MB

    public function __construct(string $url, array $config = [])
    {
        $this->url = $url;
        $this->allowedHosts = $config['allowed_hosts'] ?? [];
        $this->maxFileSize = $config['max_file_size'] ?? $this->maxFileSize;
    }

    public function validate(): bool
    {
        // URL format validation
        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(_MD_MEMEGEN_INVALID_URL);
        }

        // Protocol check (prevent file:// or other protocols)
        $parts = parse_url($this->url);
        if (!in_array($parts['scheme'], ['http', 'https'])) {
            throw new \InvalidArgumentException(_MD_MEMEGEN_HTTPS_ONLY);
        }

        // Host whitelist check if configured
        if (!empty($this->allowedHosts) && !in_array($parts['host'], $this->allowedHosts)) {
            throw new \InvalidArgumentException(_MD_MEMEGEN_HOST_NOT_ALLOWED);
        }

        return true;
    }

    public function getImageData(): array
    {
        $this->validate();

        // Use cURL for better control and security
        $ch = curl_init($this->url);
        $this->tempFile = tempnam(XOOPS_UPLOAD_PATH . '/memegen_tmp', 'url_');
        $fp = fopen($this->tempFile, 'wb');

        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'XOOPS Meme Generator',
            CURLOPT_FAILONERROR => true,
            // SSRF protection
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);

        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $downloadSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

        curl_close($ch);
        fclose($fp);

        if (!$success || $httpCode !== 200) {
            $this->cleanup();
            throw new \RuntimeException(_MD_MEMEGEN_DOWNLOAD_FAILED);
        }

        // Size check
        if ($downloadSize > $this->maxFileSize) {
            $this->cleanup();
            throw new \RuntimeException(_MD_MEMEGEN_FILE_TOO_LARGE);
        }

        // Verify it's actually an image (double-check)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $actualType = $finfo->file($this->tempFile);

        if (!in_array($actualType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $this->cleanup();
            throw new \RuntimeException(_MD_MEMEGEN_INVALID_IMAGE_TYPE);
        }

        $imageData = file_get_contents($this->tempFile);
        return [
            'data' => base64_encode($imageData),
            'type' => $actualType
        ];
    }

    public function cleanup(): void
    {
        if ($this->tempFile && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
}
