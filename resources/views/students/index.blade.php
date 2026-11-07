@extends('admin.layouts.main')

@section('content')

<div class="container mt-2">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Index Book</h2>
            </div>
            <div class="pull-right mb-2">
                <a class="btn btn-success" href="{{ route('students.create') }}"> Create Book</a>
            </div>
        </div>
    </div>

    <div class='float-right'>
        <form action="" method="">
            <div class="input-group">
                <input type="text" class="form-control" name="keyword" value="{{ request()->get('keyword')}}"/>
                <div class="input-grouo-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>

    @if(session()->has('alert'))
    <div class="alert {{ session()->get('alert-type') }}" role="alert">
        {{ session()->get('alert') }}
    </div>
    @endif


    <table class="table table-bordered">
        <thead>
            <tr>
                {{-- table punya tajuk kecik selalu terbalik --}}
                <th>ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Attachment</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody>
            {{--ni akan buat table--}}
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->course }}</td>
                    <td>{{ $student->attachment }}</td>
                    <td>
                        <form action="{{ route('students.destroy', $student->id) }}" method="POST">
                            <a class="btn btn-success" href="{{ route('students.show', $student->id)}}">Show</a>
                            <a class="btn btn-primary" href="{{ route('students.edit', $student->id)}}">Edit</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- {{ $students->links() }} --}}
{{ $students->appends(['keyword' => request()->get('keyword')])->links() }}
@endsection
