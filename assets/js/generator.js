/**
 * XOOPS Meme Generator
 * @namespace MemeGenerator
 */
const MemeGenerator = (function() {
    'use strict';

    // Private variables
    let config = {
        fontName: 'Anton',
        watermarkText: '',
        watermarkEnabled: false,
        moduleUrl: '',
        uploadLabel: '',
        anotherImageLabel: '',
        lang: {}
    };

    let canvas, ctx, previewContainer, previewPlaceholder;

    // Private methods
    function getWrappedLines(text, maxWidth, fontSize) {
        const words = text.split(' ');
        const lines = [];
        let line = '';

        ctx.font = `${fontSize}px "${config.fontName}"`;

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
        ctx.font = `${fontSize}px "${config.fontName}"`;
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
        if (!config.watermarkEnabled || !config.watermarkText) return;

        const fontSizeWM = 16;
        ctx.save();

        ctx.translate(canvas.width - 19, canvas.height - 12);
        ctx.rotate(-Math.PI / 2);

        ctx.font = `${fontSizeWM}px "${config.fontName}"`;
        ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
        ctx.strokeStyle = 'rgba(0, 0, 0, 0.25)';
        ctx.lineWidth = fontSizeWM * 0.04;

        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';

        ctx.strokeText(config.watermarkText, 0, 0);
        ctx.fillText(config.watermarkText, 0, 0);

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

        drawWatermark();

        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        const link = document.createElement('a');
        link.download = 'meme_' + Date.now() + '.jpg';
        link.href = dataUrl;
        link.click();

        updatePreview(); // refresh without watermark
    }

    function loadCanvasImage(src) {
        const img = new Image();
        img.onload = function() {
            const scaleFactor = img.width > 800 ? 800 / img.width : 1;
            canvas.width = img.width * scaleFactor;
            canvas.height = img.height * scaleFactor;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            canvas.imageRef = img;
            updatePreview();

            previewContainer.style.display = 'block';
            if (previewPlaceholder) {
                previewPlaceholder.style.display = 'none';
            }
        };
        img.src = src;
    }

    async function generateFromUrl() {
        const url = document.getElementById('imageUrl').value.trim();
        const generateBtn = document.getElementById('generateFromUrl');

        if (!url) {
            alert(config.lang.enterUrl || 'Please enter a valid URL.');
            return;
        }

        // Basic client-side validation
        try {
            new URL(url);
        } catch (e) {
            alert(config.lang.invalidUrl || 'Invalid URL format.');
            return;
        }

        // Show loading state
        generateBtn.disabled = true;
        generateBtn.textContent = config.lang.loading || 'Loading...';

        try {
            const formData = new FormData();
            formData.append('imageUrl', url);

            // Add CSRF token if available
            const tokenField = document.querySelector('input[name="XOOPS_TOKEN"]');
            if (tokenField) {
                formData.append('XOOPS_TOKEN', tokenField.value);
            }

            const response = await fetch(config.moduleUrl + '/generate_meme.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error || config.lang.unknownError || 'Unknown error');
            }

            loadCanvasImage(result.imageData);
            document.getElementById('imageUrl').value = '';

        } catch (error) {
            alert(error.message);
        } finally {
            generateBtn.disabled = false;
            generateBtn.textContent = config.lang.generateUrl || 'Generate from URL';
        }
    }

    // Public API
    return {
        /**
         * Initialize the meme generator
         * @param {Object} options Configuration options
         */
        init: function(options) {
            // Merge config
            config = Object.assign(config, options);

            // Get DOM elements
            canvas = document.getElementById('previewCanvas');
            ctx = canvas.getContext('2d');
            previewContainer = document.getElementById('previewContainer');
            previewPlaceholder = document.getElementById('previewPlaceholder');

            // Store globally for text drawing functions
            window.ctx = ctx;
            window.canvas = canvas;

            // Set up event listeners
            this.setupEventListeners();
        },

        setupEventListeners: function() {
            // Text input listeners
            ['topText', 'bottomText', 'fontSize', 'fontColor'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', updatePreview);
                }
            });

            // Image upload listener
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const label = document.getElementById('uploadLabel');
                    if (label) {
                        label.textContent = config.anotherImageLabel;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        loadCanvasImage(event.target.result);
                    };
                    reader.readAsDataURL(e.target.files[0]);
                });
            }

            // Save button
            const saveBtn = document.getElementById('saveMeme');
            if (saveBtn) {
                saveBtn.addEventListener('click', saveWithWatermark);
            }

            // Color picker
            const fontColorInput = document.getElementById('fontColor');
            if (fontColorInput) {
                fontColorInput.addEventListener('input', function() {
                    const colorValue = document.getElementById('colorValue');
                    if (colorValue) {
                        colorValue.textContent = this.value;
                    }
                    updatePreview();
                });
            }

            // Hue slider
            const hueSlider = document.getElementById('hueSlider');
            if (hueSlider) {
                hueSlider.addEventListener('input', function() {
                    const hex = hslToHex(this.value, 100, 50);
                    document.getElementById('fontColor').value = hex;
                    const colorValue = document.getElementById('colorValue');
                    if (colorValue) {
                        colorValue.textContent = hex;
                    }
                    updatePreview();
                });
            }

            // URL generation
            const generateUrlBtn = document.getElementById('generateFromUrl');
            if (generateUrlBtn) {
                generateUrlBtn.addEventListener('click', generateFromUrl);
            }
        },

        // Expose some methods for external use if needed
        updatePreview: updatePreview,
        loadImage: loadCanvasImage
    };
})();

// Auto-initialize if DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (window.memeGeneratorConfig) {
            MemeGenerator.init(window.memeGeneratorConfig);
        }
    });
} else {
    if (window.memeGeneratorConfig) {
        MemeGenerator.init(window.memeGeneratorConfig);
    }
}
