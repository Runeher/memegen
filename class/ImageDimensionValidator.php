<?php

namespace XoopsModules\Memegen;

// (for future use)
class ImageDimensionValidator
{
    private $maxWidth = 4096;
    private $maxHeight = 4096;
    private $maxPixels = 8388608; // 8MP

    public function validate($imagePath)
    {
        $info = getimagesize($imagePath);

        if ($info[0] > $this->maxWidth || $info[1] > $this->maxHeight) {
            throw new \RuntimeException(_MD_MEMEGEN_DIMENSIONS_TOO_LARGE);
        }

        if ($info[0] * $info[1] > $this->maxPixels) {
            throw new \RuntimeException(_MD_MEMEGEN_RESOLUTION_TOO_HIGH);
        }

        return true;
    }
}
