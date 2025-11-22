@extends('layouts.app')

@section('title', 'Webcam Capture')

@section('content')
    <h1>Webcam Capture</h1>
    <video id="video" width="640" height="480" autoplay></video>
    <button id="snap">Snap Photo</button>
    <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>

    <form id="upload-form" method="POST" action="{{ route('webcam.upload') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="image" id="image">
        <button type="submit">Upload Photo</button>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
        <img src="{{ asset('images/' . session('image')) }}" alt="Captured Image">
    @endif
@endsection

@push('scripts')
    <script>
        // Get references to the elements
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        const imageInput = document.getElementById('image');

        // Debug log to indicate the script is running
        console.log('Webcam capture script loaded.');

        // Request access to the webcam
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                console.log('Webcam stream received:', stream);
                video.srcObject = stream;
            })
            .catch(error => {
                console.error('Error accessing webcam: ', error);
                alert('Unable to access your camera. Please check that you have granted permission and that you are accessing the site via HTTPS or localhost.');
            });

        // Set up the "Snap Photo" button
        document.getElementById('snap').addEventListener('click', () => {
            console.log('Snap button clicked.');
            // Draw the current frame from the video to the canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            // Convert the canvas image to a Blob and then to a Base64 string
            canvas.toBlob(blob => {
                if (!blob) {
                    console.error('Failed to capture image blob.');
                    return;
                }
                console.log('Image blob captured:', blob);
                const reader = new FileReader();
                reader.onloadend = () => {
                    console.log('Image converted to Base64.');
                    // Store the Base64 image data into the hidden input field
                    imageInput.value = reader.result;
                };
                reader.readAsDataURL(blob);
            });
        });
    </script>
@endpush
