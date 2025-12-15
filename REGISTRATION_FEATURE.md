# Registration Feature Documentation

## Overview
The registration page at `https://kitorangpeduli.id/register` allows new respondents to create an account and participate in surveys for Papua development.

## Features Implemented

### 1. **Multi-Step Registration Form**
   - **Step 1: Data Pribadi (Personal Data)**
     - Full name (as per KTP)
     - NIK (16 digits)
     - KK Number (optional, 16 digits)
     - Place and date of birth
     - Gender (with visual selection)
     - Religion
     - Citizen type
     - Occupation
   
   - **Step 2: Kontak (Contact Information)**
     - Phone number (WhatsApp)
     - Email (optional)
   
   - **Step 3: Alamat (Address)**
     - Complete address
     - Cascading location selects:
       - Province
       - Regency/City
       - District
       - Village
   
   - **Step 4: Verifikasi (Verification)**
     - KTP photo upload with preview
     - Visual upload area with drag-and-drop support
     - Image preview before submission

### 2. **UI/UX Enhancements**

#### Progress Indicator
- Visual 4-step progress bar at the top
- Active step highlighted in yellow
- Completed steps shown in green
- Step labels for clarity

#### Form Validation
- **Client-side validation:**
  - Required field checking
  - NIK/KK format validation (16 digits, numeric only)
  - Phone number format (9-13 digits)
  - Email format validation
  - Date validation
  - File size validation (max 2MB for KTP)
  - File type validation (JPG, JPEG, PNG)

- **Server-side validation:**
  - Unique NIK check
  - Unique phone number check
  - Unique email check
  - Date must be before today
  - Valid religion options
  - Existing relationships (province, regency, etc.)

#### Interactive Elements
- **Gender Selection:** Visual radio buttons with icons
- **KTP Upload:** 
  - Click to upload
  - Image preview
  - Change photo option
  - Visual feedback
- **Location Cascade:** Automatic loading of dependent dropdowns
- **Loading States:** Submit button shows loading spinner

### 3. **Responsive Design**
- Mobile-first approach
- Grid layouts adapt to screen size
- Touch-friendly form controls
- Optimized for all device sizes

### 4. **Visual Design**
- Papua theme colors (Black, Yellow, Red)
- Smooth animations and transitions
- Clear visual hierarchy
- Consistent spacing and typography
- Icons for better visual communication

## Technical Implementation

### Routes
```php
// In routes/web.php
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
```

### Controller
**File:** `app/Http/Controllers/AuthController.php`

**Methods:**
- `showRegister()` - Display registration form with citizen types and provinces
- `register()` - Process registration, validate data, create respondent, upload KTP, send OTP

### Model
**File:** `app/Models/Respondent.php`

**Key Fields:**
- Personal: `nama_lengkap`, `nik`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`
- Contact: `phone`, `email`
- Location: `province_id`, `regency_id`, `district_id`, `village_id`, `alamat`
- Verification: `verification_status`, `phone_verified_at`
- Media: Uses Spatie Media Library for KTP photo

### View
**File:** `resources/views/auth/register.blade.php`

**JavaScript Features:**
- Multi-step navigation
- Form validation per step
- Cascading location dropdowns
- Image upload and preview
- Loading states
- Input formatting (numeric only for NIK, KK, phone)

### API Endpoints Used
```
GET /api/wilayah/regencies/{province_id}
GET /api/wilayah/districts/{regency_id}
GET /api/wilayah/villages/{district_id}
```

## User Flow

1. **Access Registration Page**
   - User navigates to `/register`
   - Sees Step 1 (Data Pribadi)

2. **Fill Personal Data**
   - Enter full name, NIK, birth info
   - Select gender, religion, citizen type
   - Click "Selanjutnya" to proceed

3. **Provide Contact Information**
   - Enter phone number (will be used for OTP)
   - Optionally enter email
   - Click "Selanjutnya"

4. **Enter Address Details**
   - Type complete address
   - Select location from cascading dropdowns
   - Click "Selanjutnya"

5. **Upload Verification Photo**
   - Click upload area or drag KTP photo
   - Preview image before submission
   - Read upload tips
   - Click "Daftar Sekarang"

6. **Server Processing**
   - Form data validated
   - Respondent record created
   - KTP photo stored via Spatie Media Library
   - OTP generated and cached
   - OTP sent via WhatsApp

7. **Redirect to OTP Verification**
   - User redirected to verify phone number
   - Success message displayed
   - Can now verify and login

## Validation Rules

### Required Fields
- nama_lengkap
- nik (16 digits, unique)
- tempat_lahir
- tanggal_lahir (must be before today)
- jenis_kelamin (L or P)
- agama (predefined options)
- citizen_type_id
- no_hp (9-13 digits, unique)
- alamat
- province_id, regency_id, district_id, village_id
- foto_ktp (image, max 2MB)

### Optional Fields
- no_kk (if provided, must be 16 digits)
- pekerjaan
- email (if provided, must be valid and unique)

## Error Handling

### Client-side
- Immediate feedback on invalid input
- Red border flash on validation failure
- Cannot proceed to next step without completing required fields

### Server-side
- Comprehensive error messages
- Field-specific error display
- Old input preserved on validation failure
- User-friendly error messages in Indonesian

## Security Features

1. **CSRF Protection:** All forms include CSRF token
2. **Unique Constraints:** Prevents duplicate NIK, phone, email
3. **File Validation:** 
   - Type checking (images only)
   - Size limiting (2MB max)
   - Stored securely via Spatie Media Library
4. **Data Sanitization:** Phone numbers cleaned (leading zeros removed)
5. **OTP Verification:** Phone ownership verified before account activation

## Database Schema Impact

### respondents table
Fields used:
- `nama_lengkap`
- `nik`
- `tempat_lahir`
- `tanggal_lahir`
- `jenis_kelamin`
- `agama`
- `citizen_type_id`
- `pekerjaan`
- `phone` (stores cleaned phone number)
- `email`
- `alamat`
- `province_id`, `regency_id`, `district_id`, `village_id`
- `verification_status` (set to 'pending')

### media table (Spatie Media Library)
- Stores KTP photos
- Collection: 'ktp'
- Linked to respondent via polymorphic relationship

## Integration Points

### WhatsApp Service
**File:** `app/Services/WhatsAppService.php`
- Sends OTP to registered phone number
- Used after successful registration

### Wilayah API
**Controller:** `app/Http/Controllers/Api/WilayahController.php`
- Provides location data for dropdowns
- Ensures valid location selections

## Future Enhancements

1. **Progressive Image Upload**
   - Upload KTP during Step 4 instead of at submission
   - Show progress bar for large files

2. **Auto-save Draft**
   - Save form data to localStorage
   - Allow users to resume registration

3. **Alternative Verification**
   - Email verification option
   - SMS backup for OTP

4. **Enhanced KTP Processing**
   - OCR to auto-fill data from KTP photo
   - Automatic data validation against KTP

5. **Social Registration**
   - Login with Google
   - Login with Facebook

## Testing Checklist

- [ ] Form displays correctly on desktop
- [ ] Form displays correctly on mobile
- [ ] Step navigation works (next/previous)
- [ ] Form validation prevents incomplete submissions
- [ ] NIK uniqueness is enforced
- [ ] Phone uniqueness is enforced
- [ ] Location cascades load correctly
- [ ] KTP upload works and shows preview
- [ ] File size validation works
- [ ] File type validation works
- [ ] Successful registration creates respondent
- [ ] OTP is sent after registration
- [ ] Redirect to OTP verification works
- [ ] Error messages display correctly
- [ ] Old input is preserved on validation errors

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database migrations are run
- Ensure Spatie Media Library is properly configured
- Confirm WhatsApp service credentials are set

## Files Modified/Created

### Created
- `REGISTRATION_FEATURE.md` (this file)

### Modified
- `resources/views/auth/register.blade.php` - Complete redesign
- `resources/views/layouts/app.blade.php` - Added custom CSS
- `app/Http/Controllers/AuthController.php` - Fixed field mappings
- `routes/web.php` - Already had routes (no changes needed)
- `app/Models/Respondent.php` - Already properly configured

## Deployment Notes

1. Clear cache after deployment:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. Ensure storage is linked:
   ```bash
   php artisan storage:link
   ```

3. Verify media disk is configured in `config/filesystems.php`

4. Test OTP delivery in production environment

5. Monitor initial registrations for any issues
