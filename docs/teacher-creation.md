# Teacher Creation Process

This document explains how a teacher account is created in the ROSHS Student Portal at `/teacher/create`.

---

## Overview

The teacher creation process uses a **Quick Setup** approach where only basic information is collected initially. Teachers complete their full profile (date of birth, addresses, profile picture) and change their password on first login.

---

## Route Configuration

| Method | URI | Controller | Route Name |
|--------|-----|------------|------------|
| GET | `/teacher/create` | `TeacherController@create` | `teacher.create` |
| POST | `/teacher/store` | `TeacherController@store` | `teacher.store` |

Defined in `routes/web.php`:
```php
Route::resource('teacher', 'TeacherController')->except(['show']);
```

---

## Form Fields

### Required Fields

| Field | Type | Validation Rules |
|-------|------|------------------|
| **Name** | text | `required\|string\|max:255` |
| **Phone** | text | `required\|string\|max:255\|unique:teachers,phone` |
| **Gender** | radio | `required\|string` (male/female) |

### Optional Roles (Checkboxes)

| Role | Field Name | Description |
|------|------------|-------------|
| **Class Teacher** | `is_class_teacher` | Can manage class students and attendance |
| **Head of Department** | `is_hod` | Department management responsibilities |
| **Sport Director** | `is_sport_director` | Manages sports activities and teams |

---

## Data Flow

### 1. Form Submission

When the form is submitted to `POST /teacher/store`:

```
User Input → Validation → User Creation → Teacher Profile Creation → Role Assignment → Notifications
```

### 2. User Account Creation

A `User` record is created with:

| Field | Value |
|-------|-------|
| `name` | Submitted name |
| `email` | Auto-generated placeholder: `teacher_{phone}@placeholder.co.zw` |
| `password` | Hashed default: `12345678` |
| `profile_picture` | Default: `avatar.png` |

### 3. Teacher Profile Creation

A `Teacher` record is created linked to the user:

| Field | Value |
|-------|-------|
| `user_id` | Created user's ID |
| `gender` | Submitted gender |
| `phone` | Submitted phone number |
| `dateofbirth` | `null` (completed on first login) |
| `current_address` | `null` (completed on first login) |
| `permanent_address` | `null` (completed on first login) |
| `is_class_teacher` | Boolean from form |
| `is_hod` | Boolean from form |
| `is_sport_director` | Boolean from form |

### 4. Role Assignment

The user is assigned the `Teacher` role via Spatie permissions:

```php
$user->assignRole('Teacher');
```

---

## Notifications

After successful creation, credentials are sent via:

### Email Notification
- **Template**: `emails.teacher-credentials`
- **Subject**: "Your Teacher Account Credentials - {App Name}"
- **Contains**: Name, email, password

### SMS Notification
- **Format**: Phone number normalized to `+263` country code
- **Message**: `RSH School: Teacher account created. Login: {email}, Password: {password}. Complete profile on first login.`

---

## Login Credentials

| Credential | Value |
|------------|-------|
| **Username** | Phone Number |
| **Password** | `12345678` (must change on first login) |

---

## Duplicate Handling

The system handles duplicate phone numbers:

1. **Existing Teacher**: Returns error "A teacher with this phone number already exists"
2. **Orphaned User Record**: Deletes the orphaned record and proceeds with creation

---

## Database Tables Affected

| Table | Action |
|-------|--------|
| `users` | INSERT new user record |
| `teachers` | INSERT new teacher profile |
| `model_has_roles` | INSERT role assignment |

---

## Teacher Model Fields

```php
protected $fillable = [
    'user_id',
    'gender',
    'phone',
    'dateofbirth',
    'current_address',
    'permanent_address',
    'is_class_teacher',
    'is_hod',
    'is_sport_director',
    'qr_code',
    'qr_code_token',
    'device_registration_status',
];
```

---

## First Login Flow

On first login, teachers are required to:

1. Set a real email address (replacing the placeholder)
2. Enter date of birth
3. Provide current address
4. Provide permanent address
5. Upload profile picture (optional)
6. Change password from default `12345678`

---

## Related Files

| File | Purpose |
|------|---------|
| `app/Http/Controllers/TeacherController.php` | Controller logic |
| `app/Teacher.php` | Eloquent model |
| `resources/views/backend/teachers/create.blade.php` | Create form view |
| `resources/views/emails/teacher-credentials.blade.php` | Email template |
| `app/Helpers/SmsHelper.php` | SMS sending utility |
| `routes/web.php` | Route definitions |

---

## Error Handling

- **Validation Errors**: Displayed inline below each field with red styling
- **Email Failures**: Logged to Laravel log, does not interrupt creation
- **SMS Failures**: Logged to Laravel log, does not interrupt creation

---

## Success Response

On successful creation:
- Redirects to `teacher.index` (teacher list)
- Flash message: "Teacher created successfully! Login credentials have been sent via email and SMS."
