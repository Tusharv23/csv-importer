<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Student;
  
class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUpload()
    {
        return view('fileUpload');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUploadPost(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);
        if (!file_exists($request->file) || !is_readable($request->file))
        return false;

        if (($handle = fopen($request->file, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, ',')) !== false)
            {
                try{
                    \DB::beginTransaction();
                    $student = new Student;
                    $student->first_name = $row[0];
                    $student->last_name = $row[1];
                    $student->email = $row[2];
                    $student->save();
                    for($i = 3; $i<count($row); $i++){
                        if(!empty($row[$i] || $row[$i] != null || $row[$i] != ""))
                        \DB::table('addresses')->insert(['student_id'=>$student->id,'address'=>$row[$i]]);
                    }
                } catch(Exception $e){
                    \DB::rollBack();
                }
                \DB::commit();
            }
            fclose($handle);
        }
        return back()
            ->with('success','You have successfully upload file.')
            ->with('file',$request->file->getClientOriginalName());
   
    }
}