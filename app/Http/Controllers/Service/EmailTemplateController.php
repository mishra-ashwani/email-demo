<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use URL;
use File;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emailTemplates = EmailTemplate::where('user_id', Auth::User()->id)->get();
        return view('user.template.list-all',compact('emailTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.template.add-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_title' => 'required|max:127',
            'template_body' => 'required'
        ]);

        $emailTemplate=new EmailTemplate();
        $user_id=Auth::user()->id;
        
        $emailTemplate->template_title=$request->get('template_title');
        $emailTemplate->template_body=$request->get('template_body');
        $emailTemplate->user_id=$user_id;

        $emailTemplate->save();

        session()->flash('classes', 'alert-success');
        return back()
        ->with('message','Email Template Saved Successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $template = EmailTemplate::where('id',$id)->first();
        return response()->json([
            'content' => $template->template_body,
            'result' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $template = EmailTemplate::where('id',$id)->first();
        return view('user.template.edit',compact('template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'template_title' => 'required|max:127',
            'template_body' => 'required'
        ]);

        $user_id=Auth::user()->id;
       
        $arr=[
            'template_title' => $request->input('template_title'),
            'template_body' => $request->input('template_body'),
        ];
        EmailTemplate::where('id', $id)->where('user_id', Auth::user()->id)->update($arr); 
        session()->flash('classes', 'alert-success');
        return redirect()->route('edit-template',['id'=>$id])->with('message', 'Email Template Updated Successfully!');

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        EmailTemplate::destroy($id);
        return response()->json([
            'message' => 'Email Template Deleted',
            'result' => 'success',
        ]);
    }

    public function upload_image(Request $request)
    {
        $user_id=Auth::user()->id;

        if($request->file()) {
            $fileName = $user_id.'_'.time().'_image_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->move(public_path('uploads'), $fileName);


            return response()->json([
                'path' => URL::asset('public/uploads/'.$fileName),
                'result' => 'success',
            ]);
        }
    }
}