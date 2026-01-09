# School Timetable System Documentation

## Overview

The timetable system allows administrators to automatically generate and manage class schedules for the school. It supports multiple classes, subjects with different lesson durations (single, double, triple, quad periods), break times, lunch times, and teacher conflict detection.

**URL:** `/admin/timetable`

---

## Table of Contents

1. [Database Structure](#database-structure)
2. [How to Create a Timetable](#how-to-create-a-timetable)
3. [Configuration Options](#configuration-options)
4. [Subject Lesson Configuration](#subject-lesson-configuration)
5. [Auto-Generation Algorithm](#auto-generation-algorithm)
6. [Editing Timetables](#editing-timetables)
7. [Clearing Timetables](#clearing-timetables)
8. [Viewing Timetables](#viewing-timetables)
9. [Technical Details](#technical-details)

---

## Database Structure

### Tables

#### `timetables`
Stores individual time slots for each class.

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `class_id` | bigint | Reference to `grades` table |
| `subject_id` | bigint | Reference to `subjects` table (nullable for free periods) |
| `teacher_id` | bigint | Reference to `teachers` table (nullable) |
| `day` | string | Day of the week (Monday-Friday) |
| `start_time` | time | Slot start time |
| `end_time` | time | Slot end time |
| `slot_type` | string | Type: `subject`, `break`, or `lunch` |
| `slot_order` | integer | Order of slot in the day |
| `academic_year` | string | Academic year (e.g., "2025") |
| `term` | integer | Term number (1, 2, or 3) |

#### `timetable_settings`
Stores the configuration used to generate a class timetable.

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `class_id` | bigint | Reference to `grades` table |
| `start_time` | time | School day start time |
| `break_start` | time | Break period start |
| `break_end` | time | Break period end |
| `lunch_start` | time | Lunch period start |
| `lunch_end` | time | Lunch period end |
| `end_time` | time | School day end time |
| `subject_duration` | integer | Duration per subject period (minutes) |
| `academic_year` | string | Academic year |
| `term` | integer | Term number |

#### `subjects` (Lesson Configuration Fields)
| Field | Type | Description |
|-------|------|-------------|
| `single_lessons_per_week` | integer | Number of single-period lessons per week |
| `double_lessons_per_week` | integer | Number of double-period lessons per week |
| `triple_lessons_per_week` | integer | Number of triple-period lessons per week |
| `quad_lessons_per_week` | integer | Number of quad-period (4 consecutive) lessons per week |

---

## How to Create a Timetable

### Step 1: Navigate to Timetable Management
1. Log in as an **Administrator**
2. Go to `/admin/timetable`
3. Click the **"Generate Timetable"** button

### Step 2: Select Classes
- Choose one or more classes from the list
- Use **"Select All Classes"** to generate timetables for all classes at once
- Each selected class will get its own timetable based on its assigned subjects

### Step 3: Configure School Hours
Set the following time parameters:

| Setting | Default | Description |
|---------|---------|-------------|
| **Start Time** | 07:30 | When the school day begins |
| **End Time** | 15:30 | When the school day ends |
| **Break Start** | 10:00 | Morning break begins |
| **Break End** | 10:30 | Morning break ends |
| **Lunch Start** | 12:30 | Lunch break begins |
| **Lunch End** | 13:30 | Lunch break ends |
| **Subject Duration** | 40 min | Length of each period (20-120 minutes) |

### Step 4: Set Academic Period
- **Academic Year:** The year for this timetable (e.g., 2025)
- **Term:** Select Term 1, 2, or 3

> **Note:** Different terms can have different timetables. The system auto-detects the current term from `ResultsStatus`.

### Step 5: Generate
Click **"Generate Timetable"** to create the schedule automatically.

---

## Configuration Options

### Time Slots
The system automatically calculates time slots based on:
- School start and end times
- Break and lunch periods (fixed times that don't move)
- Subject duration setting

### Example Day Structure (40-minute periods)
```
07:30 - 08:10  |  Period 1 (Subject)
08:10 - 08:50  |  Period 2 (Subject)
08:50 - 09:30  |  Period 3 (Subject)
09:30 - 10:00  |  Period 4 (Subject - shorter before break)
10:00 - 10:30  |  BREAK
10:30 - 11:10  |  Period 5 (Subject)
11:10 - 11:50  |  Period 6 (Subject)
11:50 - 12:30  |  Period 7 (Subject)
12:30 - 13:30  |  LUNCH
13:30 - 14:10  |  Period 8 (Subject)
14:10 - 14:50  |  Period 9 (Subject)
14:50 - 15:30  |  Period 10 (Subject)
```

---

## Subject Lesson Configuration

Before generating timetables, configure each subject's weekly lesson requirements:

### Lesson Types

| Type | Periods | Use Case |
|------|---------|----------|
| **Single** | 1 period | Standard lessons |
| **Double** | 2 consecutive periods | Labs, practical work |
| **Triple** | 3 consecutive periods | Extended practicals |
| **Quad** | 4 consecutive periods | Full practical sessions |

### Configuration Location
Subjects are configured via the **Subjects Management** section (`/admin/subject`).

### Example Configuration
For a subject like "Chemistry":
- Single lessons per week: 2
- Double lessons per week: 1 (for lab work)
- Triple lessons per week: 0
- Quad lessons per week: 0

**Total weekly periods = 2 + (1 × 2) = 4 periods**

---

## Auto-Generation Algorithm

The system uses an intelligent algorithm to distribute lessons with **strict constraints**.

### Hard Constraints (MUST NOT BE VIOLATED)

| Rule | Description |
|------|-------------|
| **One Block Per Day** | A subject may appear only ONCE per day |
| **Consecutive Only** | Multi-period lessons must be adjacent slots |
| **No Repetition** | Non-consecutive repeated subjects are forbidden |
| **No Force Fill** | Free slots are NOT force-filled to meet lesson counts |
| **Teacher Conflicts** | Teacher cannot be double-booked |

### Lesson Type Rules

| Lesson Type | Allowed on Same Day |
|-------------|---------------------|
| Single | Exactly once |
| Double | Once (2 adjacent slots) |
| Triple | Once (3 adjacent slots) |
| Quad | Once (4 adjacent slots) |

### State Tracking

During generation, the system maintains:
```php
$usedSubjectsByDay = [
    'Monday'    => [],  // Array of subject IDs placed
    'Tuesday'   => [],
    'Wednesday' => [],
    'Thursday'  => [],
    'Friday'    => [],
];
```

### Algorithm Steps

#### 1. Lesson Pool Creation
- Collects all lessons from class subjects
- Groups by type (quad, triple, double, single)
- Sorts longest lessons first (harder to place)

#### 2. Day Distribution
- Spreads subjects across different days
- **STRICT:** Each subject appears maximum ONCE per day
- Shuffles available days for variety

#### 3. Consecutive Slot Finding
- Finds available consecutive slots for multi-period lessons
- Double/triple lessons CAN span across break/lunch
- **STRICT:** Returns null if subject already exists on day
- Respects teacher availability

#### 4. Teacher Conflict Detection
- Checks if teacher is already assigned elsewhere at the same time
- Prevents double-booking of teachers
- Works across all classes in the same term/year

### 5. Free Periods
- Unassigned slots remain as free periods
- Gap slots (irregular timing) stay empty
- Shows as empty cells in the timetable view

---

## Editing Timetables

### Access Edit Mode
1. Go to `/admin/timetable`
2. Find the class card
3. Click **"Edit Timetable"**

### What Can Be Edited
- **Subject Assignment:** Change which subject is in each slot
- **Teacher Assignment:** Assign different teachers to slots

### Validation Rules
When editing, the system checks:
1. **Subject belongs to class:** Only subjects assigned to the class can be selected
2. **Teacher teaches subject:** Only the assigned teacher for a subject can be selected
3. **No time conflicts:** Teacher cannot be in two places at once

### Conflict Warnings
If any edits cause conflicts, the system will:
- Show a warning message
- Skip the conflicting changes
- Apply all valid changes

---

## Clearing Timetables

### When to Clear
- Start of a new term
- Major schedule restructuring
- Correcting generation errors

### How to Clear
1. Go to `/admin/timetable`
2. Click **"Clear Timetables"** (red button)
3. Select:
   - **Academic Year** (required)
   - **Term** (required)
   - **Class** (optional - leave empty for all classes)
4. Click **"Clear Timetables"**

> **Warning:** This action is permanent and cannot be undone!

---

## Viewing Timetables

### Admin View
- `/admin/timetable/{classId}` - Full timetable grid with edit options

### Teacher View
- `/teacher/timetable` - Shows only the logged-in teacher's schedule across all classes

### Student View
- `/my-timetable` - Shows the student's class timetable

### Parent View
- `/child-timetable` - Shows timetables for all children

---

## Technical Details

### Files Structure

```
app/
├── Http/Controllers/
│   ├── AdminTimetableController.php    # Admin CRUD operations
│   └── TimetableController.php         # Teacher/Student/Parent views
├── Timetable.php                       # Timetable model
└── TimetableSetting.php                # Settings model

resources/views/backend/admin/timetable/
├── index.blade.php                     # Class list view
├── create.blade.php                    # Generation form
├── show.blade.php                      # View timetable
└── edit.blade.php                      # Edit timetable
```

### Routes

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/admin/timetable` | `index` | List all classes |
| GET | `/admin/timetable/create` | `create` | Show generation form |
| POST | `/admin/timetable` | `store` | Generate timetable |
| GET | `/admin/timetable/{id}` | `show` | View class timetable |
| GET | `/admin/timetable/{id}/edit` | `edit` | Edit form |
| PUT | `/admin/timetable/{id}` | `update` | Save edits |
| DELETE | `/admin/timetable/{id}` | `destroy` | Delete timetable |
| POST | `/admin/timetable/check-conflicts` | `checkConflicts` | AJAX conflict check |
| POST | `/admin/timetable/clear` | `clear` | Clear timetables |

### Key Methods

#### `generateTimetable(TimetableSetting $settings)`
Main generation algorithm that:
1. Deletes existing timetable for the class/term/year
2. Creates day structures with breaks and lunch
3. Builds lesson pool from class subjects
4. Places lessons prioritizing longer durations
5. Creates timetable records for all slots

#### `checkTeacherConflict($teacherId, $day, $startTime, $endTime, $excludeId)`
Static method on `Timetable` model that checks for overlapping time slots.

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| **No subjects appear** | Ensure subjects are assigned to the class via class management |
| **Teacher conflicts** | Check teacher assignments - one teacher per subject |
| **Missing periods** | Verify lesson counts are configured on subjects |
| **Free periods everywhere** | Subject lesson counts may not fill available slots |

### Best Practices

1. **Configure subjects first** - Set up all lesson types before generating
2. **Assign teachers to subjects** - Required for conflict detection
3. **Start fresh each term** - Clear old timetables before generating new ones
4. **Review after generation** - Edit any slots that need adjustment
5. **Test with one class** - Generate for single class first, then batch

---

## Version History

| Date | Version | Changes |
|------|---------|---------|
| 2025 | 1.0 | Initial timetable system |

---

*Documentation generated for ROSHS Student Portal*
