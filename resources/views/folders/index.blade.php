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
                <form action="{{ route('folders.index') }}" method="GET">
                    <input type="text" name="search" class="form-control w-25" placeholder="Search..." value="{{ request('search') }}">
                </form>

                    <!-- Dropdown Button -->
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Create New
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="{{ route('folders.index') }}">New Folder</a></li>
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

            <!-- Main Card Content -->
            <div class="card">
                <div class="card-header">Your Folders</div>
                <div class="card-body">
                    <!-- Form to Create a New Folder -->
                    <form action="{{ route('folders.store') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Folder Name" required>
                            <button type="submit" class="btn btn-primary">Create New Folder</button>
                        </div>
                    </form>

                    <!-- Folders Grid -->
                    <div class="row">
                        @forelse ($folders as $folder)
                            <div class="col-md-2 mb-3">
                                <div class="card-body">
                                    <a href="{{ route('folders.show', $folder) }}" class="text-decoration-none">
                                        <i class="fas fa-folder fa-3x text-primary mb-2"></i>
                                        <h5 class="card-title">{{ $folder->name }}</h5>
                                    </a>
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
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
