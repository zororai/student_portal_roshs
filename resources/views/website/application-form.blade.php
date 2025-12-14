<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Application Form - Rose Of Sharon High School</title>
    <link href="images/favicon.ico" rel="icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css')}}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ time() }}" />
    <style>
        .form-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .form-section h4 { color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; }
        .form-control { margin-bottom: 10px; }
        .btn-add-document { padding: 6px 15px; }
        .document-row { margin-bottom: 10px; }
    </style>
</head>
<body id="inner_page" data-spy="scroll" data-target="#navbar-wd" data-offset="98">
    <div id="preloader">
        <div class="loader"><img src="images/loader.gif" alt="#" /></div>
    </div>

    <header class="top-header">
        <nav class="navbar header-nav navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('website.index') }}">
                    <img style="height:80px; width:100px" src="images/logo.png" alt="image">
                    <b>Rose Of Sharon High School</b>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-wd">
                    <span></span><span></span><span></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbar-wd">
                    <ul class="navbar-nav">
                        <li><a class="nav-link" href="{{ route('website.index') }}">Home</a></li>
                        <li><a class="nav-link" href="{{ route('website.about') }}">About</a></li>
                        <li><a class="nav-link" href="{{ route('website.courses') }}">Our Subjects</a></li>
                        <li><a class="nav-link active" href="{{ route('website.application') }}">Application Form</a></li>
                        <li><a class="nav-link" href="{{ route('website.contact') }}">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="inner_banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="full"><h3>Student Application Form</h3></div>
                </div>
            </div>
        </div>
    </section>

    <br><br>

    <div class="site-section">
        <div class="container">
            @if(session('success'))
                <!-- Success Modal -->
                <div class="modal fade show" id="successModal" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body text-center py-5">
                                <div class="mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#28a745" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </div>
                                <h4 class="text-success mb-3">Application Submitted!</h4>
                                <p class="text-muted mb-4">{{ session('success') }}</p>
                                <button type="button" class="btn btn-success px-5" onclick="document.getElementById('successModal').style.display='none'">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('website.application.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- School Information -->
                <div class="form-section">
                    <h4>School You Wish to Apply For</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="school_applying_for">Select School</label>
                            <select name="school_applying_for" id="school_applying_for" class="form-control">
                                <option value="">Select School</option>
                                <option value="Rose of Sharon High School" {{ old('school_applying_for') == 'Rose of Sharon High School' ? 'selected' : '' }}>Rose of Sharon High School</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="previous_school">Enter Previous School Attended</label>
                            <input type="text" name="previous_school" id="previous_school" class="form-control" placeholder="Previous School Name" value="{{ old('previous_school') }}">
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="form-section">
                    <h4>Student Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="{{ old('last_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value="">Select gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="applying_for_form">Applying for Form <span class="text-danger">*</span></label>
                            <select name="applying_for_form" id="applying_for_form" class="form-control" required>
                                <option value="">Applying for Form</option>
                                <option value="Form 1" {{ old('applying_for_form') == 'Form 1' ? 'selected' : '' }}>Form 1</option>
                                <option value="Form 2" {{ old('applying_for_form') == 'Form 2' ? 'selected' : '' }}>Form 2</option>
                                <option value="Form 3" {{ old('applying_for_form') == 'Form 3' ? 'selected' : '' }}>Form 3</option>
                                <option value="Form 4" {{ old('applying_for_form') == 'Form 4' ? 'selected' : '' }}>Form 4</option>
                                <option value="Form 5" {{ old('applying_for_form') == 'Form 5' ? 'selected' : '' }}>Form 5</option>
                                <option value="Form 6" {{ old('applying_for_form') == 'Form 6' ? 'selected' : '' }}>Form 6</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="religion">Religion</label>
                            <input type="text" name="religion" id="religion" class="form-control" placeholder="Religion" value="{{ old('religion') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="street_address">Street Address</label>
                            <input type="text" name="street_address" id="street_address" class="form-control" placeholder="Street Address" value="{{ old('street_address') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="residential_area">Select Residential Area (Optional)</label>
                            <select name="residential_area" id="residential_area" class="form-control">
                                <option value="">Select Residential Area</option>
                                <option value="Zimre Park" {{ old('residential_area') == 'Zimre Park' ? 'selected' : '' }}>Zimre Park</option>
                                <option value="Ruwa" {{ old('residential_area') == 'Ruwa' ? 'selected' : '' }}>Ruwa</option>
                                <option value="Harare" {{ old('residential_area') == 'Harare' ? 'selected' : '' }}>Harare</option>
                                <option value="Chitungwiza" {{ old('residential_area') == 'Chitungwiza' ? 'selected' : '' }}>Chitungwiza</option>
                                <option value="Epworth" {{ old('residential_area') == 'Epworth' ? 'selected' : '' }}>Epworth</option>
                                <option value="Other" {{ old('residential_area') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="subjects_of_interest">Select Subjects of Interest</label>
                            <select name="subjects_of_interest[]" id="subjects_of_interest" class="form-control" multiple style="height: 120px;">
                                @if(isset($subjects))
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple subjects</small>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="form-section">
                    <h4>Guardian Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="guardian_full_name">Guardian Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="guardian_full_name" id="guardian_full_name" class="form-control" placeholder="Guardian Full Name" value="{{ old('guardian_full_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_phone">Guardian Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="guardian_phone" id="guardian_phone" class="form-control" placeholder="Guardian Phone Number" value="{{ old('guardian_phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_email">Guardian Email</label>
                            <input type="email" name="guardian_email" id="guardian_email" class="form-control" placeholder="Guardian Email" value="{{ old('guardian_email') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_relationship">Relationship with Student <span class="text-danger">*</span></label>
                            <select name="guardian_relationship" id="guardian_relationship" class="form-control" required>
                                <option value="">Select relationship</option>
                                <option value="Father" {{ old('guardian_relationship') == 'Father' ? 'selected' : '' }}>Father</option>
                                <option value="Mother" {{ old('guardian_relationship') == 'Mother' ? 'selected' : '' }}>Mother</option>
                                <option value="Guardian" {{ old('guardian_relationship') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                                <option value="Uncle" {{ old('guardian_relationship') == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                <option value="Aunt" {{ old('guardian_relationship') == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                <option value="Grandparent" {{ old('guardian_relationship') == 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                                <option value="Other" {{ old('guardian_relationship') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Other Information -->
                <div class="form-section">
                    <h4>Other Information</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="birth_entry_number">Birth Entry Number</label>
                            <input type="text" name="birth_entry_number" id="birth_entry_number" class="form-control" placeholder="Birth Entry Number" value="{{ old('birth_entry_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="dream_job">Dream Job</label>
                            <input type="text" name="dream_job" id="dream_job" class="form-control" placeholder="Dream Job" value="{{ old('dream_job') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="expected_start_date">Expected Start Date</label>
                            <input type="date" name="expected_start_date" id="expected_start_date" class="form-control" value="{{ old('expected_start_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Documents Upload -->
                <div class="form-section">
                    <h4>Documents Upload</h4>
                    <div id="documents-container">
                        <div class="document-row row">
                            <div class="col-md-5">
                                <input type="text" name="document_names[]" class="form-control" placeholder="Document Name (e.g., Birth Certificate)">
                            </div>
                            <div class="col-md-5">
                                <input type="file" name="documents[]" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success btn-add-document" onclick="addDocumentRow()">+</button>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG. Max size: 5MB per file.</small>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Submit Application</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer-box">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="footer_blog">
                        <div class="full margin-bottom_30">
                            <img style="height:80px;width:100px" src="images/logo.png" alt="image">
                        </div>
                        <div class="full white_fonts">
                            <p>Our Vision is to provide a well-groomed, enriched (in ideas) and productive learner given a firm foundation for tertiary and life challenges.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="footer_blog full white_fonts">
                        <h3>Contact us</h3>
                        <ul class="full">
                            <li><img src="images/i5.png"><span>6884 Mt Madecheche Road<br>Zimre Park</span></li>
                            <li><img src="images/i6.png"><span>info@roshs.co.zw</span></li>
                            <li><img src="images/i7.png"><span>+263 772 490 478</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="footer_bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p style="color: white">Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | Rose Of Sharon High School</p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>
    <script>
        function addDocumentRow() {
            var container = document.getElementById('documents-container');
            var newRow = document.createElement('div');
            newRow.className = 'document-row row mt-2';
            newRow.innerHTML = '<div class="col-md-5"><input type="text" name="document_names[]" class="form-control" placeholder="Document Name"></div><div class="col-md-5"><input type="file" name="documents[]" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png"></div><div class="col-md-2"><button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()">-</button></div>';
            container.appendChild(newRow);
        }
    </script>
</body>
</html>
