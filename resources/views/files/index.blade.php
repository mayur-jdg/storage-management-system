@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Files</h1>
    <div class="row">
        @forelse ($files as $file)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $file->name }}</h5>
                        <p class="card-text">{{ round($file->size / 1024, 2) }} KB</p>
                        <a href="{{ route('files.show', $file) }}" class="btn btn-primary">Download</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">
                    No files uploaded yet.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
