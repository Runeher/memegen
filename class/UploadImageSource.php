<?php

namespace XoopsModules\Memegen;

// (for future use)
class UploadImageSource extends ImageSource
{
    private $file;

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    public function validate(): bool
    {
        if (!is_uploaded_file($this->file['tmp_name'])) {
            throw new \RuntimeException(_MD_MEMEGEN_UPLOAD_FAILED);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($this->file['tmp_name']);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            throw new \RuntimeException(_MD_MEMEGEN_INVALID_IMAGE_TYPE);
        }

        return true;
    }

    public function getImageData(): array
    {
        $this->validate();
        return [
            'data' => base64_encode(file_get_contents($this->file['tmp_name'])),
            'type' => (new \finfo(FILEINFO_MIME_TYPE))->file($this->file['tmp_name'])
        ];
    }

    public function cleanup(): void
    {
        // Nothing needed for uploads
    }
}
