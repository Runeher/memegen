<link rel="stylesheet" type="text/css" href="<{$xoops_url}>/modules/memegen/style/style.css">

<style>
@font-face {
  font-family: 'Anton';
  src: url('<{$xoops_url}>/modules/memegen/fonts/Anton-Regular.ttf') format('truetype');
  font-weight: normal;
  font-style: normal;
}
canvas {
  font-family: 'Anton', Impact, sans-serif;
}
</style>


<div class="memegen-wrapper" style="display: flex; flex-wrap: wrap; gap: 20px; overflow: hidden;">
  <h3 style="text-align: center; width: 100%; margin-bottom: 2px;"><{_MD_MEMEGEN_HEADER1}></h3>

  <div style="flex: 1; min-width: 300px; position: relative;">
    <div id="previewContainer" style="display: block; position: relative;">
      <canvas id="previewCanvas" style="max-width: 100%; border-radius: 8px;"></canvas>
      <div id="previewPlaceholder" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-20deg); font-size: 2rem; color: rgba(0,0,0,0.1); pointer-events: none;"><{_MD_MEMEGEN_PREVIEW}></div>
    </div>
  </div>

  <div style="flex: 1; min-width: 300px;">
    <form method="post" enctype="multipart/form-data">
      <div class="input-group">
        
        <label class="formButton" style="display: inline-block; padding: 10px 20px; cursor: pointer;">
          <span id="uploadLabel"><{_MD_MEMEGEN_UPLOAD}></span>
          <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif" required style="display:none;">
        </label>
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

<script>
const fontName = 'Anton';
const watermarkText = '<{$watermark_text|escape:"javascript"}>';
const watermarkEnabled = '<{$watermark_enabled|default:"0"}>' === '1';

function getWrappedLines(text, maxWidth, fontSize) {
  const words = text.split(' ');
  const lines = [];
  let line = '';
  ctx.font = `${fontSize}px "${fontName}"`;
  for (let word of words) {
    let testLine = line + word + ' ';
    if (ctx.measureText(testLine).width > maxWidth) {
      lines.push(line.trim());
      line = word + ' ';
    } else {
      line = testLine;
    }
  }
  lines.push(line.trim());
  return lines;
}

function drawTextWithStroke(text, fontSize, color, align = 'top') {
  ctx.font = `${fontSize}px "${fontName}"`;
  ctx.fillStyle = color;
  ctx.strokeStyle = 'black';
  ctx.lineWidth = fontSize * 0.08;
  ctx.textAlign = 'center';

  const lines = getWrappedLines(text, canvas.width * 0.9, fontSize);
  let y;

  if (align === 'top') {
    y = fontSize * 0.55;
  } else {
    y = canvas.height - fontSize * lines.length - fontSize * 0.2;
  }

  ctx.textBaseline = 'top';
  for (let i = 0; i < lines.length; i++) {
    const lineY = y + i * fontSize * 1.2;
    ctx.strokeText(lines[i], canvas.width / 2, lineY);
    ctx.fillText(lines[i], canvas.width / 2, lineY);
  }
}

function drawWatermark() {
  if (!watermarkEnabled || !watermarkText) return;

  const fontSizeWM = 16; //  watermark font size
  ctx.save();

  ctx.translate(canvas.width - 19, canvas.height - 12);
  ctx.rotate(-Math.PI / 2);

  ctx.font = `${fontSizeWM}px "${fontName}"`;
  ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
  ctx.strokeStyle = 'rgba(0, 0, 0, 0.25)';
  ctx.lineWidth = fontSizeWM * 0.04;

  ctx.textAlign = 'left';
  ctx.textBaseline = 'top';

  ctx.strokeText(watermarkText, 0, 0);
  ctx.fillText(watermarkText, 0, 0);

  ctx.restore();
}

function hslToHex(h, s, l) {
  l /= 100;
  const a = s * Math.min(l, 1 - l) / 100;
  const f = n => {
    const k = (n + h / 30) % 12;
    const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
    return Math.round(255 * color).toString(16).padStart(2, '0');
  };
  return `#${f(0)}${f(8)}${f(4)}`;
}

window.addEventListener("DOMContentLoaded", function () {
  const imageInput = document.getElementById('image');
  const canvas = document.getElementById('previewCanvas');
  const ctx = canvas.getContext('2d');
  const previewContainer = document.getElementById('previewContainer');
  const previewPlaceholder = document.getElementById('previewPlaceholder');

  window.ctx = ctx;
  window.canvas = canvas;

  function updatePreview() {
    if (!canvas.imageRef) return;
    const fontSizeInput = parseInt(document.getElementById('fontSize').value);
    const fontSize = Math.round(canvas.width * (fontSizeInput / 800));
    const color = document.getElementById('fontColor').value;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(canvas.imageRef, 0, 0, canvas.width, canvas.height);

    drawTextWithStroke(document.getElementById('topText').value, fontSize, color, 'top');
    drawTextWithStroke(document.getElementById('bottomText').value, fontSize, color, 'bottom');
  }

  function saveWithWatermark() {
    const fontSizeInput = parseInt(document.getElementById('fontSize').value);
    const fontSize = Math.round(canvas.width * (fontSizeInput / 800));

    drawWatermark(); // uses fixed 16px size

    const dataUrl = canvas.toDataURL('image/jpeg', 0.8); // compress to JPEG
    const link = document.createElement('a');
    link.download = 'meme_' + Date.now() + '.jpg';
    link.href = dataUrl;
    link.click();

    updatePreview(); // refresh without watermark
  }

  ['topText', 'bottomText', 'fontSize', 'fontColor'].forEach(id => {
    document.getElementById(id).addEventListener('input', updatePreview);
  });

  imageInput.addEventListener('change', function (e) {
    const label = document.getElementById('uploadLabel');
    if (label) label.textContent = '<{_MD_MEMEGEN_ANOTHER_IMAGE}>';

    const reader = new FileReader();
    reader.onload = function (event) {
      const img = new Image();
      img.onload = function () {
        const scaleFactor = img.width > 800 ? 800 / img.width : 1;
        const scaledWidth = img.width * scaleFactor;
        const scaledHeight = img.height * scaleFactor;

        canvas.width = scaledWidth;
        canvas.height = scaledHeight;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        canvas.imageRef = img;
        canvas.imageScale = scaleFactor;

        updatePreview();
        previewContainer.style.display = 'block';
        if (previewPlaceholder) previewPlaceholder.style.display = 'none';
      };
      img.src = event.target.result;
    };
    reader.readAsDataURL(e.target.files[0]);
  });

  document.getElementById('saveMeme').addEventListener('click', function () {
    saveWithWatermark();
  });

  document.getElementById('fontColor').addEventListener('input', function () {
    document.getElementById('colorValue').textContent = this.value;
    updatePreview();
  });

  document.getElementById('hueSlider').addEventListener('input', function () {
    const hex = hslToHex(this.value, 100, 50);
    document.getElementById('fontColor').value = hex;
    document.getElementById('colorValue').textContent = hex;
    updatePreview();
  });
});
</script>
