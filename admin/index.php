<?php
include '../../../include/cp_header.php';
xoops_cp_header();

$moduleDirName = basename(dirname(__DIR__));
$moduleHandler = xoops_getHandler('module');
$module = $moduleHandler->getByDirname($moduleDirName);
$configHandler = xoops_getHandler('config');
$moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));

$watermarkEnabled = $moduleConfig['enable_watermark'] ?? 1;
$watermarkText = $moduleConfig['watermark_text'] ?? 'memegen';

// Tilbakemeldingstype
$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $mid = $module->getVar('mid');

    $changedStatus = false;
    $changedText = false;

    if (isset($_POST['enable_watermark'])) {
        $newEnabled = (int)$_POST['enable_watermark'];
        if ($newEnabled !== (int)$watermarkEnabled) {
            $db->queryF("UPDATE " . $db->prefix('config') . "
                         SET conf_value = " . $db->quoteString($newEnabled) . "
                         WHERE conf_name = 'enable_watermark' AND conf_modid = " . $mid);
            $watermarkEnabled = $newEnabled;
            $changedStatus = true;
        }
    }

    if (isset($_POST['watermark_text'])) {
        $newText = trim($_POST['watermark_text']);
        if ($newText !== $watermarkText) {
            $db->queryF("UPDATE " . $db->prefix('config') . "
                         SET conf_value = " . $db->quoteString($newText) . "
                         WHERE conf_name = 'watermark_text' AND conf_modid = " . $mid);
            $watermarkText = $newText;
            $changedText = true;
        }
    }

    // Kombiner tilbakemeldingstype
    if ($changedStatus && $changedText) {
        $feedback = 'both';
    } elseif ($changedStatus) {
        $feedback = $watermarkEnabled ? 'enabled' : 'disabled';
    } elseif ($changedText) {
        $feedback = 'text_updated';
    }
}

echo '<h2>MemeGen Watermark Settings</h2>';

echo '<form method="post" style="margin-bottom:20px;">';
echo '<fieldset style="padding:10px; border:1px solid #ccc; position:relative;">';
echo '<legend><strong>Watermark Settings</strong></legend>';

// VISUELL TILBAKEMELDING
if ($feedback === 'enabled') {
    echo '<div style="margin-bottom:12px; background:#dff0d8; color:#3c763d; border:1px solid #c5e2c8; border-radius:4px; padding:10px;">✅ Watermark is now enabled.</div>';
} elseif ($feedback === 'disabled') {
    echo '<div style="margin-bottom:12px; background:#f2dede; color:#a94442; border:1px solid #ebcccc; border-radius:4px; padding:10px;">❌ Watermark is now disabled.</div>';
} elseif ($feedback === 'text_updated') {
    echo '<div style="margin-bottom:12px; background:#d9edf7; color:#31708f; border:1px solid #bce8f1; border-radius:4px; padding:10px;">✏️ Watermark text updated.</div>';
} elseif ($feedback === 'both') {
    echo '<div style="margin-bottom:12px; background:#eaf5ea; color:#2c662d; border:1px solid #bce8bc; border-radius:4px; padding:10px;">✅ Watermark status and text updated.</div>';
}

echo '<label for="enable_watermark">Enable Watermark:</label> ';
echo '<select name="enable_watermark">';
echo '<option value="1"' . ($watermarkEnabled ? ' selected' : '') . '>Yes</option>';
echo '<option value="0"' . (!$watermarkEnabled ? ' selected' : '') . '>No</option>';
echo '</select><br><br>';

echo '<label for="watermark_text">Watermark Text:</label> ';
echo '<input type="text" name="watermark_text" value="' . htmlspecialchars($watermarkText, ENT_QUOTES) . '" size="40" />';
echo '<br><br><input type="submit" value="Save Watermark Settings" class="formButton" />';
echo '</fieldset>';
echo '<small style="color:gray;">Watermark settings can also be changed under Preferences.</small>';

echo '</form>';

xoops_cp_footer();
