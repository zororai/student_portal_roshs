<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card</title>
    <style>
        /* Existing styles... */
    </style>
</head>
<body>
    <div class="id-card">
        <h2>Student ID Card</h2>
        <img src="{{ asset('path_to_image/' . $student->user->profile_image) }}" alt="Profile Image">
        <p><strong>Name:</strong> {{ $student->user->name }}</p>
        <p><strong>Roll Number:</strong> {{ $student->roll_number }}</p>
        <p><strong>Class:</strong> {{ $student->class->name }}</p>
        <p><strong>Gender:</strong> {{ $student->gender }}</p>
        <p><strong>Phone:</strong> {{ $student->phone }}</p>
        <p><strong>Date of Birth:</strong> {{ $student->dateofbirth->format('d/m/Y') }}</p>
        <p><strong>Address:</strong> {{ $student->current_address }}</p>
    
        <a href="{{ route('student.download_id_card', $student->id) }}" class="btn btn-primary">Download PDF</a>
    </div>
</body>
</html>