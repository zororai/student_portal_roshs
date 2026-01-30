# ROSHS Student Portal - Features Documentation

A comprehensive school management system built with Laravel, designed for managing students, teachers, parents, academics, and finances.

---

## Table of Contents

1. [User Roles](#user-roles)
2. [Public Website](#public-website)
3. [Admin Features](#admin-features)
4. [Teacher Features](#teacher-features)
5. [Parent Features](#parent-features)
6. [Student Features](#student-features)
7. [Finance & Accounting](#finance--accounting)
8. [Academic Management](#academic-management)
9. [Communication](#communication)
10. [Settings & Configuration](#settings--configuration)

---

## User Roles

The system supports multiple user roles with role-based access control (RBAC) using Spatie Permissions:

- **Admin** - Full system access
- **Teacher** - Academic management, student records, assessments
- **Parent** - View child's records, submit payments, groceries
- **Student** - View results, timetable, reading materials

---

## Public Website

### Landing Pages
- **Home Page** - School overview with banners
- **About Page** - School information
- **Contact Page** - Contact details
- **Courses Page** - Available courses/subjects
- **News/Newsletters** - Published school news

### Online Application
- Student application form for new admissions
- Application status tracking
- Success confirmation page

### Public Shop
- View school products
- Product details

---

## Admin Features

### Dashboard
- Overview statistics (students, teachers, classes)
- Gender distribution charts
- Assessment statistics
- Quick action buttons

### User Management
- Create, edit, delete users
- Assign roles to users
- Force password reset
- Sidebar permissions management

### Role & Permission Management
- Create and manage roles
- Create and manage permissions
- Assign permissions to roles
- Sidebar access control per user

### Student Management
- **Student Records** - Full CRUD operations
- **Student Registration** - With or without parents
- **Student ID Cards** - Generate and print ID cards with photo
- **Webcam Photo Capture** - Take student photos via webcam
- **Bulk Import** - Import students via Excel/CSV
- **Student Transfer** - Mark students as transferred
- **Seat Assignment** - Assign chair/desk numbers
- **Class Upgrade** - Promote students to next class

### Teacher Management
- Teacher registration and management
- Subject assignment to teachers
- Class teacher assignment
- Session/period management
- Force password reset
- QR code generation for attendance

### Parent Management
- Parent registration
- Link parents to students
- Self-registration via token link
- Force password reset

### Class Management
- Create and manage classes
- Assign subjects to classes
- Class formats configuration

### Subject Management
- Create and manage subjects
- Assign subjects to classes
- Bulk assign/unassign subjects

### Library Records
- **Book Archive** - Manage library books
  - Add books with title, number, condition, author, ISBN
  - Track book condition/health
  - Upload book images
  - Track quantity and availability
- **Issue Books** - Issue books to students
  - Search students
  - Search books from archive
  - Set issue and due dates
- **Return Books** - Mark books as returned
- **Book History** - View borrowing history per book
- **Student History** - View borrowing history per student

### Disciplinary Records
- Record student disciplinary issues
- Track offense types and severity
- Add notes and actions taken
- View history by student

### Medical Reports
- View medical reports submitted by parents
- Acknowledge and review reports
- Track student health conditions

### Attendance Management
- **Student Attendance** - Track daily attendance by class
- **Teacher Attendance** - QR code-based check-in/out
  - Generate QR codes for teachers
  - Attendance logbook
  - Availability tracking
  - History and reports
- **Attendance Settings** - Configure attendance rules

### Applicants Management
- View student applications
- Approve/reject applications
- Convert applicants to students

### Timetable Management
- Create class timetables
- Assign teachers to periods
- Conflict detection
- Master timetable view
- Clear timetable functionality

### Results Management
- View all student results
- Results approval workflow
- Clean/delete results
- Active results management
- Term/semester configuration

### Assessment Marks Approval
- Review pending assessment marks
- Approve or reject marks
- Bulk approval functionality

### Marking Scheme
- View assessments by class
- View marks per assessment
- Export marking schemes

### Groceries Management
- Create grocery lists for students
- View responses by class
- Acknowledge submissions
- Block settings for non-compliance
- Grocery arrears tracking
- Student grocery history

### Scholarships
- Manage student scholarships
- Set scholarship percentages
- Bulk update scholarships

### Audit Trail
- Track all system activities
- View change history
- Export audit logs
- Clear old logs

### Notifications
- Send notifications to users
- Target by role or specific users
- SMS notifications
- View notification history

### Payment Verification
- Review payment proofs submitted by parents
- Verify or reject payments
- Link to student accounts

---

## Teacher Features

### Dashboard
- Personal teaching overview
- Assigned classes and subjects
- Quick action buttons

### My Attendance
- View personal attendance history
- Self check-out functionality

### Leave Applications
- Submit leave requests
- View leave status
- Track leave history

### Student Records
- View students in assigned classes
- Transfer students between classes
- Class teacher functions

### Class Attendance
- Take student attendance for class
- View attendance history

### Assessments
- **Create Assessments** - Tests, quizzes, exams
- **Record Marks** - Enter student marks
- **View Assessments** - Review submitted assessments
- **Marking Scheme** - View and export marking schemes
- **Comments** - Add assessment comments

### Results Management
- Enter student results
- Edit existing results
- View results by class
- Submit results for approval

### Syllabus Management
- Create syllabus topics
- Edit and manage topics
- Track syllabus coverage

### Schemes of Work
- Create data-driven schemes
- Link to syllabus topics
- Track topic completion status
- Create remedial lessons
- Evaluation reports

### Reading Materials
- Upload subject materials
- Share with students
- Manage file attachments

### Timetable
- View personal timetable
- See class schedules

### Disciplinary Records
- Record student discipline issues
- View disciplinary history

### Device Registration
- Register mobile device for attendance
- View device status

### Push Notifications
- Subscribe to notifications
- Receive real-time alerts

---

## Parent Features

### Dashboard
- Overview of children's information
- Quick links to features

### View Results
- View child's academic results
- Term-by-term breakdown

### Assessments
- View child's assessment marks
- Track academic progress

### Groceries
- View grocery requests
- Submit grocery responses
- Track grocery history

### Medical Reports
- Submit medical reports
- View submitted reports
- Track acknowledgment status

### Disciplinary Records
- View child's disciplinary history
- Track incidents and actions

### Payment Verification
- Submit payment proof
- Upload receipts/screenshots
- Track verification status

### Payment History
- View payment records
- Track outstanding balances

### Class Timetable
- View child's class timetable

---

## Student Features

### Dashboard
- Personal academic overview
- Quick links to features

### View Results
- View personal academic results
- Term breakdown

### Timetable
- View personal class timetable

### Reading Materials
- Access shared materials
- Download study resources

### Attendance
- View attendance history

### Change Password
- Update account password

---

## Finance & Accounting

### Student Payments
- Record student fee payments
- Generate receipts
- Track payment history
- Export payment reports

### Fee Management
- **Fee Types** - Create fee categories
- **Fee Categories** - Organize fees
- **Fee Level Groups** - Set fees by class level
- **Term Fees** - Configure term-based fees

### Parents Arrears
- Track outstanding balances
- Generate arrears reports
- Export arrears data

### Financial Dashboard
- Revenue overview
- Expense tracking
- Financial charts

### Cash Book
- Record cash transactions
- Track income and expenses
- Generate reports

### Expense Management
- Record school expenses
- Expense categories
- Approval workflow
- Expense reports

### Purchase Orders
- Create purchase orders
- Supplier management
- Order approval workflow
- Invoice recording
- Payment tracking

### Payroll
- Employee salary management
- Generate payroll
- Approval workflow
- Payslip generation

### Inventory Management
- Product management
- Stock tracking
- Stock movements
- Barcode support

### Point of Sale (POS)
- Process sales
- Find products by barcode
- Sales history
- Receipt generation

### Bank Reconciliation
- Bank account management
- Transaction reconciliation

### Ledger
- General ledger accounts
- Ledger entries
- Account management

### Budget Management
- Budget periods
- Budget items
- Budget tracking

### Financial Reports
- Income statements
- Balance sheets
- Expense reports
- Fee collection reports

---

## Academic Management

### Term/Semester Management
- Create academic terms
- Set active term
- Results status configuration

### Grading System
- Configure grade scales
- Set grade boundaries

### Syllabus Topics
- Define curriculum topics
- Track coverage

### Schemes of Work
- Week-by-week planning
- Topic scheduling
- Progress tracking

### Results
- Enter student results
- Approval workflow
- Report generation

### Assessments
- Create assessments
- Enter marks
- Track performance

---

## Communication

### School Notifications
- Create notifications
- Target specific audiences
- In-app notifications
- SMS notifications

### SMS Integration
- Send SMS to parents
- Registration SMS
- Notification SMS
- SMS settings configuration

### Newsletters
- Create and publish newsletters
- Public viewing
- Archive management

### Events
- Create school events
- Event calendar
- Event management

### Push Notifications
- Web push notifications
- Real-time alerts

---

## Settings & Configuration

### School Settings
- **Class Formats** - Configure class naming
- **Upgrade Direction** - Set class progression
- **General Settings** - School-wide configurations

### Geolocation Settings
- Define school boundaries
- GPS-based attendance validation
- Multiple location support

### SMS Settings
- SMS gateway configuration
- Message templates
- Usage tracking

### Website Settings
- **General** - School name, contact info
- **Colors** - Theme customization
- **Images** - Logo, banners
- **Text** - Content management
- **Banners** - Homepage banners

### Attendance Settings
- Check-in/out times
- Late threshold
- Attendance rules

---

## Technical Stack

- **Framework**: Laravel (PHP)
- **Frontend**: Blade Templates, Alpine.js, Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Auth with Spatie Permissions
- **File Storage**: Local storage with public assets
- **SMS**: Configurable SMS gateway
- **Push Notifications**: Web Push API

---

## Security Features

- Role-based access control (RBAC)
- Permission-based sidebar visibility
- Password policies
- Force password change on first login
- Audit trail logging
- Session management

---

*Last Updated: January 2026*
