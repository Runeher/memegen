<?php

namespace XoopsModules\Memegen;

// (for future use)
class MemeRepository
{
    public function saveMeme($imageData, $userId, $metadata = [])
    {
        // Save to XOOPS image manager
        $imageHandler = xoops_gethandler('image');
        $image = $imageHandler->create();

        $image->setVar('image_name', 'meme_' . time() . '.jpg');
        $image->setVar('image_nicename', $metadata['title'] ?? 'Meme');
        $image->setVar('image_mimetype', 'image/jpeg');
        $image->setVar('image_created', time());
        $image->setVar('image_display', 1);
        $image->setVar('image_weight', 0);
        $image->setVar('imgcat_id', $this->getMemeCategory());

        // Save physical file
        $filename = 'meme_' . md5(uniqid()) . '.jpg';
        $path = XOOPS_UPLOAD_PATH . '/images/' . $filename;
        file_put_contents($path, base64_decode($imageData));

        $image->setVar('image_name', $filename);

        return $imageHandler->insert($image);
    }
}

