<?php
include '../../mainfile.php';

$GLOBALS['xoopsOption']['template_main'] = 'memegen_index.tpl';
include XOOPS_ROOT_PATH . '/header.php';

global $xoopsModuleConfig, $xoopsModule;

// Hent innstillinger for vannmerke
$watermarkText = '';
$watermarkEnabled = 0;

if (isset($xoopsModule) && $xoopsModule->getVar('dirname') === 'memegen') {
    $watermarkText = $xoopsModuleConfig['watermark_text'] ?? '';
    $watermarkEnabled = $xoopsModuleConfig['enable_watermark'] ?? 0;
}

// Send til template med riktige navn
$xoopsTpl->assign('watermark_text', $watermarkText);
$xoopsTpl->assign('watermark_enabled', $watermarkEnabled); // 🔥 MÅ hete dette

include XOOPS_ROOT_PATH . '/footer.php';
?>



