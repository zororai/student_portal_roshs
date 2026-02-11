# ROSHS Student Portal - Application Functions Reference

> Auto-generated documentation of all public functions in the application.
> Total Functions: **1,235+**

---

## Table of Contents

1. [Console Commands](#console-commands)
2. [Controllers](#controllers)
   - [Authentication](#authentication-controllers)
   - [Finance & Accounting](#finance--accounting-controllers)
   - [Academic Management](#academic-management-controllers)
   - [Student Management](#student-management-controllers)
   - [Teacher Management](#teacher-management-controllers)
   - [Administration](#administration-controllers)
   - [Communication](#communication-controllers)
   - [Assets & Inventory](#assets--inventory-controllers)
   - [Library](#library-controllers)
   - [Website](#website-controllers)
3. [Models](#models)
4. [Services](#services)
5. [Middleware](#middleware)
6. [Traits](#traits)
7. [Jobs & Listeners](#jobs--listeners)
8. [Policies](#policies)

---

## Console Commands

| Command | Function | Description |
|---------|----------|-------------|
| `AutoUpgradeStudents` | `handle()` | Automatically upgrade students to next class |
| `CleanParentData` | `handle()` | Clean orphaned parent data |
| `CleanStudentData` | `handle()` | Clean orphaned student data |
| `CleanupGeneratedStudents` | `handle()` | Remove generated test students |
| `CreateDummyParent` | `handle()` | Create dummy parent for testing |
| `RedistributeStudents` | `handle()` | Redistribute students across classes |
| `RollbackStudentUpgrade` | `handle()` | Rollback student class upgrades |
| `SeedAssetData` | `handle()` | Seed asset management data |
| `SeedSchoolData` | `handle()` | Seed initial school data |
| `SendLessonReminders` | `handle()` | Send lesson reminder notifications |
| `SyncSidebarPermissions` | `handle()` | Sync sidebar permissions |
| `TransferOrGenerateStudents` | `handle()` | Transfer or generate student records |

---

## Controllers

### Authentication Controllers

#### `LoginController`
| Function | Description |
|----------|-------------|
| `login()` | Handle user login |

#### `ForgotPasswordController`
| Function | Description |
|----------|-------------|
| `__construct()` | Initialize password reset |

#### `RegisterController`
| Function | Description |
|----------|-------------|
| `__construct()` | Initialize registration |

#### `ResetPasswordController`
| Function | Description |
|----------|-------------|
| `__construct()` | Initialize password reset |

#### `VerificationController`
| Function | Description |
|----------|-------------|
| `__construct()` | Initialize email verification |

---

### Finance & Accounting Controllers

#### `AccountsPayableController`
| Function | Description |
|----------|-------------|
| `index()` | View accounts payable dashboard |
| `invoices()` | List supplier invoices |
| `createInvoice()` | Create new supplier invoice form |
| `storeInvoice()` | Store new supplier invoice |
| `showInvoice()` | View invoice details |
| `showPaymentForm()` | Display payment form |
| `recordPayment()` | Record supplier payment |
| `aging()` | View A/P aging report |

#### `AccountsReceivableController`
| Function | Description |
|----------|-------------|
| `index()` | View accounts receivable dashboard |
| `invoices()` | List student invoices |
| `createInvoice()` | Create new student invoice form |
| `storeInvoice()` | Store new student invoice |
| `showInvoice()` | View invoice details |
| `aging()` | View A/R aging report |
| `studentStatement()` | Generate student statement |

#### `BankReconciliationController`
| Function | Description |
|----------|-------------|
| `index()` | Bank reconciliation dashboard |
| `accounts()` | List bank accounts |
| `createAccount()` | Create bank account form |
| `storeAccount()` | Store new bank account |
| `transactions()` | View bank transactions |
| `importStatement()` | Import bank statement |
| `reconcile()` | Perform reconciliation |
| `matchTransactions()` | Match transactions |
| `unmatch()` | Unmatch transactions |

#### `BudgetController`
| Function | Description |
|----------|-------------|
| `index()` | View budgets |
| `create()` | Create budget form |
| `store()` | Store new budget |
| `show()` | View budget details |
| `addItem()` | Add budget item |
| `updateActual()` | Update actual amounts |
| `activate()` | Activate budget |
| `close()` | Close budget period |
| `revenueForecast()` | Revenue forecast view |
| `storeRevenueForecast()` | Store revenue forecast |
| `expenseForecast()` | Expense forecast view |
| `storeExpenseForecast()` | Store expense forecast |
| `comparison()` | Budget vs actual comparison |

#### `CashBookController`
| Function | Description |
|----------|-------------|
| `index()` | View cash book entries |
| `create()` | Create entry form |
| `store()` | Store new entry |
| `show()` | View entry details |
| `edit()` | Edit entry form |
| `update()` | Update entry |
| `destroy()` | Delete entry |
| `report()` | Cash book report |

#### `ExpenseController`
| Function | Description |
|----------|-------------|
| `index()` | List expenses |
| `create()` | Create expense form |
| `store()` | Store new expense |
| `show()` | View expense details |
| `edit()` | Edit expense form |
| `update()` | Update expense |
| `approve()` | Approve expense |
| `categories()` | Manage expense categories |
| `storeCategory()` | Store expense category |
| `report()` | Expense report |

#### `FinanceController`
| Function | Description |
|----------|-------------|
| `studentPayments()` | View student payments |
| `storePayment()` | Record student payment |
| `getStudentFeePayments()` | Get student fee payments |
| `paynowPayment()` | Process Paynow payment |
| `parentsArrears()` | View parent arrears |
| `exportParentsArrears()` | Export arrears report |
| `exportStudentPayments()` | Export payments report |
| `schoolIncome()` | View school income |
| `schoolExpenses()` | View school expenses |
| `products()` | View products |
| `storeIncome()` | Record income |
| `destroyIncome()` | Delete income record |
| `storeExpense()` | Record expense |
| `destroyExpense()` | Delete expense record |
| `storeProduct()` | Store product |
| `destroyProduct()` | Delete product |
| `financialStatements()` | View financial statements |
| `accountingGuide()` | View accounting guide |
| `parentPaymentHistory()` | Parent payment history |
| `enforceFees()` | Enforce fee payment |

#### `FinanceDashboardController`
| Function | Description |
|----------|-------------|
| `index()` | Finance dashboard |
| `incomeStatement()` | Income statement report |
| `balanceSheet()` | Balance sheet report |
| `feeReport()` | Fee collection report |
| `expenseReport()` | Expense report |

#### `FinancialReportsController`
| Function | Description |
|----------|-------------|
| `trialBalance()` | Trial balance report |
| `profitAndLoss()` | Profit & loss statement |
| `balanceSheet()` | Balance sheet |
| `generalLedger()` | General ledger report |

#### `JournalController`
| Function | Description |
|----------|-------------|
| `index()` | List journal batches |
| `create()` | Create journal entry form |
| `store()` | Store journal entry |
| `show()` | View journal batch |
| `edit()` | Edit journal batch |
| `update()` | Update journal batch |
| `approve()` | Approve journal batch |
| `post()` | Post journal to ledger |
| `destroy()` | Delete journal batch |

#### `LedgerController`
| Function | Description |
|----------|-------------|
| `index()` | View chart of accounts |
| `createAccount()` | Create ledger account form |
| `storeAccount()` | Store ledger account |
| `showAccount()` | View account details |
| `editAccount()` | Edit account form |
| `updateAccount()` | Update account |
| `entries()` | View ledger entries |
| `createEntry()` | Create entry form |
| `storeEntry()` | Store ledger entry |
| `trialBalance()` | Trial balance |

#### `PaymentController`
| Function | Description |
|----------|-------------|
| `create()` | Create payment form |
| `store()` | Store payment |
| `downloadReceipt()` | Download payment receipt |
| `index()` | List payments |
| `receipt()` | View receipt |

#### `PaymentVerificationController`
| Function | Description |
|----------|-------------|
| `create()` | Create verification request |
| `store()` | Store verification request |
| `adminIndex()` | Admin verification list |
| `show()` | View verification |
| `verify()` | Verify payment |
| `reject()` | Reject verification |

#### `PaynowSettingsController`
| Function | Description |
|----------|-------------|
| `index()` | Paynow settings view |
| `store()` | Store Paynow settings |
| `test()` | Test Paynow connection |

#### `PayrollController`
| Function | Description |
|----------|-------------|
| `index()` | Payroll dashboard |
| `salaries()` | View employee salaries |
| `createSalary()` | Create salary form |
| `storeSalary()` | Store salary |
| `editSalary()` | Edit salary form |
| `updateSalary()` | Update salary |
| `generate()` | Generate payroll form |
| `processGenerate()` | Process payroll generation |
| `show()` | View payroll details |
| `approve()` | Approve payroll |
| `markPaid()` | Mark payroll as paid |
| `payslip()` | Generate payslip |

#### `PurchaseOrderController`
| Function | Description |
|----------|-------------|
| `index()` | List purchase orders |
| `create()` | Create PO form |
| `store()` | Store purchase order |
| `show()` | View PO details |
| `approve()` | Approve purchase order |
| `markOrdered()` | Mark as ordered |
| `markReceived()` | Mark as received |
| `suppliers()` | Manage suppliers |
| `storeSupplier()` | Store supplier |
| `recordInvoice()` | Record supplier invoice |
| `recordPayment()` | Record supplier payment |

#### `FeeCategoryController`
| Function | Description |
|----------|-------------|
| `index()` | List fee categories |
| `create()` | Create category form |
| `store()` | Store fee category |
| `edit()` | Edit category form |
| `update()` | Update category |
| `destroy()` | Delete category |

#### `FeeLevelGroupController`
| Function | Description |
|----------|-------------|
| `index()` | List fee level groups |
| `create()` | Create group form |
| `store()` | Store fee level group |
| `edit()` | Edit group form |
| `update()` | Update group |
| `destroy()` | Delete group |
| `applyToNewStudents()` | Apply fees to new students |

#### `FeeTypeController`
| Function | Description |
|----------|-------------|
| `index()` | List fee types |
| `create()` | Create fee type form |
| `store()` | Store fee type |
| `edit()` | Edit fee type form |
| `update()` | Update fee type |
| `destroy()` | Delete fee type |

---

### Academic Management Controllers

#### `ResultController`
| Function | Description |
|----------|-------------|
| `index()` | Results dashboard |
| `parentindex()` | Parent results view |
| `resultsactive()` | Active results |
| `recordindex()` | Record results index |
| `Classnames()` | Get class names |
| `adminclassnames()` | Admin class names |
| `listResults()` | List results |
| `activelistResults()` | Active results list |
| `createByTeacher()` | Create result by teacher |
| `edit()` | Edit result |
| `update()` | Update result |
| `destroy()` | Delete result |
| `showstudentresults()` | Show student results |
| `viewupdateresults()` | View/update results |
| `changestatus()` | Change result status |
| `Showssubject()` | Show subjects |
| `Stuntentname()` | Get student names |
| `adminStuntentname()` | Admin student names |
| `store()` | Store result |
| `viewstatus()` | View result status |
| `classname()` | Get class name |
| `deleteResult()` | Delete result |
| `showResult()` | Show result |
| `adminshowResult()` | Admin show result |
| `show()` | View result |
| `studentshow()` | Student result view |
| `viewstudentshow()` | View student results |
| `adminViewResults()` | Admin view results |
| `getAdminResults()` | Get admin results |
| `parentAssessments()` | Parent assessments view |
| `cleanResults()` | Clean results data |
| `pendingApproval()` | Pending approval list |
| `getPendingResults()` | Get pending results |
| `approveResults()` | Approve results |
| `approveAllResults()` | Approve all results |
| `rejectResults()` | Reject results |
| `pendingAssessmentMarks()` | Pending assessment marks |
| `getPendingAssessmentMarks()` | Get pending marks |
| `getAssessmentMarksForApproval()` | Get marks for approval |
| `approveAssessmentMarks()` | Approve marks |
| `approveAllAssessmentMarks()` | Approve all marks |
| `exemptStudentResults()` | Exempt student from results |
| `removeExemption()` | Remove exemption |
| `getExemptedStudents()` | Get exempted students |

#### `ResultsStatusController`
| Function | Description |
|----------|-------------|
| `index()` | Results status list |
| `create()` | Create status form |
| `store()` | Store status |
| `edit()` | Edit status form |
| `update()` | Update status |
| `destroy()` | Delete status |

#### `ExerciseController`
| Function | Description |
|----------|-------------|
| `index()` | List exercises |
| `create()` | Create exercise form |
| `store()` | Store exercise |
| `show()` | View exercise |
| `edit()` | Edit exercise form |
| `update()` | Update exercise |
| `destroy()` | Delete exercise |
| `editQuestions()` | Edit exercise questions |
| `storeQuestion()` | Store question |
| `updateQuestion()` | Update question |
| `destroyQuestion()` | Delete question |
| `togglePublish()` | Toggle publish status |
| `toggleResults()` | Toggle results visibility |
| `submissions()` | View submissions |
| `markSubmission()` | Mark submission |
| `saveMarks()` | Save marks |
| `autoMark()` | Auto-mark MCQ questions |
| `getSubjectsForClass()` | Get subjects for class |

#### `StudentExerciseController`
| Function | Description |
|----------|-------------|
| `index()` | Student exercise list |
| `show()` | View exercise |
| `attempt()` | Attempt exercise |
| `saveAnswer()` | Save answer |
| `uploadFile()` | Upload file answer |
| `submit()` | Submit exercise |
| `saveAndExit()` | Save and exit |
| `results()` | View results |

#### `SchemeController`
| Function | Description |
|----------|-------------|
| `index()` | List schemes of work |
| `create()` | Create scheme form |
| `store()` | Store scheme |
| `show()` | View scheme |
| `edit()` | Edit scheme form |
| `update()` | Update scheme |
| `destroy()` | Delete scheme |
| `updateTopicStatus()` | Update topic status |
| `getSyllabusTopics()` | Get syllabus topics |
| `createRemedial()` | Create remedial lesson |
| `completeRemedial()` | Complete remedial lesson |
| `evaluationReport()` | Evaluation report |

#### `TeacherSyllabusController`
| Function | Description |
|----------|-------------|
| `index()` | List syllabi |
| `create()` | Create syllabus form |
| `store()` | Store syllabus |
| `edit()` | Edit syllabus form |
| `update()` | Update syllabus |
| `destroy()` | Delete syllabus |

#### `AdminSyllabusController`
| Function | Description |
|----------|-------------|
| `index()` | Admin syllabus list |
| `create()` | Create syllabus form |
| `store()` | Store syllabus |
| `edit()` | Edit syllabus form |
| `update()` | Update syllabus |
| `destroy()` | Delete syllabus |

#### `SubjectController`
| Function | Description |
|----------|-------------|
| `index()` | List subjects |
| `create()` | Create subject form |
| `show()` | View subject |
| `upload()` | Upload subject material |
| `store()` | Store subject |
| `edit()` | Edit subject form |
| `update()` | Update subject |
| `destroy()` | Delete subject |

#### `AddsubjectController`
| Function | Description |
|----------|-------------|
| `show()` | Show subject |
| `showread()` | Show reading material |
| `download()` | Download material |
| `studentviewsubject()` | Student view subject |
| `studentattendance()` | Student attendance |
| `create()` | Create form |
| `store()` | Store |
| `edit()` | Edit form |
| `update()` | Update |
| `destroy()` | Delete |

#### `GradeController`
| Function | Description |
|----------|-------------|
| `index()` | List classes |
| `adminindex()` | Admin class list |
| `create()` | Create class form |
| `store()` | Store class |
| `show()` | View class |
| `edit()` | Edit class form |
| `update()` | Update class |
| `destroy()` | Delete class |
| `assignSubject()` | Assign subject form |
| `storeAssignedSubject()` | Store assigned subject |

#### `TimetableController`
| Function | Description |
|----------|-------------|
| `studentView()` | Student timetable view |
| `teacherView()` | Teacher timetable view |
| `parentView()` | Parent timetable view |

#### `AdminTimetableController`
| Function | Description |
|----------|-------------|
| `index()` | Admin timetable list |
| `master()` | Master timetable view |
| `create()` | Create timetable form |
| `store()` | Store timetable |
| `show()` | View timetable |
| `edit()` | Edit timetable form |
| `update()` | Update timetable |
| `destroy()` | Delete timetable |
| `checkConflicts()` | Check scheduling conflicts |
| `clear()` | Clear timetable |

---

### Student Management Controllers

#### `StudentController`
| Function | Description |
|----------|-------------|
| `index()` | List students |
| `downloadIdCard()` | Download student ID card |
| `showid()` | Show student ID |
| `create()` | Create student form |
| `store()` | Store student |
| `show()` | View student |
| `edit()` | Edit student form |
| `update()` | Update student |
| `destroy()` | Delete student |
| `createWithParents()` | Create student with parents |
| `generateRollNumberAjax()` | Generate roll number |
| `storeWithParents()` | Store student with parents |
| `showChangePasswordForm()` | Change password form |
| `updatePassword()` | Update password |
| `resendParentSms()` | Resend parent SMS |
| `forcePasswordReset()` | Force password reset |
| `updateChairDesk()` | Update chair/desk |
| `seatAssignmentIndex()` | Seat assignment view |
| `updateSeatAssignment()` | Update seat assignment |
| `bulkUpdateToExisting()` | Bulk update students |

#### `StudentUpgradeController`
| Function | Description |
|----------|-------------|
| `index()` | Upgrade dashboard |
| `preview()` | Preview upgrade |
| `execute()` | Execute upgrade |
| `history()` | Upgrade history |
| `rollback()` | Rollback upgrade |

#### `ParentsController`
| Function | Description |
|----------|-------------|
| `index()` | List parents |
| `create()` | Create parent form |
| `store()` | Store parent |
| `show()` | View parent |
| `edit()` | Edit parent form |
| `update()` | Update parent |
| `destroy()` | Delete parent |
| `showRegistrationForm()` | Registration form |
| `completeRegistration()` | Complete registration |
| `registrationSuccess()` | Registration success |
| `forcePasswordReset()` | Force password reset |

#### `AttendanceController`
| Function | Description |
|----------|-------------|
| `index()` | Attendance dashboard |
| `classDetail()` | Class attendance detail |
| `cleanAttendance()` | Clean attendance data |
| `create()` | Create attendance form |
| `createByTeacher()` | Teacher create attendance |
| `store()` | Store attendance |
| `show()` | View attendance |
| `edit()` | Edit attendance form |
| `update()` | Update attendance |
| `destroy()` | Delete attendance |

#### `AttendanceScanController`
| Function | Description |
|----------|-------------|
| `index()` | QR scan dashboard |
| `exportLogbook()` | Export logbook |
| `scan()` | Process QR scan |
| `generateQrCode()` | Generate QR code |
| `regenerateQrCode()` | Regenerate QR code |
| `clearQrCode()` | Clear QR code |
| `clearAllQrCodes()` | Clear all QR codes |
| `getQrCode()` | Get QR code |
| `printQrCode()` | Print QR code |
| `teacherHistory()` | Teacher attendance history |
| `availability()` | Teacher availability |
| `attendanceHistory()` | Attendance history |
| `updateAttendance()` | Update attendance |
| `storeAttendance()` | Store attendance |
| `deleteAttendance()` | Delete attendance |

#### `AttendanceSettingsController`
| Function | Description |
|----------|-------------|
| `index()` | Attendance settings |
| `update()` | Update settings |

#### `DisciplinaryController`
| Function | Description |
|----------|-------------|
| `index()` | Disciplinary records |
| `getStudentsByClass()` | Get students by class |
| `store()` | Store record |
| `update()` | Update record |
| `destroy()` | Delete record |
| `parentIndex()` | Parent disciplinary view |

#### `MedicalReportController`
| Function | Description |
|----------|-------------|
| `parentIndex()` | Parent medical reports |
| `parentCreate()` | Create report form |
| `parentStore()` | Store report |
| `parentShow()` | View report |
| `adminIndex()` | Admin medical reports |
| `adminShow()` | Admin view report |
| `adminAcknowledge()` | Acknowledge report |
| `adminReview()` | Review report |

---

### Teacher Management Controllers

#### `TeacherController`
| Function | Description |
|----------|-------------|
| `index()` | List teachers |
| `create()` | Create teacher form |
| `store()` | Store teacher |
| `showChangePasswordForm()` | Change password form |
| `updatePassword()` | Update password |
| `myAttendance()` | My attendance view |
| `selfCheckout()` | Self checkout |
| `myClassStudents()` | My class students |
| `classAttendance()` | Class attendance |
| `storeClassAttendance()` | Store class attendance |
| `show()` | View teacher |
| `edit()` | Edit teacher form |
| `update()` | Update teacher |
| `studentRecord()` | Student record |
| `classStudents()` | Class students list |
| `transferStudent()` | Transfer student |
| `assessment()` | Assessment dashboard |
| `assessmentList()` | Assessment list |
| `createAssessment()` | Create assessment form |
| `storeAssessment()` | Store assessment |
| `storeAssessmentComment()` | Store assessment comment |
| `deleteAssessmentComment()` | Delete comment |
| `assessmentMarks()` | Assessment marks |
| `saveAssessmentMark()` | Save assessment mark |
| `viewAssessment()` | View assessment |
| `editAssessment()` | Edit assessment |
| `updateAssessment()` | Update assessment |
| `deleteAssessment()` | Delete assessment |
| `markingScheme()` | Marking scheme |
| `exportMarkingScheme()` | Export marking scheme |
| `getAssessmentMarks()` | Get assessment marks |
| `sessions()` | Teacher sessions |
| `updateSessions()` | Update sessions |
| `forcePasswordReset()` | Force password reset |
| `destroy()` | Delete teacher |

#### `TeacherDeviceController`
| Function | Description |
|----------|-------------|
| `index()` | Device management |
| `show()` | View device |
| `enableRegistration()` | Enable device registration |
| `allowPhoneChange()` | Allow phone change |
| `revokeDevice()` | Revoke device |
| `resetDevice()` | Reset device |
| `bulkEnableRegistration()` | Bulk enable registration |
| `registerDevice()` | Register device |
| `getDeviceStatus()` | Get device status |

#### `TeacherLeaveController`
| Function | Description |
|----------|-------------|
| `index()` | Leave applications |
| `create()` | Create leave form |
| `store()` | Store leave |
| `show()` | View leave |
| `destroy()` | Delete leave |

#### `AdminLeaveController`
| Function | Description |
|----------|-------------|
| `index()` | Admin leave list |
| `show()` | View leave application |
| `approve()` | Approve leave |
| `reject()` | Reject leave |
| `calendar()` | Leave calendar |

---

### Administration Controllers

#### `AdminUserController`
| Function | Description |
|----------|-------------|
| `index()` | List users |
| `create()` | Create user form |
| `store()` | Store user |
| `show()` | View user |
| `edit()` | Edit user form |
| `update()` | Update user |
| `destroy()` | Delete user |

#### `AdminStaffController`
| Function | Description |
|----------|-------------|
| `index()` | List staff |
| `show()` | View staff |

#### `AdminApplicantController`
| Function | Description |
|----------|-------------|
| `index()` | List applicants |
| `show()` | View applicant |
| `updateStatus()` | Update applicant status |
| `destroy()` | Delete applicant |

#### `AdminSchemeController`
| Function | Description |
|----------|-------------|
| `index()` | Admin schemes list |
| `show()` | View scheme |
| `syllabusIndex()` | Syllabus index |
| `teacherSchemes()` | Teacher schemes |

#### `AdminMarkingSchemeController`
| Function | Description |
|----------|-------------|
| `index()` | Marking scheme list |
| `classAssessments()` | Class assessments |
| `assessmentMarks()` | Assessment marks |
| `getAssessmentMarks()` | Get assessment marks |

#### `AdminSubjectController`
| Function | Description |
|----------|-------------|
| `index()` | List subjects |
| `showByClass()` | Show by class |
| `create()` | Create subject form |
| `store()` | Store subject |
| `assignForm()` | Assign subject form |
| `assign()` | Assign subject |
| `unassign()` | Unassign subject |
| `bulkUnassign()` | Bulk unassign |
| `edit()` | Edit subject form |
| `update()` | Update subject |
| `destroy()` | Delete subject |

#### `RolePermissionController`
| Function | Description |
|----------|-------------|
| `roles()` | List roles |
| `createRole()` | Create role form |
| `storeRole()` | Store role |
| `editRole()` | Edit role form |
| `updateRole()` | Update role |
| `createPermission()` | Create permission form |
| `storePermission()` | Store permission |
| `editPermission()` | Edit permission form |
| `updatePermission()` | Update permission |
| `manageSidebarPermissions()` | Manage sidebar permissions |
| `updateUserSidebarPermissions()` | Update user sidebar permissions |

#### `RoleAssign`
| Function | Description |
|----------|-------------|
| `index()` | Role assignment list |
| `create()` | Create assignment form |
| `store()` | Store assignment |
| `edit()` | Edit assignment form |
| `update()` | Update assignment |
| `destroy()` | Delete assignment |

#### `AuditTrailController`
| Function | Description |
|----------|-------------|
| `index()` | View audit trail |
| `show()` | View audit entry |
| `export()` | Export audit trail |
| `clear()` | Clear audit trail |

#### `SchoolSettingsController`
| Function | Description |
|----------|-------------|
| `classFormats()` | Class format settings |
| `storeClassFormat()` | Store class format |
| `updateClassFormat()` | Update class format |
| `deleteClassFormat()` | Delete class format |
| `upgradeDirection()` | Upgrade direction settings |
| `updateUpgradeDirection()` | Update upgrade direction |
| `getUpgradePreview()` | Preview upgrade |
| `receiptSettings()` | Receipt settings |
| `updateReceiptSettings()` | Update receipt settings |

#### `SchoolGeolocationController`
| Function | Description |
|----------|-------------|
| `index()` | Geolocation settings |
| `store()` | Store geolocation |
| `update()` | Update geolocation |
| `destroy()` | Delete geolocation |
| `setActive()` | Set active location |
| `getActive()` | Get active location |
| `checkPoint()` | Check if point is within bounds |

#### `NonTeachingStaffController`
| Function | Description |
|----------|-------------|
| `index()` | List non-teaching staff |
| `create()` | Create staff form |
| `store()` | Store staff |
| `show()` | View staff |
| `edit()` | Edit staff form |
| `update()` | Update staff |
| `destroy()` | Delete staff |

#### `Admin\ScholarshipController`
| Function | Description |
|----------|-------------|
| `index()` | List scholarships |
| `update()` | Update scholarship |
| `bulkUpdate()` | Bulk update scholarships |

---

### Communication Controllers

#### `SchoolNotificationController`
| Function | Description |
|----------|-------------|
| `adminIndex()` | Admin notifications |
| `create()` | Create notification form |
| `store()` | Send notification |
| `show()` | View notification |
| `destroy()` | Delete notification |
| `inbox()` | User inbox |
| `markAsRead()` | Mark as read |
| `sendSms()` | Send SMS notification |

#### `NewsletterController`
| Function | Description |
|----------|-------------|
| `index()` | List newsletters |
| `showNewsletters()` | Show newsletters |
| `show()` | View newsletter |
| `show1()` | Alternate show |
| `create()` | Create newsletter form |
| `store()` | Store newsletter |
| `edit()` | Edit newsletter form |
| `update()` | Update newsletter |
| `destroy()` | Delete newsletter |

#### `SmsSettingsController`
| Function | Description |
|----------|-------------|
| `index()` | SMS settings |
| `resetCount()` | Reset SMS count |
| `update()` | Update settings |
| `preview()` | Preview SMS |

#### `SmsTestController`
| Function | Description |
|----------|-------------|
| `index()` | SMS test page |
| `send()` | Send test SMS |

#### `PushNotificationController`
| Function | Description |
|----------|-------------|
| `subscribe()` | Subscribe to push notifications |
| `unsubscribe()` | Unsubscribe |
| `getVapidPublicKey()` | Get VAPID key |

---

### Assets & Inventory Controllers

#### `AssetController`
| Function | Description |
|----------|-------------|
| `index()` | List assets |
| `create()` | Create asset form |
| `store()` | Store asset |
| `show()` | View asset |
| `edit()` | Edit asset form |
| `update()` | Update asset |
| `destroy()` | Delete asset |
| `assign()` | Assign asset |
| `unassign()` | Unassign asset |
| `showAssignForm()` | Show assign form |
| `showDisposeForm()` | Show dispose form |
| `dispose()` | Dispose asset |
| `categories()` | Asset categories |
| `createCategory()` | Create category form |
| `storeCategory()` | Store category |
| `editCategory()` | Edit category form |
| `updateCategory()` | Update category |
| `locations()` | Asset locations |
| `createLocation()` | Create location form |
| `storeLocation()` | Store location |
| `editLocation()` | Edit location form |
| `updateLocation()` | Update location |
| `maintenance()` | Maintenance records |
| `createMaintenance()` | Create maintenance form |
| `storeMaintenance()` | Store maintenance |
| `completeMaintenance()` | Complete maintenance |
| `depreciation()` | Depreciation view |
| `runDepreciation()` | Run depreciation |
| `postDepreciation()` | Post depreciation |
| `postAllDepreciation()` | Post all depreciation |
| `reports()` | Asset reports |
| `assetRegister()` | Asset register report |
| `depreciationScheduleReport()` | Depreciation schedule |
| `maintenanceCostReport()` | Maintenance cost report |
| `disposedAssetsReport()` | Disposed assets report |
| `assetsByLocation()` | Assets by location |

#### `ProductController`
| Function | Description |
|----------|-------------|
| `index()` | List products |
| `create()` | Create product form |
| `store()` | Store product |
| `show()` | View product |
| `edit()` | Edit product form |
| `update()` | Update product |
| `adjustStock()` | Adjust stock |
| `pos()` | Point of sale |
| `findByBarcode()` | Find by barcode |
| `processSale()` | Process sale |
| `salesHistory()` | Sales history |
| `saleReceipt()` | Sale receipt |
| `inventory()` | Inventory view |
| `stockMovements()` | Stock movements |
| `categories()` | Product categories |
| `storeCategory()` | Store category |
| `deleteCategory()` | Delete category |

#### `GroceryController`
| Function | Description |
|----------|-------------|
| `index()` | Grocery lists |
| `store()` | Store grocery list |
| `showClass()` | Show class list |
| `viewResponse()` | View response |
| `acknowledge()` | Acknowledge response |
| `updateStudentGrocery()` | Update student grocery |
| `parentIndex()` | Parent grocery view |
| `parentSubmit()` | Parent submit |
| `close()` | Close list |
| `edit()` | Edit list |
| `update()` | Update list |
| `lock()` | Lock list |
| `studentHistory()` | Student history |
| `destroy()` | Delete list |
| `blockSettings()` | Block settings |
| `updateBlockSettings()` | Update block settings |
| `toggleExemption()` | Toggle exemption |
| `groceryArrears()` | Grocery arrears |
| `exportGroceryArrears()` | Export arrears |
| `printStudentHistory()` | Print history |

#### `GroceryStockController`
| Function | Description |
|----------|-------------|
| `index()` | Stock dashboard |
| `items()` | Stock items |
| `storeItem()` | Store item |
| `updateItem()` | Update item |
| `transactions()` | Stock transactions |
| `storeTransaction()` | Store transaction |
| `recordUsage()` | Record usage form |
| `storeUsage()` | Store usage |
| `recordBadStock()` | Record bad stock form |
| `storeBadStock()` | Store bad stock |
| `carryForward()` | Carry forward |
| `print()` | Print stock report |

---

### Library Controllers

#### `LibraryController`
| Function | Description |
|----------|-------------|
| `index()` | Library dashboard |
| `create()` | Create borrow form |
| `searchStudents()` | Search students |
| `searchTeachers()` | Search teachers |
| `store()` | Store borrow record |
| `show()` | View record |
| `returnBook()` | Return book |
| `destroy()` | Delete record |
| `studentHistory()` | Student borrow history |
| `teacherHistory()` | Teacher borrow history |
| `myTeacherLibrary()` | Teacher's library view |
| `books()` | List books |
| `createBook()` | Create book form |
| `storeBook()` | Store book |
| `editBook()` | Edit book form |
| `updateBook()` | Update book |
| `bookHistory()` | Book history |
| `destroyBook()` | Delete book |
| `searchBooks()` | Search books |
| `myLibrary()` | Student's library view |

---

### Website Controllers

#### `websiteController`
| Function | Description |
|----------|-------------|
| `index()` | Homepage |
| `about()` | About page |
| `contact()` | Contact page |
| `courses()` | Courses page |
| `news()` | News page |
| `results()` | Results page |
| `success()` | Success page |

#### `WebsiteSettingController`
| Function | Description |
|----------|-------------|
| `index()` | Settings dashboard |
| `general()` | General settings |
| `colors()` | Color settings |
| `images()` | Image settings |
| `text()` | Text settings |
| `pages()` | Page settings |
| `homepage()` | Homepage settings |
| `update()` | Update settings |
| `updateSingle()` | Update single setting |
| `reset()` | Reset settings |
| `banners()` | Banner settings |
| `updateBanners()` | Update banners |

#### `BannerController`
| Function | Description |
|----------|-------------|
| `index()` | List banners |
| `store()` | Store banner |

#### `EventController`
| Function | Description |
|----------|-------------|
| `index()` | List events |
| `create()` | Create event form |
| `store()` | Store event |
| `edit()` | Edit event form |
| `update()` | Update event |
| `destroy()` | Delete event |

#### `ApplicationController`
| Function | Description |
|----------|-------------|
| `index()` | Application form |
| `store()` | Submit application |
| `success()` | Application success |

---

## Models

### Core Models

| Model | Key Functions |
|-------|---------------|
| `User` | `teacher()`, `student()`, `parent()` |
| `Student` | `user()`, `parent()`, `class()`, `attendances()`, `payments()`, `subjects()` |
| `Teacher` | `user()`, `subjects()`, `classes()`, `students()`, `attendances()`, `devices()`, `activeDevice()`, `hasRegisteredDevice()`, `todayAttendance()`, `isCheckedInToday()` |
| `Parents` | `user()`, `children()`, `students()` |
| `Grade` | `students()`, `subjects()`, `teacher()`, `assessments()` |
| `Subject` | `teacher()`, `grades()`, `results()`, `readings()` |

### Academic Models

| Model | Key Functions |
|-------|---------------|
| `Assessment` | `teacher()`, `class()`, `subject()`, `marks()`, `syllabusTopic()`, `exercise()` |
| `AssessmentMark` | `assessment()`, `student()`, `approvedBy()` |
| `Result` | `subject()`, `teacher()`, `student()`, `class()`, `approvedBy()` |
| `Exercise` | `teacher()`, `class()`, `subject()`, `questions()`, `submissions()`, `getSubmissionForStudent()`, `isOverdue()`, `getStatusBadgeAttribute()` |
| `ExerciseQuestion` | `exercise()`, `options()`, `answers()`, `isAutoMarkable()` |
| `ExerciseQuestionOption` | `question()` |
| `ExerciseSubmission` | `exercise()`, `student()`, `answers()`, `calculateAutoMarks()`, `getPercentageScore()` |
| `ExerciseAnswer` | `submission()`, `question()`, `selectedOption()` |
| `SchemeOfWork` | `teacher()`, `subject()`, `class()`, `schemeTopics()`, `getProgressPercentageAttribute()`, `recalculateTotals()` |
| `SchemeTopic` | `scheme()`, `syllabusTopic()`, `remedialLessons()`, `updateMasteryLevel()` |
| `SyllabusTopic` | `subject()`, `assessments()`, `schemeTopics()`, `performanceSnapshots()`, `remedialLessons()` |
| `RemedialLesson` | `syllabusTopic()`, `class()`, `teacher()`, `students()`, `markCompleted()` |

### Finance Models

| Model | Key Functions |
|-------|---------------|
| `LedgerAccount` | `entries()`, `updateBalance()` |
| `LedgerEntry` | `account()`, `cashBookEntry()`, `isDebit()`, `isCredit()` |
| `CashBookEntry` | `payroll()`, `creator()`, `ledgerEntries()`, `isReceipt()`, `isPayment()`, `postToLedger()` |
| `JournalBatch` | `entries()`, `creator()`, `approver()`, `isBalanced()`, `approve()`, `post()`, `canEdit()`, `canApprove()`, `canPost()` |
| `JournalEntry` | `batch()`, `ledgerAccount()`, `getEntryType()`, `getAmount()` |
| `BudgetPeriod` | `budgetItems()`, `revenueForecasts()`, `expenseForecasts()`, `getTotalBudgetedIncomeAttribute()` |
| `BudgetItem` | `budgetPeriod()`, `updateVariance()`, `getVariancePercentageAttribute()` |
| `Expense` | `category()`, `approver()`, `creator()` |
| `ExpenseCategory` | `parent()`, `children()`, `expenses()` |
| `Payroll` | `user()`, `salary()`, `approver()`, `payer()`, `cashBookEntry()`, `ledgerEntries()`, `isPending()`, `isApproved()`, `isPaid()` |
| `EmployeeSalary` | `user()`, `payrolls()`, `getTotalAllowancesAttribute()`, `getTotalDeductionsAttribute()`, `getNetSalaryAttribute()` |
| `PurchaseOrder` | `expense()`, `supplier()`, `items()`, `creator()`, `approver()`, `calculateTotals()` |
| `Supplier` | `purchaseOrders()`, `invoices()`, `payments()`, `getOutstandingBalance()` |
| `SupplierInvoice` | `supplier()`, `payments()`, `postToLedger()`, `getOutstandingAmount()`, `isOverdue()` |
| `SupplierPayment` | `invoice()`, `creator()`, `postToLedger()` |
| `StudentAccount` | `student()`, `invoices()`, `updateBalance()`, `getOutstandingBalance()`, `isInArrears()`, `getAgingBreakdown()` |
| `StudentInvoice` | `student()`, `items()`, `postToLedger()`, `recordPayment()`, `getOutstandingAmount()`, `isOverdue()` |
| `StudentPayment` | `student()`, `resultsStatus()`, `termFee()`, `feeStructure()` |
| `Payment` | `student()`, `feeCategory()` |
| `BankAccount` | `transactions()`, `updateBalance()` |
| `BankTransaction` | `bankAccount()`, `cashBookEntry()`, `reconciledByUser()` |

### Asset Models

| Model | Key Functions |
|-------|---------------|
| `Asset` | `category()`, `location()`, `purchaseOrder()`, `assignment()`, `maintenances()`, `depreciations()`, `getAgeInYearsAttribute()`, `getConditionBadgeAttribute()`, `isDisposed()`, `calculateAnnualDepreciation()` |
| `AssetCategory` | `assets()`, `getActiveAssetsCountAttribute()`, `getTotalValueAttribute()` |
| `AssetLocation` | `assets()`, `getFullNameAttribute()` |
| `AssetMaintenance` | `asset()`, `performer()`, `approver()`, `isPending()`, `isCompleted()`, `complete()` |
| `AssetDepreciation` | `asset()`, `ledgerEntry()`, `isPosted()`, `canBeModified()` |
| `AssetAssignmentHistory` | `asset()`, `assigner()`, `getActionDescriptionAttribute()` |

### Inventory Models

| Model | Key Functions |
|-------|---------------|
| `Product` | `creator()`, `saleItems()`, `stockMovements()`, `getTotalRevenueAttribute()`, `isLowStock()` |
| `ProductCategory` | `products()` |
| `ProductSale` | `items()`, `seller()` |
| `ProductSaleItem` | `sale()`, `product()` |
| `StockMovement` | `product()`, `creator()` |
| `GroceryStockItem` | `transactions()`, `updateBalance()` |
| `GroceryStockTransaction` | `stockItem()`, `recordedBy()`, `getTypeLabel()` |

### Other Models

| Model | Key Functions |
|-------|---------------|
| `Attendance` | `student()`, `teacher()`, `class()` |
| `TeacherAttendance` | `teacher()`, `isCheckedIn()`, `isComplete()`, `getDurationAttribute()` |
| `TeacherDevice` | `teacher()`, `isActive()`, `isPending()`, `isRevoked()`, `activate()`, `revoke()` |
| `LeaveApplication` | `teacher()`, `approver()`, `isPending()`, `isApproved()`, `isRejected()` |
| `Book` | `addedBy()`, `libraryRecords()`, `activeBorrows()`, `isAvailable()` |
| `LibraryRecord` | `student()`, `teacher()`, `issuedBy()`, `book()`, `getBorrowerAttribute()` |
| `DisciplinaryRecord` | `student()`, `class()`, `teacher()` |
| `MedicalReport` | `parent()`, `student()`, `acknowledgedBy()`, `getStatusBadgeAttribute()` |
| `SchoolNotification` | `sender()`, `class()`, `recipient()`, `reads()` |
| `AuditTrail` | `user()`, `getActionColorAttribute()`, `getActionIconAttribute()` |
| `Timetable` | `grade()`, `subject()`, `teacher()` |

---

## Services

### `LedgerPostingService`
| Function | Description |
|----------|-------------|
| `postTransaction()` | Post transaction to ledger |
| `reverseTransaction()` | Reverse a posted transaction |
| `isBalanced()` | Check if entries are balanced |
| `getTrialBalance()` | Get trial balance |

### `AssetManagementService`
| Function | Description |
|----------|-------------|
| `createAssetFromPurchaseOrder()` | Create asset from PO |
| `createAsset()` | Create new asset |
| `postAssetPurchaseToLedger()` | Post purchase to ledger |
| `calculateDepreciation()` | Calculate depreciation |
| `postDepreciationToLedger()` | Post depreciation to ledger |
| `runAnnualDepreciation()` | Run annual depreciation |
| `assignAsset()` | Assign asset |
| `unassignAsset()` | Unassign asset |
| `disposeAsset()` | Dispose asset |
| `createMaintenance()` | Create maintenance record |
| `completeMaintenance()` | Complete maintenance |
| `getAssetValuationSummary()` | Get valuation summary |
| `getDepreciationSchedule()` | Get depreciation schedule |

### `TopicPerformanceService`
| Function | Description |
|----------|-------------|
| `calculateTopicPerformance()` | Calculate topic performance |
| `determineMasteryLevel()` | Determine mastery level |
| `createPerformanceSnapshot()` | Create performance snapshot |
| `updateSchemeTopicPerformance()` | Update scheme topic performance |
| `getTopicHistoricalPerformance()` | Get historical performance |
| `getSuggestedPeriods()` | Get suggested periods |
| `getClassTopicHeatmap()` | Get class topic heatmap |
| `getMasteryColor()` | Get mastery color |
| `checkAndTriggerRemedial()` | Check and trigger remedial |
| `recalculateSchemePerformance()` | Recalculate performance |
| `getWeakTopicsForTeacher()` | Get weak topics |
| `compareTopicAcrossClasses()` | Compare topic across classes |

---

## Middleware

| Middleware | Function | Description |
|------------|----------|-------------|
| `CheckDefaultPassword` | `handle()` | Check if using default password |
| `CheckPasswordChange` | `handle()` | Check password change requirement |
| `CheckPasswordChanged` | `handle()` | Verify password was changed |
| `LoginRateLimiter` | `handle()` | Rate limit login attempts |
| `RedirectIfAuthenticated` | `handle()` | Redirect if already logged in |
| `ValidateTeacherDevice` | `handle()` | Validate teacher device |

---

## Traits

### `Auditable`
| Function | Description |
|----------|-------------|
| `bootAuditable()` | Boot auditable trait (auto-logs create/update/delete) |
| `audit()` | Create audit trail entry (skips super admin) |

---

## Jobs & Listeners

### Jobs
| Job | Function | Description |
|-----|----------|-------------|
| `SendParentSms` | `handle()` | Send SMS to parent |

### Listeners
| Listener | Function | Description |
|----------|----------|-------------|
| `LogSuccessfulLogin` | `handle()` | Log successful login |
| `LogSuccessfulLogout` | `handle()` | Log successful logout |

---

## Policies

### `AssetPolicy`
| Function | Description |
|----------|-------------|
| `before()` | Check admin override |
| `viewAny()` | Can view any assets |
| `view()` | Can view asset |
| `create()` | Can create asset |
| `update()` | Can update asset |
| `delete()` | Can delete asset |
| `assign()` | Can assign asset |
| `dispose()` | Can dispose asset |
| `calculateDepreciation()` | Can calculate depreciation |
| `postDepreciation()` | Can post depreciation |
| `viewReports()` | Can view reports |
| `exportReports()` | Can export reports |

---

## Exports

### `MarkingSchemeExport`
| Function | Description |
|----------|-------------|
| `collection()` | Get export collection |
| `headings()` | Get column headings |
| `styles()` | Get cell styles |
| `title()` | Get sheet title |

---

*Last Updated: February 2026*
*Generated for ROSHS Student Portal*
