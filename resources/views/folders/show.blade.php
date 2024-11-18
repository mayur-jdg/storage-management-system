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
                    @if (Auth::user() && Auth::user()->type == 1)
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center text-dark" href="{{ route('trash') }}">
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
                    <form action="{{ route('folders.show', ['folder' => $folder->id]) }}" method="GET">
                        <input type="text" name="search" class="form-control w-100" placeholder="Search..."
                            value="{{ request('search') }}">
                    </form>

                    <!-- Dropdown Button -->
                    @if (Auth::user() && Auth::user()->type == 1)
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Create New
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#createFolderModal">New Folder</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#uploadFileModal">Upload Files</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#uploadFolderModal">Upload Folder</a></li>
                            </ul>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div>
                        @if (Auth::user() && Auth::user()->type == 1)
                        <button class="btn btn-primary" id="shareButton" data-bs-toggle="modal" data-bs-target="#shareModal">Share</button>
                            <button class="btn btn-success" id="downloadSelectedItems">Download</button>
                            <button class="btn btn-danger">Delete</button>
                        @endif
                    </div>
                </div>
                <!-- Folder Content -->
                <div class="card">
                    {{-- <div class="card-header">
                        {{ $folder->name }}
                    </div> --}}
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ $folder->name }}</span>
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-2" id="selectAll">Select All</button>
                            <button class="btn btn-sm btn-outline-secondary" id="deselectAll">Deselect All</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Display child folders -->
                            @if ($childFolders->count() > 0)
                                @foreach ($childFolders as $childFolder)
                                    <div class="col-md-2 mb-3">
                                        <div class="card position-relative">
                                            <!-- Checkbox in the top-right corner -->
                                                <input type="checkbox"
                                                    class="form-check-input position-absolute top-0 end-0 m-2 folder-checkbox"
                                                    style="z-index: 1;" name="childFolderSelect[]"
                                                    value="{{ $childFolder->id }}">

                                            <div class="card-body">
                                                <a href="{{ route('folders.show', $childFolder->id) }}">
                                                    <i class="fas fa-folder fa-3x text-primary mb-2"></i>
                                                    <!-- Icon for folders -->
                                                    <h5 class="card-title">{{ $childFolder->name }}</h5>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    {{-- <p class="text-muted">No subfolders found.</p> --}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Display files -->
                    <!-- Files Grid -->
                    <div class="card-body">
                        <div class="row">
                            @foreach ($files as $file)
                                <div class="col-md-2 mb-3">
                                    <div class="card position-relative">
                                        <input type="checkbox" class="form-check-input position-absolute top-0 end-0 m-2 file-checkbox"
                                            style="z-index: 1;" name="fileSelect[]" value="{{ $file->id }}">

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
                                                @elseif (strtolower($extension) === 'mp4')
                                                    <!-- Display a default video thumbnail or icon -->
                                                    {{-- <img src="https://img.youtube.com/vi/{{ $file->path }}maxresdefault.jpg"
                                                        class="img-fluid mb-2" alt="{{ $file->name }}"> --}}
                                                        <div class="video-preview position-relative">
                                                            <video class="img-fluid mb-2" controls>
                                                                <source src="{{ asset('storage/' . $file->path) }}" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                            <div class="play-icon position-absolute top-50 start-50 translate-middle" style="top: calc(50% - 10%);">
                                                                <i class="fas fa-play-circle fa-3x text-white" style="opacity: 0.5;"></i>
                                                            </div>
                                                        </div>
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
                    </div>
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
                        <!-- Add hidden input for parent_id -->
                        <input type="hidden" name="parent_id" value="{{ $folder->id }}">
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
                        <!-- Add hidden input for folder_id -->
                        <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                        <button type="submit" class="btn btn-primary">Upload Files</button>
                    </form>
                    <div class="alert alert-success mt-2" id="fileSuccessMessage" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Previewing Files -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel"
        aria-hidden="true">
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
                        <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                        <button type="submit" class="btn btn-primary">Upload Folder</button>
                    </form>
                    <div class="alert alert-success mt-2" id="folderSuccessMessage" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Sharing Link -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">Share Selected Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Copy the link below to share your selected items:</p>
                    <input type="text" id="shareLink" class="form-control mb-3" readonly>
                    <button class="btn btn-primary" onclick="copyShareLink()">Copy Link</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        //select all or deselect all
        const deselectAllButton = document.getElementById('deselectAll');
        const checkboxes = document.querySelectorAll('.folder-checkbox, .file-checkbox');

        // Update visibility of "Deselect All" button
        function toggleDeselectButton() {
            deselectAllButton.style.display = [...checkboxes].some(cb => cb.checked) ? 'inline-block' : 'none';
        }

        // Add event listeners to checkboxes
        checkboxes.forEach(cb => cb.addEventListener('change', toggleDeselectButton));

        // Initial button visibility check
        toggleDeselectButton();

        // "Select All" functionality
        document.getElementById('selectAll').addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = true);
            toggleDeselectButton();
        });

        // "Deselect All" functionality
        deselectAllButton.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeselectButton();
        });

        //Previewing Files
        $(document).ready(function() {
            let currentIndex = -1; // To keep track of the currently displayed file index
            const files = @json($files); // Pass the files collection from Blade to JavaScript

            // Function to load file details into the modal
            function loadFileDetails(index) {
                if (index < 0 || index >= files.length) return; // Do nothing if the index is out of bounds

                const file = files[index];
                currentIndex = index; // Update the current index

                // Use AJAX to fetch the file details
                $.ajax({
                    url: '/filess/' + file.id, // Adjust the URL to your route for fetching file details
                    method: 'GET',
                    success: function(response) {
                        // Check the file extension
                        const fileExtension = response.file_extension.toLowerCase();
                        let modalContent = '';

                        if (['jpeg', 'png', 'jpg', 'gif'].includes(fileExtension)) {
                            // Image preview
                            modalContent =
                                `<img src="${response.file_url}" class="img-fluid" alt="${response.file_name}">`;
                        } else if (fileExtension === 'mp4') {
                            // Video preview
                            modalContent = `
                        <video controls class="w-100">
                            <source src="${response.file_url}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>`;
                        } else {
                            // Unsupported format message
                            modalContent = '<p>File format not supported for preview.</p>';
                        }

                        // Update the modal content
                        $('#filePreviewContent').html(modalContent);
                    },
                    error: function() {
                        alert('Error fetching file details.');
                    }
                });
            }

            // Event listener for file preview links
            $('a[data-bs-target="#filePreviewModal"]').on('click', function() {
                const fileId = $(this).data('file-id');
                const index = files.findIndex(file => file.id === fileId);
                loadFileDetails(index);
            });

            // Event listeners for Previous and Next buttons
            $('#prevFileBtn').on('click', function() {
                if (currentIndex > 0) {
                    loadFileDetails(currentIndex - 1);
                }
            });

            $('#nextFileBtn').on('click', function() {
                if (currentIndex < files.length - 1) {
                    loadFileDetails(currentIndex + 1);
                }
            });
        });

        //download Folder and files
        $(document).ready(function() {
        $('#downloadSelectedItems').on('click', function() {
            var selectedFiles = [];
            var selectedFolders = [];

            // Collect selected files and folders
            $('.file-checkbox:checked').each(function() {
                selectedFiles.push($(this).val());
            });
            $('.folder-checkbox:checked').each(function() {
                selectedFolders.push($(this).val());
            });

            if (selectedFiles.length > 0 || selectedFolders.length > 0) {
                // Send the AJAX request with both selected files and folders
                $.ajax({
                    url: '{{ route('filesOrFolders.download') }}', // Route to the download method
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        file_ids: selectedFiles,
                        folder_ids: selectedFolders
                    },
                    success: function(response) {
                        if (response.success) {
                            // Trigger the download of the zip file
                            window.location.href = response.download_url;
                        } else {
                            alert('Error occurred while downloading.');
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing the download.');
                    }
                });
            } else {
                alert('Please select at least one file or folder to download.');
            }
        });
    });

        //share copy link
        document.getElementById('shareButton').addEventListener('click', function () {
            // Collect selected folder and file IDs
            let selectedFolders = Array.from(document.querySelectorAll('.folder-checkbox:checked')).map(cb => cb.value);
            let selectedFiles = Array.from(document.querySelectorAll('.file-checkbox:checked')).map(cb => cb.value);

            // Generate the shareable link
            let baseUrl = `${window.location.origin}/share`;
            let queryParams = new URLSearchParams();
            if (selectedFolders.length) queryParams.append('folders', selectedFolders.join(','));
            if (selectedFiles.length) queryParams.append('files', selectedFiles.join(','));

            // Set the link in the modal input
            document.getElementById('shareLink').value = `${baseUrl}?${queryParams.toString()}`;
        });

        function copyShareLink() {
            let shareLinkInput = document.getElementById('shareLink');
            shareLinkInput.select();
            document.execCommand('copy');
            // alert('Link copied to clipboard!');
        }

        //soft delete Items
        document.querySelector('.btn-danger').addEventListener('click', function() {
            // Collect selected folder and file IDs
            const selectedFolders = Array.from(document.querySelectorAll('input[name="childFolderSelect[]"]:checked')).map(input => input.value);
            const selectedFiles = Array.from(document.querySelectorAll('input[name="fileSelect[]"]:checked')).map(input => input.value);

            if (selectedFolders.length === 0 && selectedFiles.length === 0) {
                alert('Please select at least one folder or file to delete.');
                return;
            }

            // Send an AJAX request to delete the selected items
            fetch('{{ route('delete.items') }}', {
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
                    alert('Items deleted successfully.');
                    location.reload(); // Reload the page to update the status
                } else {
                    alert('Failed to delete items. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Handle the AJAX submission for creating a folder
        $(document).ready(function() {
            $('#createFolderForm').on('submit', function(e) {
                e.preventDefault();

                // Get the form data, including parent_id
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

        // Handle AJAX file upload
        $(document).ready(function() {
            $('#uploadFileForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                // AJAX request to upload files
                $.ajax({
                    url: '{{ route('files.uploadToFolder') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Show success message and reload
                        // alert('Files uploaded successfully!');
                        window.location.reload(); // Reload to reflect the uploaded files
                    },
                    error: function(xhr, status, error) {
                        alert('Error uploading files');
                    }
                });
            });
        });

        //upload folder
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
                    url: '{{ route('folders.uploadToFolder') }}',
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
