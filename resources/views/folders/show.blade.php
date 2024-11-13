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
                    <form action="{{ route('folders.show', ['folder' => $folder->id]) }}" method="GET">
                    <input type="text" name="search" class="form-control w-100" placeholder="Search..." value="{{ request('search') }}">
                    </form>

                    <!-- Dropdown Button -->
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Create New
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createFolderModal">New Folder</a></li>
                            <li><a class="dropdown-item" href="#">Upload Files</a></li>
                            <li><a class="dropdown-item" href="#">Upload Folder</a></li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div>
                        <button class="btn btn-primary">Share</button>
                        <button class="btn btn-success">Download</button>
                        @if (Auth::user() && Auth::user()->type == 1)
                            <button class="btn btn-danger">Delete</button>
                        @endif
                    </div>
                </div>
                <!-- Folder Content -->
                <div class="card">
                    <div class="card-header">
                        {{ $folder->name }}
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Display child folders -->
                            @if ($childFolders->count() > 0)
                                @foreach ($childFolders as $childFolder)
                                    <div class="col-md-2 mb-3">
                                        <div class="card position-relative">
                                            <!-- Checkbox in the top-right corner -->
                                            <input type="checkbox" class="form-check-input position-absolute top-0 end-0 m-2"
                                                   style="z-index: 1;" name="childFolderSelect[]" value="{{ $childFolder->id }}">

                                            <div class="card-body">
                                                <a href="{{ route('folders.show', $childFolder->id) }}">
                                                    <i class="fas fa-folder fa-3x text-primary mb-2"></i> <!-- Icon for folders -->
                                                    <h5 class="card-title">{{ $childFolder->name }}</h5>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">No subfolders found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal for Creating New Folder -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
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

    <script>
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

    </script>
@endsection
