# Logo & Branding Implementation

## Overview
Implemented a complete logo and branding system for BarangayConnect, allowing administrators to upload custom barangay logos while providing fallback icons and default branding throughout the application.

## Features Implemented

### 1. **Sidebar Logo Display** (Primary Branding)
- **Location**: `resources/views/layouts/partials/sidebar.blade.php`
- **Functionality**:
  - Displays uploaded logo if available (12x12 pixels with rounded corners)
  - Falls back to building icon if no logo is uploaded
  - Shows barangay name alongside logo
  - Responsive on all screen sizes
  - Works on both desktop and mobile

### 2. **Top Navigation Bar Logo** (Mobile Branding)
- **Location**: `resources/views/layouts/partials/topbar.blade.php`
- **Functionality**:
  - Shows logo in header on mobile devices (only on screens < 1024px)
  - Auto-hidden on desktop to maintain clean layout
  - Appears next to hamburger menu on mobile
  - Provides consistent branding on smaller screens

### 3. **Browser Tab Favicon**
- **Files Created**:
  - `public/favicon.svg` - SVG favicon (default)
  - Dynamic logo from database when uploaded
- **Locations Updated**:
  - `resources/views/layouts/app.blade.php`
  - `resources/views/layouts/guest.blade.php`
- **Functionality**:
  - Uses uploaded logo if available
  - Falls back to SVG favicon if no logo
  - Displays in browser tab for all users

### 4. **Admin Settings - Logo Upload**
- **Location**: `resources/views/admin/settings/index.blade.php`
- **Features**:
  - Current logo preview section (if logo exists)
  - Drag-and-drop file upload area
  - Visual feedback on hover
  - File size limit: 2MB
  - Supported formats: PNG, SVG, WebP, JPG
  - Upload timestamp information
  - Dynamic button text ("Upload Logo" or "Update Logo")

### 5. **Sample Logo Files**
- **Files Created**:
  - `public/images/barangay-logo.svg` - Default barangay logo
  - `public/favicon.svg` - Browser tab favicon

## Database Integration

### System Settings
The logo is stored in the `SystemSetting` table with key: `logo_path`

**Storage Path**: `public/storage/settings/{logo-file}`

**Example**:
```php
$logoPath = \App\Models\SystemSetting::get('logo_path');
// Result: 'settings/barangay-logo-12345.svg'
```

## Usage Instructions

### For Administrators

1. **Upload a Logo**:
   - Navigate to Admin > Settings > Platform Branding
   - Click the upload area to select a file or drag and drop
   - Choose PNG, SVG, or WebP format (PNG recommended for icons)
   - Click "Upload Logo" or "Update Logo"
   - The logo will appear throughout the system

2. **Supported Image Formats**:
   - **PNG**: Best for transparent logos
   - **SVG**: Scalable vector format (recommended)
   - **WebP**: Modern compressed format
   - **JPG**: Basic support (no transparency)

3. **Recommended Dimensions**:
   - Square format (e.g., 200x200px, 512x512px)
   - Transparent background for PNG/SVG
   - Minimum 100x100px for clarity

### For All Users

- **Sidebar**: See logo every time on desktop (always visible)
- **Mobile**: Logo appears in top header bar
- **Browser Tab**: Logo shows as favicon
- **Dashboard**: Consistent branding across all pages

## Technical Details

### CSS Classes & Tailwind
- `.rounded-xl` - Rounded corners for logo display
- `.object-cover` - Maintains aspect ratio
- `.shadow-lg` - Shadow effect for depth
- Responsive breakpoints (lg:hidden for mobile-only elements)

### Laravel Integration
- **Facade**: `Storage::disk('public')` for logo file access
- **Model**: `SystemSetting` for storing logo path
- **Validation**: Image type validation in `SettingsController`
- **Audit**: Logo changes logged in audit trail

### File Storage
- **Location**: `storage/app/public/settings/`
- **Symlink Required**: `php artisan storage:link` (usually already set up)
- **Access**: Via `asset('storage/...')` in Blade templates

## Code Examples

### Display Logo with Fallback
```blade
@php
    $logoPath = \App\Models\SystemSetting::get('logo_path');
@endphp

@if($logoPath && Storage::disk('public')->exists($logoPath))
    <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-12 w-12 rounded-xl">
@else
    <div class="h-12 w-12 bg-indigo-600 rounded-xl flex items-center justify-center">
        <i data-lucide="building-2" class="h-6 w-6"></i>
    </div>
@endif
```

### Update Logo in Admin
```php
// In SettingsController
public function updateLogo(Request $request)
{
    $path = $request->file('logo')->store('settings', 'public');
    SystemSetting::set('logo_path', $path);
    return back()->with('success', 'Logo updated.');
}
```

## Locations Where Logo Appears

✅ **Sidebar** (All authenticated users)
- Top-left corner
- Always visible on desktop
- Height: 48px (12 units)
- Responsive layout

✅ **Top Navigation Bar** (Mobile)
- Next to hamburger menu
- Height: 32px (8 units)
- Visible only on mobile (<1024px)

✅ **Browser Tab** (All users)
- Favicon display
- 32x32 SVG
- Falls back to default building icon

✅ **Admin Settings** (Administrators)
- Current logo preview
- Upload interface
- Size: 128px height

## Responsive Behavior

### Desktop (≥1024px)
- Sidebar logo always visible and full size
- Mobile logo in topbar hidden
- Favicon displayed in browser tab

### Tablet (768px - 1024px)
- Sidebar visible but logo smaller
- Mobile logo in topbar visible
- Favicon displayed

### Mobile (<768px)
- Sidebar toggled (hidden by default)
- Logo in topbar visible when menu is open
- Favicon displayed in browser tab

## Best Practices

1. **Logo Design**:
   - Use square aspect ratio (1:1)
   - Transparent background for versatility
   - Keep text minimal (abbreviation works well)
   - Test on dark background (app uses dark theme)

2. **File Size**:
   - Keep under 100KB for fast loading
   - Compress images before upload
   - Use SVG for scalable logos

3. **Naming**:
   - Use descriptive names
   - Include file type extension
   - Example: `barangay-sanjose-logo.png`

4. **Updates**:
   - Upload new logo to replace old one
   - Old logos are automatically replaced
   - Check preview before finalizing

## Troubleshooting

### Logo Not Showing
1. Verify storage symlink: `php artisan storage:link`
2. Check file permissions: `chmod -R 755 storage/app/public`
3. Clear Laravel cache: `php artisan cache:clear`
4. Verify file exists in `storage/app/public/settings/`

### Upload Fails
1. Check file size (max 2MB)
2. Verify image format is supported
3. Check disk space available
4. Verify write permissions on storage folder

### Favicon Not Updating
1. Clear browser cache (Ctrl+Shift+Del)
2. Hard refresh page (Ctrl+F5)
3. Check HTML for favicon link
4. Try different browser

## Future Enhancements

- [ ] Multiple logo variants (dark/light mode)
- [ ] Logo cropping tool in admin panel
- [ ] Logo preview in real-time before upload
- [ ] Custom favicon color picker
- [ ] Logo usage guidelines display
- [ ] Automatic logo format conversion

## File Structure
```
public/
├── favicon.svg                 # Default favicon
└── images/
    └── barangay-logo.svg       # Default barangay logo

resources/views/
├── layouts/
│   ├── app.blade.php           # Main app layout (favicon)
│   ├── guest.blade.php         # Guest layout (favicon)
│   └── partials/
│       ├── sidebar.blade.php   # Sidebar logo
│       └── topbar.blade.php    # Mobile logo in header
└── admin/settings/
    └── index.blade.php         # Logo upload form

storage/app/public/
└── settings/                   # Uploaded logo storage
    └── barangay-logo-{hash}.svg
```

## Security & Audit

- **Image Validation**: Only image files accepted
- **File Size Limit**: 2MB maximum
- **Audit Logging**: Logo changes logged in audit trail
- **Access Control**: Only admins can upload logos
- **Storage**: Files stored outside web root with symlink

## No Breaking Changes

✅ Existing system fully compatible
✅ Default icons still work without uploaded logo
✅ No changes to user roles or permissions
✅ No changes to existing functionality
✅ Pure UI/UX enhancement
