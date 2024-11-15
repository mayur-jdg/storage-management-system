@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 bg-light sidebar p-3">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex align-items-center text-dark" href="{{ URL('/files') }}">
                            <i class="fas fa-folder-open me-2 text-primary"></i> <!-- FontAwesome icon for "Files" -->
                            Files
                        </a>
                    </li>
                    <!-- Show "Trash" only if the login type is 1 -->
                    @if (Auth::user() && Auth::user()->type == 1)
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center text-dark" href="#">
                                <i class="fas fa-trash-alt me-2 text-danger"></i> <!-- FontAwesome icon for "Trash" -->
                                Trash
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-10">
                <!-- Topbar/Header -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                    <!-- Search Bar -->
                    <form action="{{ route('home') }}" method="GET">
                        <input type="text" name="search" class="form-control w-100" placeholder="Search..."
                            value="{{ request('search') }}">
                    </form>

                    <!-- Action Buttons -->
                    <div>
                        <!-- Show "Delete" button only if the login type is 1 -->
                        @if (Auth::user() && Auth::user()->type == 1)
                            <button class="btn btn-success">Restore</button>
                            <button class="btn btn-danger" id="permanentlyDeleteBtn">Permanently  Delete</button>
                        @endif
                    </div>
                </div>

                <!-- Main Card Content -->
                <div class="card">
                    <div class="card-header">Trash Files</div>
                    <div class="card-body">
                        {{-- <div class="alert alert-success">
                        @if ($message = Session::get('success'))
                            {{ $message }}
                        @else
                            You are logged in!
                        @endif
                    </div> --}}
                        <!-- Folders Grid -->
                        <div class="row">
                            @forelse ($folders as $folder)
                                <div class="col-md-2 mb-3">
                                    <div class="card position-relative">
                                        <!-- Checkbox in the top-right corner -->
                                        @if (Auth::user() && Auth::user()->type == 1)
                                            <input type="checkbox"
                                                class="form-check-input position-absolute top-0 end-0 m-2"
                                                style="z-index: 1;" name="folderSelect[]" value="{{ $folder->id }}">
                                        @endif

                                        <div class="card-body">
                                            <a href="{{ route('folders.show', $folder) }}" class="text-decoration-none">
                                                <i class="fas fa-folder fa-3x text-primary mb-2"></i>
                                                <h5 class="card-title">{{ $folder->name }}</h5>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        No folders found.
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Files Grid -->
                        <div class="row">
                            @foreach ($files as $file)
                                <div class="col-md-2 mb-3">
                                    <div class="card position-relative">
                                        @if (Auth::user() && Auth::user()->type == 1)
                                            <input type="checkbox"
                                                class="form-check-input position-absolute top-0 end-0 m-2"
                                                style="z-index: 1;" name="fileSelect[]" value="{{ $file->id }}">
                                        @endif
                                        <div class="card-body">
                                            <a href="#" class="text-decoration-none" data-bs-toggle="modal"
                                                data-bs-target="#filePreviewModal" data-file-id="{{ $file->id }}">
                                                @php
                                                    $extension = pathinfo($file->path, PATHINFO_EXTENSION); // Get file extension
                                                @endphp

                                                @if (in_array(strtolower($extension), ['jpeg', 'png', 'jpg', 'gif']))
                                                    <!-- Display image thumbnail -->
                                                    <img src="{{ asset('storage/' . $file->path) }}" class="img-fluid mb-2"
                                                        alt="{{ $file->name }}">
                                                @elseif (strtolower($extension) == 'mp4')
                                                    <!-- Display a video thumbnail (can be a static image or a custom thumbnail) -->
                                                    {{-- <img src="https://img.youtube.com/vi/{{ $file->path }}maxresdefault.jpg"
                                                        class="img-fluid mb-2" alt="{{ $file->name }}"> --}}

                                                        <div class="video-preview position-relative">
                                                            <video class="img-fluid mb-2" controls>
                                                                <source src="{{ asset('storage/' . $file->path) }}" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                            <div class="play-icon position-absolute top-50 start-50 translate-middle">
                                                                <i class="fas fa-play-circle fa-3x text-white" style="opacity: 0.5;"></i>
                                                            </div>
                                                        </div>

                                                    {{-- <video class="img-fluid mb-2" controls>
                                                    <source src="{{ asset('storage/' . $file->path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video> --}}

                                                    <!-- <img src="path/to/play-button-thumbnail.jpg" class="img-fluid mb-2" alt="{{ $file->name }}"> -->
                                                @else
                                                    <!-- Display a default file icon for non-image, non-video files -->
                                                    <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($files->isEmpty() && $folders->isEmpty())
                            <div class="alert alert-warning">
                                No folders or files found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Previewing Files -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="filePreviewContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevFileBtn">Previous</button>
                    <button type="button" class="btn btn-secondary" id="nextFileBtn">Next</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Creating New Folder -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createFolderForm">
                        @csrf
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Folder</button>
                    </form>
                    <div class="alert alert-success mt-2" id="successMessage" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Uploading Files -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Upload Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadFileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="files" class="form-label">Choose Files</label>
                            <input type="file" class="form-control" id="files" name="files[]" multiple required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Files</button>
                    </form>
                    <div class="alert alert-success mt-2" id="fileSuccessMessage" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Uploading Folders -->
    <div class="modal fade" id="uploadFolderModal" tabindex="-1" aria-labelledby="uploadFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFolderModalLabel">Upload Folders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadFolderForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="folders" class="form-label">Choose Folders</label>
                            <input type="file" class="form-control" id="folders" name="folders[]" webkitdirectory multiple required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Folder</button>
                    </form>
                    <div class="alert alert-success mt-2" id="folderSuccessMessage" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        // Permanently delete files
        $(document).ready(function() {
            $('#permanentlyDeleteBtn').on('click', function() {
                // Show a confirmation prompt
                const confirmation = confirm('Are you sure you want to permanently delete the selected items? This action cannot be undone.');

                if (!confirmation) {
                    return; // If the user cancels, do nothing
                }

                // Gather selected folder and file IDs
                const folderIds = $('input[name="folderSelect[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                const fileIds = $('input[name="fileSelect[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                // Send AJAX request to delete selected items
                $.ajax({
                    url: '{{ route("folders.permanentlyDelete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedFolders: folderIds,
                        selectedFiles: fileIds
                    },
                    success: function(response) {
                        alert('Selected items permanently deleted.');
                        // Optionally, reload the page or update the DOM to reflect changes
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting items.');
                        console.error(xhr.responseText);
                    }
                });
            });
        });


        //restore delete Items
        document.querySelector('.btn-success').addEventListener('click', function() {
            // Collect selected folder and file IDs
            const selectedFolders = Array.from(document.querySelectorAll('input[name="folderSelect[]"]:checked')).map(input => input.value);
            const selectedFiles = Array.from(document.querySelectorAll('input[name="fileSelect[]"]:checked')).map(input => input.value);

            if (selectedFolders.length === 0 && selectedFiles.length === 0) {
                alert('Please select at least one folder or file to restore.');
                return;
            }

            // Send an AJAX request to delete the selected items
            fetch('{{ route('restore.items') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    folders: selectedFolders,
                    files: selectedFiles
                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    alert('Items restored successfully.');
                    location.reload(); // Reload the page to update the status
                } else {
                    alert('Failed to restore items. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        //Previewing Files

        $(document).ready(function() {
            let currentIndex = -1; // To keep track of the currently displayed file index
            const files =
            @json($files); // This passes the files collection from your backend to JavaScript

            // Function to load file details into the modal
            function loadFileDetails(index) {
                if (index < 0 || index >= files.length) {
                    return; // Do nothing if the index is out of bounds
                }

                const file = files[index];
                currentIndex = index; // Update the current index

                // Use AJAX to fetch the file details by ID
                $.ajax({
                    url: '/filess/' + file.id, // Adjust the URL to your route for fetching file details
                    method: 'GET',
                    success: function(response) {
                        // Check if the file is an image or video
                        const fileExtension = response.file_extension.toLowerCase();

                        // Set the modal content based on file type
                        let modalContent = '';
                        if (['jpeg', 'png', 'jpg', 'gif'].includes(fileExtension)) {
                            // Display image
                            modalContent = '<img src="' + response.file_url +
                                '" class="img-fluid" alt="' + response.file_name + '">';
                        } else if (fileExtension === 'mp4') {
                            // Display video
                            modalContent = '<video controls class="w-100"><source src="' + response
                                .file_url +
                                '" type="video/mp4">Your browser does not support the video tag.</video>';
                        } else {
                            modalContent = '<p>File format not supported for preview.</p>';
                        }

                        // Set the modal content
                        $('#filePreviewContent').html(modalContent);
                    },
                    error: function() {
                        alert('Error fetching file details.');
                    }
                });
            }

            // When an image or video is clicked
            $('a[data-bs-toggle="modal"]').on('click', function() {
                const fileId = $(this).data('file-id'); // Get the file ID from the clicked element

                // Find the index of the clicked file in the files array
                currentIndex = files.findIndex(file => file.id === fileId);
                loadFileDetails(currentIndex); // Load the file details
            });

            // Handle Previous button click
            $('#prevFileBtn').on('click', function() {
                if (currentIndex > 0) {
                    loadFileDetails(currentIndex - 1); // Load the previous file
                }
            });

            // Handle Next button click
            $('#nextFileBtn').on('click', function() {
                if (currentIndex < files.length - 1) {
                    loadFileDetails(currentIndex + 1); // Load the next file
                }
            });
        });




        // Handle the AJAX submission for creating a folder
        $(document).ready(function() {
            $('#createFolderForm').on('submit', function(e) {
                e.preventDefault();

                // Get the form data
                var formData = $(this).serialize();

                // AJAX request to create the folder
                $.ajax({
                    url: '{{ route('folders.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Close the modal
                        $('#createFolderModal').modal('hide');

                        // Show success message
                        $('#successMessage').text('Folder created successfully!').show();

                        // Reload the page to show the new folder
                        window.location.reload(); // Page reload

                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        alert('Error creating folder');
                    }
                });
            });
        });


        $(document).ready(function() {
            $('#uploadFileForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('files.upload') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#uploadFileModal').modal('hide');
                        $('#fileSuccessMessage').text('Files uploaded successfully!').show();
                        window.location.reload(); // Page reload to show the new files
                    },
                    error: function(xhr, status, error) {
                        alert('Error uploading files');
                    }
                });
            });
        });


        $(document).ready(function() {
            $('#uploadFolderForm').on('submit', function(e) {
                e.preventDefault();

                // Create a new FormData object
                let formData = new FormData(this);

                // Get the folder names dynamically
                let folderNames = new Set();  // Set to hold unique folder names

                // Loop through the selected files and extract folder names
                let files = $('#folders')[0].files;
                Array.from(files).forEach(file => {
                    let path = file.webkitRelativePath;  // Get the relative path of the file
                    let folderName = path.split('/')[0]; // The folder name is the first part of the path
                    folderNames.add(folderName);  // Add the folder name to the set
                });

                // Append the folder names to the formData object
                formData.append('folder_names', JSON.stringify(Array.from(folderNames)));

                // Append the files to the formData object with the correct field name
                Array.from(files).forEach(file => {
                    formData.append('files[]', file);
                });

                // AJAX request to upload the folder
                $.ajax({
                    url: '{{ route('folders.upload') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#uploadFolderModal').modal('hide');
                        $('#folderSuccessMessage').text('Folder uploaded successfully!').show();
                        window.location.reload(); // Page reload to show the new folders and files
                    },
                    error: function(xhr, status, error) {
                        alert('Error uploading folder');
                    }
                });
            });
        });


    </script>
@endsection
