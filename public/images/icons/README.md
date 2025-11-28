# Icon Placeholder

**Note:** This is a placeholder directory for PWA app icons.

## Required Icons

Create these PNG files in this directory:

- `icon-72x72.png` (72x72 pixels)
- `icon-96x96.png` (96x96 pixels)
- `icon-128x128.png` (128x128 pixels)
- `icon-144x144.png` (144x144 pixels)
- `icon-152x152.png` (152x152 pixels)
- `icon-192x192.png` (192x192 pixels) - **Required for Android**
- `icon-384x384.png` (384x384 pixels)
- `icon-512x512.png` (512x512 pixels) - **Required for Android**

## Quick Generation

### Option 1: Online Tool
Use [RealFaviconGenerator](https://realfavicongenerator.net/)
1. Upload your logo (square, 512x512 recommended)
2. Generate all sizes automatically
3. Download and extract to this folder

### Option 2: ImageMagick Command
```bash
# Install ImageMagick first
# Then convert your logo:
convert logo.png -resize 72x72 icon-72x72.png
convert logo.png -resize 96x96 icon-96x96.png
convert logo.png -resize 128x128 icon-128x128.png
convert logo.png -resize 144x144 icon-144x144.png
convert logo.png -resize 152x152 icon-152x152.png
convert logo.png -resize 192x192 icon-192x192.png
convert logo.png -resize 384x384 icon-384x384.png
convert logo.png -resize 512x512 icon-512x512.png
```

### Option 3: Figma/Photoshop
1. Create 512x512 canvas
2. Design your icon
3. Export in all sizes listed above

## Temporary Workaround

For now, the PWA will work without icons but won't look professional.
The manifest will show broken image links until these are added.

To test PWA without icons, you can temporarily:
1. Use Chrome DevTools → Application → Manifest
2. See the structure is correct
3. Service Worker still functions
4. Install prompt still works (just no icon)
