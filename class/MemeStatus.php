<?php

namespace XoopsModules\Memegen;

// Add status field to memes table (for future use)
class MemeStatus
{
    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    public static function getStatusText($status)
    {
        $statuses = [
            self::PENDING => _MD_MEMEGEN_STATUS_PENDING,
            self::APPROVED => _MD_MEMEGEN_STATUS_APPROVED,
            self::REJECTED => _MD_MEMEGEN_STATUS_REJECTED
        ];
        return $statuses[$status] ?? _MD_MEMEGEN_STATUS_UNKNOWN;
    }
}
