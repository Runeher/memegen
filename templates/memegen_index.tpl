<link rel="stylesheet" type="text/css" href="<{$xoops_url}>/modules/memegen/assets/css/style.css">

<style>
    @font-face {
        font-family: 'Anton';
        src: url('<{$xoops_url}>/modules/memegen/assets/fonts/Anton-Regular.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
</style>

<div class="memegen-wrapper">
    <{* Your existing HTML structure *}>
    <h3><{$smarty.const._MD_MEMEGEN_HEADER1}></h3>
    <div style="flex: 1; min-width: 300px; position: relative;">
        <div id="previewContainer" style="display: block; position: relative;">
            <canvas id="previewCanvas" style="max-width: 100%; border-radius: 8px;"></canvas>
            <div id="previewPlaceholder" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-20deg); font-size: 2rem; color: rgba(0,0,0,0.1); pointer-events: none;"><{_MD_MEMEGEN_PREVIEW}></div>
        </div>
    </div>

    <div style="flex: 1; min-width: 300px;">
        <form method="post" enctype="multipart/form-data">
            <{securityToken}>
            <div class="input-group">

                <label class="formButton" style="display: inline-block; padding: 10px 20px; cursor: pointer;">
                    <span id="uploadLabel"><{_MD_MEMEGEN_UPLOAD}></span>
                    <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif" required style="display:none;">
                </label>
            </div>

            <div class="input-group">
                <label for="imageUrl"><{_MD_MEMEGEN_IMAGE_URL}></label>
                <input type="text" name="imageUrl" id="imageUrl" placeholder="https://example.com/image.jpg" maxlength="200"> <br>
                <button type="button" id="generateFromUrl" class="formButton"><{_MD_MEMEGEN_GENERATE_URL}></button>
            </div>



            <div class="input-group">
                <label for="topText"><{_MD_MEMEGEN_TOP_TEXT}></label>
                <input type="text" name="topText" id="topText" maxlength="200">
            </div>

            <div class="input-group">
                <label for="bottomText"><{_MD_MEMEGEN_BOTTOM_TEXT}></label>
                <input type="text" name="bottomText" id="bottomText" maxlength="200">
            </div>

            <div class="input-pair" style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
                <div class="input-group" style="display: flex; align-items: center; gap: 10px;">
                    <label for="fontSize"><{_MD_MEMEGEN_FONT_SIZE}></label>
                    <input type="range" name="fontSize" id="fontSize" value="32" min="10" max="100" step="1" oninput="document.getElementById('fontSizeValue').textContent = this.value">
                    <span id="fontSizeValue">32</span>
                </div>

                <div class="input-group" style="display: flex; align-items: center; gap: 10px; max-width: 100%;">
                    <label for="fontColor"><{_MD_MEMEGEN_FONT_COLOR}></label>
                    <input type="color" name="fontColor" id="fontColor" value="#ffffff" style="width: 36px; height: 32px;">
                    <input type="range" id="hueSlider" min="0" max="360" value="0" style="flex: 1; height: 8px; max-width: 160px; background: linear-gradient(to right, red, yellow, lime, cyan, blue, magenta, red); border: none;">
                    <span id="colorValue">#ffffff</span>
                </div>
            </div>

            <div class="input-group">
                <button type="button" id="saveMeme" class="formButton"><{_MD_MEMEGEN_DOWNLOAD}></button>
            </div>
            <{if $xoops_isadmin}>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="<{$xoops_url}>/modules/memegen/admin/index.php" style="font-size: 0.75em; color: #888;"><{_MD_MEMEGEN_ADMIN_SECTION}></a>
                </div>
            <{/if}>
        </form>
    </div>
</div>

<{* Pass configuration to JavaScript *}>
<script>
    window.memeGeneratorConfig = {
        fontName: 'Anton',
        watermarkText: '<{$watermark_text|escape:"javascript"}>',
        watermarkEnabled: <{$watermark_enabled|default:"0"}> === '1',
        moduleUrl: '<{$xoops_url}>/modules/memegen',
        uploadLabel: '<{$smarty.const._MD_MEMEGEN_UPLOAD|escape:"javascript"}>',
        anotherImageLabel: '<{$smarty.const._MD_MEMEGEN_ANOTHER_IMAGE|escape:"javascript"}>',
        lang: {
            enterUrl: '<{$smarty.const._MD_MEMEGEN_ENTER_URL|escape:"javascript"}>',
            invalidUrl: '<{$smarty.const._MD_MEMEGEN_INVALID_URL|escape:"javascript"}>',
            loading: '<{$smarty.const._MD_MEMEGEN_LOADING|escape:"javascript"}>',
            unknownError: '<{$smarty.const._MD_MEMEGEN_UNKNOWN_ERROR|escape:"javascript"}>',
            generateUrl: '<{$smarty.const._MD_MEMEGEN_GENERATE_URL|escape:"javascript"}>',
            preview: '<{$smarty.const._MD_MEMEGEN_PREVIEW|escape:"javascript"}>'
        }
    };
</script>

<{* Load the generator script *}>
<script src="<{$xoops_url}>/modules/memegen/assets/js/generator.js"></script>
