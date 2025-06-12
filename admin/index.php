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

echo '<h2>' . _MI_MEMEGEN_MENU_HOME . '</h2>';

echo '<form method="post" style="margin-bottom:20px;">';
echo '<fieldset style="padding:10px; border:1px solid #ccc; position:relative;">';
echo '<legend><strong>' . _MI_MEMEGEN_MENU_SETTINGS . '</strong></legend>';

// VISUELL TILBAKEMELDING
if ($feedback === 'enabled') {
    echo '<div style="margin-bottom:12px; background:#dff0d8; color:#3c763d; border:1px solid #c5e2c8; border-radius:4px; padding:10px;">✅ ' . _MI_MEMEGEN_MENU_ALERT1 . '</div>';
} elseif ($feedback === 'disabled') {
    echo '<div style="margin-bottom:12px; background:#f2dede; color:#a94442; border:1px solid #ebcccc; border-radius:4px; padding:10px;">❌ ' . _MI_MEMEGEN_MENU_ALERT2 . '</div>';
} elseif ($feedback === 'text_updated') {
    echo '<div style="margin-bottom:12px; background:#d9edf7; color:#31708f; border:1px solid #bce8f1; border-radius:4px; padding:10px;">✏️ ' . _MI_MEMEGEN_MENU_ALERT3 . '</div>';
} elseif ($feedback === 'both') {
    echo '<div style="margin-bottom:12px; background:#eaf5ea; color:#2c662d; border:1px solid #bce8bc; border-radius:4px; padding:10px;">✅ ' . _MI_MEMEGEN_MENU_ALERT4 . '</div>';
}

echo '<label for="enable_watermark">' . _MI_MEMEGEN_ADMIN_ENABLE_WATERMARK . '</label> ';
echo '<select name="enable_watermark">';
echo '<option value="1"' . ($watermarkEnabled ? ' selected' : '') . '>' . _MI_MEMEGEN_ADMIN_WATERMARK_YES . '</option>';
echo '<option value="0"' . (!$watermarkEnabled ? ' selected' : '') . '>' . _MI_MEMEGEN_ADMIN_WATERMARK_NO . '</option>';
echo '</select><br><br>';

echo '<label for="watermark_text">' . _MI_MEMEGEN_MENU_TEXT . '</label> ';
echo '<input type="text" name="watermark_text" value="' . htmlspecialchars($watermarkText, ENT_QUOTES) . '" size="40" />';
echo '<br><br><input type="submit" value="' . _MI_MEMEGEN_ADMIN_WATERMARK_SAVE . '" class="formButton" />';
echo '</fieldset>';
echo '<small style="color:gray;">' . _MI_MEMEGEN_ADMIN_WATERMARK_INFO . '</small>';

echo '</form>';

xoops_cp_footer();
