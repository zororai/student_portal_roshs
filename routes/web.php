<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/web');
});


Route::get('/logins','UserController@login')->name('login');

Auth::routes();

// Add GET logout route to avoid 419 error
Route::get('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

// Parent self-registration routes (public access, no auth required)
Route::get('/parent/register/{token}', 'ParentsController@showRegistrationForm')->name('parent.register.form');
Route::post('/parent/register/{token}', 'ParentsController@completeRegistration')->name('parent.register.complete');

Route::get('/web', 'websiteController@index')->name('website.index');
Route::get('/about', 'websiteController@about')->name('website.about');
Route::get('/contact', 'websiteController@contact')->name('website.contact');
Route::get('/courses', 'websiteController@courses')->name('website.courses');
Route::get('/news', 'websiteController@news')->name('website.News');
Route::get('/result', 'websiteController@results')->name('website.results');
Route::get('/success', 'websiteController@success')->name('website.success');

// Student Application Routes
Route::get('/application', 'ApplicationController@index')->name('website.application');
Route::post('/application', 'ApplicationController@store')->name('website.application.store');
Route::get('/application/success', 'ApplicationController@success')->name('website.application.success');

Route::get('/newsletter', 'NewsletterController@showNewsletters')->name('website.News');
//Route::get('/newslettersshow/{id}','NewsletterController@show')->name('website.newsletter.show');
Route::get('/newslettersshow/{id}','NewsletterController@show')->name('website.newsletter.show');

Route::get('/image/{filename}', 'ImageController@showImage')->name('image.show');






Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/profile/edit', 'HomeController@profileEdit')->name('profile.edit');
Route::put('/profile/update', 'HomeController@profileUpdate')->name('profile.update');
Route::get('/profile/changepassword', 'HomeController@changePasswordForm')->name('profile.change.password');
Route::post('/profile/changepassword', 'HomeController@changePassword')->name('profile.changepassword');

// Student password change routes
Route::get('/student/change-password', 'StudentController@showChangePasswordForm')->name('student.change-password');
Route::post('/student/update-password', 'StudentController@updatePassword')->name('student.update-password');



Route::group(['middleware' => ['auth','role:Admin']], function ()
{
    Route::get('/roles-permissions', 'RolePermissionController@roles')->name('roles-permissions');
    Route::get('/role-create', 'RolePermissionController@createRole')->name('role.create');
    Route::post('/role-store', 'RolePermissionController@storeRole')->name('role.store');
    Route::get('/role-edit/{id}', 'RolePermissionController@editRole')->name('role.edit');
    Route::put('/role-update/{id}', 'RolePermissionController@updateRole')->name('role.update');
    Route::get('/permission-create', 'RolePermissionController@createPermission')->name('permission.create');
    Route::post('/permission-store', 'RolePermissionController@storePermission')->name('permission.store');
    Route::get('/permission-edit/{id}', 'RolePermissionController@editPermission')->name('permission.edit');
    Route::put('/permission-update/{id}', 'RolePermissionController@updatePermission')->name('permission.update');
    Route::get('assign-subject-to-class/{id}', 'GradeController@assignSubject')->name('class.assign.subject');
    Route::post('assign-subject-to-class/{id}', 'GradeController@storeAssignedSubject')->name('store.class.assign.subject');
    Route::resource('assignrole', 'RoleAssign');
    Route::resource('classes', 'GradeController');
    /////
    // Show the post and view student results

    Route::post('/adminshowclassresults/year', 'ResultController@adminshowResult')->name('adminresults.classresults');
     Route::get('/classnameadmin/{class_id}','ResultController@adminclassnames')->name('adminresults.classname');
    Route::get('/studentadmin/show/{student}', 'ResultController@adminStuntentname')->name('adminresults.yearsubject');


    // Store the updated results status
    Route::get('manageresults', 'GradeController@adminindex')->name('manageresults.index');
    ////
    // Admin Subject Management Routes
    Route::get('admin/subjects', 'AdminSubjectController@index')->name('admin.subjects.index');
    Route::get('admin/subjects/create', 'AdminSubjectController@create')->name('admin.subjects.create');
    Route::post('admin/subjects', 'AdminSubjectController@store')->name('admin.subjects.store');
    Route::get('admin/subjects/{subject}/edit', 'AdminSubjectController@edit')->name('admin.subjects.edit');
    Route::put('admin/subjects/{subject}', 'AdminSubjectController@update')->name('admin.subjects.update');
    Route::delete('admin/subjects/{subject}', 'AdminSubjectController@destroy')->name('admin.subjects.destroy');
    
    Route::resource('subject', 'SubjectController');
    Route::resource('teacher', 'TeacherController')->except(['show']);
    Route::resource('parents', 'ParentsController');
    Route::resource('student', 'StudentController');

    // Admin Applicants Routes
    Route::get('admin/applicants', 'AdminApplicantController@index')->name('admin.applicants.index');
    Route::get('admin/applicants/{id}', 'AdminApplicantController@show')->name('admin.applicants.show');
    Route::patch('admin/applicants/{id}/status', 'AdminApplicantController@updateStatus')->name('admin.applicants.updateStatus');
    Route::delete('admin/applicants/{id}', 'AdminApplicantController@destroy')->name('admin.applicants.destroy');

    // School Staff Routes
    Route::get('admin/staff', 'AdminStaffController@index')->name('admin.staff.index');
    Route::get('admin/staff/create', 'AdminStaffController@create')->name('admin.staff.create');
    Route::post('admin/staff', 'AdminStaffController@store')->name('admin.staff.store');
    Route::get('admin/staff/{id}', 'AdminStaffController@show')->name('admin.staff.show');
    Route::get('admin/staff/{id}/edit', 'AdminStaffController@edit')->name('admin.staff.edit');
    Route::put('admin/staff/{id}', 'AdminStaffController@update')->name('admin.staff.update');
    Route::delete('admin/staff/{id}', 'AdminStaffController@destroy')->name('admin.staff.destroy');

    // Log Book Routes (commented out - controller not yet created)
    // Route::get('admin/logbook', 'AdminLogBookController@index')->name('admin.logbook.index');
    // Route::get('admin/logbook/create', 'AdminLogBookController@create')->name('admin.logbook.create');
    // Route::post('admin/logbook', 'AdminLogBookController@store')->name('admin.logbook.store');
    // Route::get('admin/logbook/{id}', 'AdminLogBookController@show')->name('admin.logbook.show');
    // Route::delete('admin/logbook/{id}', 'AdminLogBookController@destroy')->name('admin.logbook.destroy');

    // Timetable Routes
    Route::get('admin/timetable', 'AdminTimetableController@index')->name('admin.timetable.index');
    Route::get('admin/timetable/create', 'AdminTimetableController@create')->name('admin.timetable.create');
    Route::post('admin/timetable', 'AdminTimetableController@store')->name('admin.timetable.store');
    Route::get('admin/timetable/{id}', 'AdminTimetableController@show')->name('admin.timetable.show');
    Route::get('admin/timetable/{id}/edit', 'AdminTimetableController@edit')->name('admin.timetable.edit');
    Route::put('admin/timetable/{id}', 'AdminTimetableController@update')->name('admin.timetable.update');
    Route::delete('admin/timetable/{id}', 'AdminTimetableController@destroy')->name('admin.timetable.destroy');

    // Stepper route for creating student with parents
    Route::get('student-with-parents/create', 'StudentController@createWithParents')->name('student.create-with-parents');
    Route::post('student-with-parents', 'StudentController@storeWithParents')->name('student.store-with-parents');

    Route::get('attendance', 'AttendanceController@index')->name('attendance.index');
    ///banner
    Route::get('/banner', 'BannerController@index')->name('banner.index');
    Route::post('/banner', 'BannerController@store')->name('banner.store');
    ///Newsletter
    Route::get('newsletters', 'NewsletterController@index')->name('newsletters.index');
    Route::get('newsletters/create', 'NewsletterController@create')->name('newsletters.create');
    Route::post('newsletters', 'NewsletterController@store')->name('newsletters.store');
    Route::get('newsletters/{newsletter}', 'NewsletterController@show')->name('newsletters.show');
    Route::get('newsletters/{newsletter}/edit', 'NewsletterController@edit')->name('newsletters.edit');
    Route::put('newsletters/{newsletter}', 'NewsletterController@update')->name('newsletters.update');
    Route::delete('newsletters/{newsletter}', 'NewsletterController@destroy')->name('newsletters.destroy');
    // Routes for ResultsStatus management
    Route::get('/Term/create', 'ResultsStatusController@create')->name('results_status.create');
    Route::post('/Term', 'ResultsStatusController@store')->name('results_status.store');
    Route::get('/Term/{id}/edit', 'ResultsStatusController@edit')->name('results_status.edit');
    Route::put('/Term/{id}', 'ResultsStatusController@update')->name('results_status.update');
    Route::get('/Term', 'ResultsStatusController@index')->name('results_status.index');
    Route::delete('/results-status/{id}', 'ResultsStatusController@destroy')->name('results_status.destroy');
    ///resultactive
    Route::get('/activeresults', 'ResultController@resultsactive')->name('activeresults.index');
    Route::get('/viewstudent/{student}', 'ResultController@changestatus')->name('viewstudentstatus.results');
    Route::get('/Admin/results/{class_id}','ResultController@activelistResults')->name('active.results');
    Route::get('/viewstudent/viewresults/{student}', 'ResultController@viewupdateresults')->name('viewstudent.results');
    //fees import
    Route::post('/students/import', 'StudentController@import')->name('students.import');
    //fees paymeny
    Route::get('payments/create/{studentId}', 'PaymentController@create')->name('payments.create');
    Route::post('payments', 'PaymentController@store')->name('payments.store');
    Route::get('payments', 'PaymentController@index')->name('payments.index');
    Route::get('payments/receipt/{id}', 'PaymentController@receipt')->name('payments.receipt');
    Route::resource('fee_categories', 'FeeCategoryController');
    Route::get('payments/receipt/download/{id}', 'PaymentController@downloadReceipt')->name('payments.receipt.download');
    Route::get('/reports', 'ReportController@index')->name('reports.index');
     //webcam and student ID
    Route::get('/webcam', 'WebcamController@index')->name('Webcam.index');
    Route::post('/webcam/upload', 'WebcamController@upload')->name('webcam.upload');
    Route::get('/webcam/students/{classId}', 'WebcamController@getStudentsByClass')->name('webcam.students');
    Route::get('/webcam/student/{id}', 'WebcamController@getStudent')->name('webcam.student');
    Route::post('/webcam/capture', 'WebcamController@capturePhoto')->name('webcam.capture');
    Route::get('/webcam/id-card/{studentId}', 'WebcamController@generateIdCard')->name('webcam.idcard');
    //news letters
    Route::resource('newsletters', 'NewsletterController')->except(['edit', 'update']);
    Route::post('newsletters/{id}/publish', 'NewsletterController@publish')->name('newsletters.publish');
    //studentid
    Route::get('/student/{id}/id-card', 'StudentController@showid')->name('student.id_card');
    Route::get('/student/{id}/id-card/download', 'StudentController@downloadIdCard')->name('student.download_id_card');

    // Finance & Accounting Routes
    Route::get('/finance/student-payments', 'FinanceController@studentPayments')->name('finance.student-payments');
    Route::post('/finance/payments/store', 'FinanceController@storePayment')->name('finance.payments.store');
    Route::get('/finance/parents-arrears', 'FinanceController@parentsArrears')->name('finance.parents-arrears');
    Route::get('/finance/parents-arrears/export', 'FinanceController@exportParentsArrears')->name('finance.parents-arrears.export');
    Route::get('/finance/student-payments/export', 'FinanceController@exportStudentPayments')->name('finance.student-payments.export');
    Route::get('/finance/school-income', 'FinanceController@schoolIncome')->name('finance.school-income');
    Route::get('/finance/school-expenses', 'FinanceController@schoolExpenses')->name('finance.school-expenses');
    Route::get('/finance/products', 'FinanceController@products')->name('finance.products');
    Route::get('/finance/statements', 'FinanceController@financialStatements')->name('finance.statements');

    // Admin View Results Routes
    Route::get('/admin/view-results', 'ResultController@adminViewResults')->name('admin.view-results');
    Route::post('/admin/get-results', 'ResultController@getAdminResults')->name('admin.get-results');

    // Disciplinary Records Routes (Admin)
    Route::get('/admin/disciplinary-records', 'DisciplinaryController@index')->name('admin.disciplinary.index');
    Route::post('/admin/disciplinary-records', 'DisciplinaryController@store')->name('admin.disciplinary.store');
    Route::get('/admin/disciplinary-records/class/{class_id}/students', 'DisciplinaryController@getStudentsByClass')->name('admin.disciplinary.students');
    Route::put('/admin/disciplinary-records/{id}', 'DisciplinaryController@update')->name('admin.disciplinary.update');
    Route::delete('/admin/disciplinary-records/{id}', 'DisciplinaryController@destroy')->name('admin.disciplinary.destroy');

    // Admin Marking Scheme Routes
    Route::get('/admin/marking-scheme', 'AdminMarkingSchemeController@index')->name('admin.marking-scheme.index');
    Route::get('/admin/marking-scheme/class/{class_id}', 'AdminMarkingSchemeController@classAssessments')->name('admin.marking-scheme.assessments');
    Route::get('/admin/marking-scheme/assessment/{assessment_id}', 'AdminMarkingSchemeController@assessmentMarks')->name('admin.marking-scheme.marks');
    Route::get('/api/admin/assessment/{assessment_id}/marks', 'AdminMarkingSchemeController@getAssessmentMarks')->name('admin.marking-scheme.api.marks');

});

Route::group(['middleware' => ['auth','role:Teacher']], function ()
{
    // Student Record Routes - must be defined early to avoid conflicts
    Route::get('/teacher/student-record', 'TeacherController@studentRecord')->name('teacher.studentrecord');
    Route::get('/teacher/student-record/{class_id}', 'TeacherController@classStudents')->name('teacher.class.students');
    Route::post('/teacher/student/{student_id}/transfer', 'TeacherController@transferStudent')->name('teacher.student.transfer');

    // Disciplinary Records Routes
    Route::get('/teacher/disciplinary-records', 'DisciplinaryController@index')->name('teacher.disciplinary.index');
    Route::post('/teacher/disciplinary-records', 'DisciplinaryController@store')->name('teacher.disciplinary.store');
    Route::get('/teacher/disciplinary-records/class/{class_id}/students', 'DisciplinaryController@getStudentsByClass')->name('teacher.disciplinary.students');
    Route::put('/teacher/disciplinary-records/{id}', 'DisciplinaryController@update')->name('teacher.disciplinary.update');
    Route::delete('/teacher/disciplinary-records/{id}', 'DisciplinaryController@destroy')->name('teacher.disciplinary.destroy');

    Route::post('attendance', 'AttendanceController@store')->name('teacher.attendance.store');
    Route::get('attendance-create/{classid}', 'AttendanceController@createByTeacher')->name('teacher.attendance.create');

    // Subject routes for teachers
    Route::get('subject', 'SubjectController@index')->name('subject.index');
    Route::get('readingmaterails/{id}','AddsubjectController@show')->name('subject.Reading');

    Route::resource('readings', 'AddsubjectController');
    Route::post('subjects/{id}/upload','AddsubjectController@upload')->name('subject.upload');

    // Student Assessment
    Route::get('/teacher/assessment', 'TeacherController@assessment')->name('teacher.assessment');
    Route::get('/teacher/assessment/class/{class_id}', 'TeacherController@assessmentList')->name('teacher.assessment.list');
    Route::get('/teacher/assessment/create/{class_id}', 'TeacherController@createAssessment')->name('teacher.assessment.create');
    Route::post('/teacher/assessment/store', 'TeacherController@storeAssessment')->name('teacher.assessment.store');
    Route::get('/teacher/assessment/{id}/view', 'TeacherController@viewAssessment')->name('teacher.assessment.view');
    Route::get('/teacher/assessment/{id}/edit', 'TeacherController@editAssessment')->name('teacher.assessment.edit');
    Route::put('/teacher/assessment/{id}/update', 'TeacherController@updateAssessment')->name('teacher.assessment.update');
    Route::delete('/teacher/assessment/{id}/delete', 'TeacherController@deleteAssessment')->name('teacher.assessment.delete');
    Route::get('/teacher/assessment/marks/{class_id}', 'TeacherController@assessmentMarks')->name('teacher.assessment.marks');
    Route::post('/teacher/assessment/marks/save', 'TeacherController@saveAssessmentMark')->name('teacher.assessment.marks.save');
    Route::get('/teacher/assessment/marking-scheme/{class_id}', 'TeacherController@markingScheme')->name('teacher.assessment.marking.scheme');
    Route::get('/teacher/assessment/marking-scheme/{class_id}/export', 'TeacherController@exportMarkingScheme')->name('teacher.assessment.marking.export');
    Route::get('/api/assessment/{id}/marks', 'TeacherController@getAssessmentMarks');
    Route::post('/teacher/assessment/comment/store', 'TeacherController@storeAssessmentComment')->name('teacher.assessment.comment.store');
    Route::delete('/teacher/assessment/comment/{id}', 'TeacherController@deleteAssessmentComment')->name('teacher.assessment.comment.delete');

    Route::get('/results', 'ResultController@index')->name('results.index');
    Route::get('/viewresults', 'ResultController@recordindex')->name('results.record');
    Route::get('/selectyear', 'ResultController@viewresultsindex')->name('results.viewrecord');
    Route::get('/classnames', 'ResultController@viewresultsindex')->name('results.viewclassrecord');
    Route::post('postclassresultsview', 'ResultController@viewResults')->name('viewallresults.view');
    Route::get('/student/addresults/{student}', 'ResultController@Showssubject')->name('results.studentsubject');
    Route::post('/results/store', 'ResultController@store')->name('teacher.results.store');



    // Show the post and view student results

    Route::post('/showclassresults/year', 'ResultController@showResult')->name('results.classresults');
    Route::get('/classname/{class_id}','ResultController@Classnames')->name('results.classname');
    Route::get('/student/show/{student}', 'ResultController@Stuntentname')->name('results.yearsubject');
// Store the updated results status
    Route::get('/teacher/results/status/{status}', 'ResultController@viewstatus')->name('results.status');
   //studentresults
     Route::post('/results/update/{result}', 'ResultController@update')->name('results.update');
     Route::delete('/results/delete/{result}', 'ResultController@destroy')->name('results.delete');
     Route::get('/student/results/{student}', 'ResultController@showstudentresults')->name('student.results');
     Route::get('/teacher/results/create/{class_id}', 'ResultController@createByTeacher')->name('results.create');
     Route::get('/results/edit/{result}', 'ResultController@edit')->name('results.edit');
     Route::get('/results/student/{id}', 'ResultController@show')->name('results.show');
     Route::get('/teacher/results/{class_id}','ResultController@listResults')->name('results.results');
     Route::get('/teacher/timetable', 'TimetableController@teacherView')->name('teacher.timetable');

});

Route::group(['middleware' => ['auth','role:Parent']], function ()
{
    Route::get('studentattendance/{attendance}', 'AttendanceController@show')->name('attendance.show');
    Route::get('/viewstudentresults/viewstudentresults', 'ResultController@viewstudentshow')->name('parentviewresults.studentresults');
    Route::get('/studentviewresults/studentviewresults', 'AddsubjectController@studentviewsubject')->name('viewsubject.studentresults');
    Route::get('/parent/timetable', 'TimetableController@parentView')->name('parent.timetable');
});

Route::group(['middleware' => ['auth','role:Student']], function () {

    Route::get('/studentviewresults/studentviewresults', 'AddsubjectController@studentviewsubject')->name('viewsubject.studentresults');
    Route::get('/studentattends/studentattends', 'AddsubjectController@studentattendance')->name('attendancy.studentattendance');
   Route::get('/studentresults/studentresults', 'ResultController@studentshow')->name('viewresults.studentresults');
   Route::get('Reading/{id}','AddsubjectController@showread')->name('subject.viewreading');
   Route::get('/readings/download/{id}', 'AddsubjectController@download')->name('readings.download');
   Route::get('/student/timetable', 'TimetableController@studentView')->name('student.timetable');

});
