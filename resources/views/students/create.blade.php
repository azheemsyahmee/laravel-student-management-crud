@extends('admin.layouts.main')

@section('content')
<div class="container mt-2">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left mb-2">
                <h2>Add Student</h2>
            </div>
            <div class="pull-right ">
                <a class="btn btn-primary" href="{{ route('students.index') }}"> Back</a>
            </div>
        </div>
    </div>   

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-mid-12">
                <div class="form-group">
                    <strong>Book Title:</strong>
                    <input type="text" name="name" class="form-control" placeholder="Book title">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-mid-12">
                <div class="form-group">
                    <strong>Book Description</strong>
                    <input type="text" name="course" class="form-control" placeholder="Book description">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-mid-12">
                <div class="form-group">
                    <label>Attachment:</label>
                    <input type="file" name="attachment" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary ml-3">Submit</button>
        </div>
    </form>
</div>
@endsection
