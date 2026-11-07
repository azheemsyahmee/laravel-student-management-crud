<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    //middleware punya usage
    public function __construct()
    {
    $this->middleware('admin')->only(['edit']);
    }

    public function index(Request $request)
    {
        //yg ni akan show all data dlm database
        //$students = Student::all();
        $students = student::paginate(2);

        //sbb buat $user = auth()->user(); ni bautkan user dpt tgk only data dia
        //which mean yg policy tk dpt check tapi ikutkan jadi sbb
        if($request->keyword){
            $user = auth()->user();
            $students = $user->students()
                ->where('name', 'LIKE', '%'.$request->keyword.'%')
                ->orWhere('course', 'LIKE', '%'.$request->keyword.'%')
                ->paginate(2);
        }else{
            $user = auth()->user();
            $students = $user->students()->paginate(2);
        }
        
        return view('students.index', compact('students'));//compact apss data dr controller ke view
    }

    public function create()
    {
        return view('students.create');//line students.create bhgn students yg ni dia depend pada web punya route
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'course' => 'nullable'
        ]);

        $student = new Student();
        $student->name = $request->name;
        $student->course = $request->course;
        $student->user_id = auth()->id() ?? 1;

        $student->save();

        if($request->hasFile('attachment')){
            $filename = $student->id.'-'.date("Y-m-d").'.'.$request->attachment->getClientOriginalExtension();

            Storage::disk('public')->putFileAs('', $request->attachment, $filename);

            $student->attachment = $filename;
            $student->save();
        }

        //Mail::to('azz@jobsfeel.com')->send(new \App\Mail\StudentCreated($student));
        dispatch(new \App\Jobs\SendEmailJob($student));

        return redirect()
        ->route('students.index')
        ->with([
            'alert-type' => 'alert-primary',
            'alert' => 'Your registration is saved!'
        ]);
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        //$this->middleware(['admin']);
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $student->name = $request->name;
        $student->course = $request->course;

        $student->save();

        return redirect()->route('students.index');
    }

    public function destroy(Student $student)
    {
        if($student->attachment){
            Storage::disk('public')->delete($student->attachment);
        }
        $student->delete();
        return redirect()->route('students.index');
    }

}
