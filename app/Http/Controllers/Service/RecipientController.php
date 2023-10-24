<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use URL;
use File;

class RecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipients = Recipient::where('user_id', Auth::User()->id)->get();
        return view('user.recipient.list-all',compact('recipients'));
    }

    /**
     * Show the form for uploading recipient sheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload_form()
    {
        return view('user.recipient.add-new');

    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'recipient_list_name' => 'required|max:127',
            'recipient_list' => 'required|mimes:csv|max:2048'
        ]);

        $recipient=new Recipient();

        $user_id=Auth::user()->id;

        if($request->file()) {
            $fileName = $user_id.'_'.time().'_'.$request->recipient_list->getClientOriginalName();

            $filePath = $request->file('recipient_list')->move(public_path('uploads'), $fileName);

            $recipient->user_id=$user_id;
            $recipient->recipient_list_name = $request->input('recipient_list_name');
            $recipient->recipient_file_path = URL::asset('uploads/'.$fileName);
            $recipient->file_name = $fileName;


            $csv = array_map("str_getcsv", file($filePath,FILE_SKIP_EMPTY_LINES));
            $keys = array_shift($csv);
            $recipentMeta=json_encode($keys,true);
            $recipient->recipient_meta=$recipentMeta;

            $recipient->save();

            session()->flash('classes', 'alert-success');
            return back()
            ->with('message','File has been uploaded.');
        }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function show(Recipient $recipient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipient $recipient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipient $recipient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $recipient = Recipient::where('id', $id)->where('user_id',Auth::user()->id)->first();
        $file_name=$recipient->file_name;
        if(File::exists(public_path('uploads/'.$file_name))){
            File::delete(public_path('uploads/'.$file_name));
        }
        Recipient::destroy($id);

        return response()->json([
            'message' => 'Recipient List Deleted',
            'result' => 'success',
        ]);
    }
}
