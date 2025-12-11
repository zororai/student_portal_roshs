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
    Route::resource('subject', 'SubjectController');
    Route::resource('teacher', 'TeacherController');
    Route::resource('parents', 'ParentsController');
    Route::resource('student', 'StudentController');

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
     //nweb
    Route::get('/webcam', 'WebcamController@index')->name('Webcam.index');
    Route::post('/webcam/upload', 'WebcamController@upload')->name('webcam.upload');
    //news letters
    Route::resource('newsletters', 'NewsletterController')->except(['edit', 'update']);
    Route::post('newsletters/{id}/publish', 'NewsletterController@publish')->name('newsletters.publish');
    //studentid
    Route::get('/student/{id}/id-card', 'StudentController@showid')->name('student.id_card');
    Route::get('/student/{id}/id-card/download', 'StudentController@downloadIdCard')->name('student.download_id_card');

});

Route::group(['middleware' => ['auth','role:Teacher']], function ()
{
    Route::post('attendance', 'AttendanceController@store')->name('teacher.attendance.store');
    Route::get('attendance-create/{classid}', 'AttendanceController@createByTeacher')->name('teacher.attendance.create');

    Route::get('readingmaterails/{id}','AddsubjectController@show')->name('subject.Reading');

    Route::resource('readings', AddsubjectController::class);
    Route::post('subjects/{id}/upload','AddsubjectController@upload')->name('subject.upload');

    // Student Record Routes
    Route::get('/teacher/student-record', 'TeacherController@studentRecord')->name('teacher.studentrecord');
    Route::get('/teacher/student-record/{class_id}', 'TeacherController@classStudents')->name('teacher.class.students');

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



});

Route::group(['middleware' => ['auth','role:Parent']], function ()
{
    Route::get('studentattendance/{attendance}', 'AttendanceController@show')->name('attendance.show');
    Route::get('/viewstudentresults/viewstudentresults', 'ResultController@viewstudentshow')->name('parentviewresults.studentresults');
    Route::get('/studentviewresults/studentviewresults', 'AddsubjectController@studentviewsubject')->name('viewsubject.studentresults');

});

Route::group(['middleware' => ['auth','role:Student']], function () {

    Route::get('/studentviewresults/studentviewresults', 'AddsubjectController@studentviewsubject')->name('viewsubject.studentresults');
    Route::get('/studentattends/studentattends', 'AddsubjectController@studentattendance')->name('attendancy.studentattendance');
   Route::get('/studentresults/studentresults', 'ResultController@studentshow')->name('viewresults.studentresults');
   Route::get('Reading/{id}','AddsubjectController@showread')->name('subject.viewreading');
   Route::get('/readings/download/{id}', 'AddsubjectController@download')->name('readings.download');


});
