<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Smtp;
use App\Models\SmtpGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP as PHPMailerSMTP;

class SMTPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $smtps = Smtp::where('user_id', getPrimaryUserId(Auth::user()->id))->get();
        return view('user.smtp.list-all',compact('smtps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $smtpGroups=SmtpGroup::where('user_id',getPrimaryUserId(Auth::user()->id))->get();
        return view('user.smtp.add-new',['smtpGroups'=>$smtpGroups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id=getPrimaryUserId(Auth::user()->id);
        $user_plan=getUserPlan($user_id);
        $smtps=Smtp::where('user_id',$user_id)->get();

        if($user_plan == 'Free' && $smtps->count() == 5){
            session()->flash('classes', 'alert-error');
            return redirect()->route('add-new-smtp')->with('message', 'Trail Account. SMTP Limit Reached.');
        }

        $validated = $request->validate([
            'account_name' => 'required|max:64',
            'from_name' => 'required|max:64',
            'from_email' => 'required|email',
            'reply_email' => 'required|email',
            'server' => 'required',
            'port' => 'required|min:2|max:4',
            'user_email' => 'required|email',
            'user_password' => 'required',
        ]);

        $smtp=new SMTP();

        $smtp->account_name=$request->input('account_name');
        $smtp->from_name=$request->input('from_name');
        $smtp->from_email=$request->input('from_email');
        $smtp->reply_email=$request->input('reply_email');
        $smtp->server=$request->input('server');
        $smtp->port=$request->input('port');
        $smtp->user_email=$request->input('user_email');
        $smtp->user_password=$request->input('user_password');
        $smtp->user_id=getPrimaryUserId(Auth::user()->id);
        $smtp->status='1';

        $smtp->save();
        session()->flash('classes', 'alert-success');
        return redirect()->route('add-new-smtp')->with('message', 'SMTP Saved Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Smtp  $smtp
     * @return \Illuminate\Http\Response
     */
    public function show(Smtp $smtp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Smtp  $smtp
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $smtp = Smtp::where('id',$id)->first();
        return view('user.smtp.edit',compact('smtp'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Smtp  $smtp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'account_name' => 'required|max:64',
            'from_name' => 'required|max:64',
            'from_email' => 'required|email|unique:smtps,from_email,'.$id,
            'reply_email' => 'required|email',
            'server' => 'required',
            'port' => 'required|min:2|max:4',
            'user_email' => 'required|email',
            'user_password' => 'required',
        ]);

        $arr=[
            'account_name' => $request->input('account_name'),
            'from_name' => $request->input('from_name'),
            'from_email' => $request->input('from_email'),
            'reply_email' => $request->input('reply_email'),
            'server' => $request->input('server'),
            'port' => $request->input('port'),
            'user_email' => $request->input('user_email'),
            'user_password' => $request->input('user_password'),
        ];

        Smtp::where('id', $id)->where('user_id', getPrimaryUserId(Auth::user()->id))->update($arr);
        return redirect()->route('edit-smtp',['id'=>$id])->with('message', 'SMTP Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Smtp  $smtp
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id){
        Smtp::destroy($id);
    }
    public function delete_group(int $id){
        SmtpGroup::destroy($id);
        return response()->json(['status' => 'success']);
    }

    public function test_smtp(Request $request){
        require base_path("vendor/autoload.php");

        try{
            $mail = new PHPMailer(true);     // Passing `true` enables exceptions
            $mail->isSMTP();
            $mail->Host = $request->input('host');
            $mail->SMTPAuth = true;
            $mail->Username = $request->input('user_email');
            $mail->Password = $request->input('user_password');
            $mail->SMTPSecure = 'tls';
            $mail->Port = $request->input('port');
            $mail->isHTML(true);

            $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
             );

            $mail->Subject = 'Email Testing @HexCodeSoftwares';

            $mailContent = "<h1>Testing Email</h1><p>This is a test email I\'m sending using SMTP mail server.</p>";
            $mail->Body = $mailContent;

            $mail->setFrom($request->input('user_email'), $request->input('from_name'));
            $mail->addAddress($request->input('user_email'));


            if($mail->send()){
                return response()->json([
                    'message' => 'Message has been sent',
                    'result' => 'success',
                ]);
            }else{
                return response()->json([
                    'message' => $mail->ErrorInfo,
                    'result' => 'fail',
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'message' => $mail->ErrorInfo,
                'result' => 'fail',
            ]);
        }


    }

    public function test_smtp_by_id(Request $request){
        $smtp = Smtp::where('id',$request->input('smtp_id'))->first();
        require base_path("vendor/autoload.php");

        // dd($smtp->server);

        try{
            $mail = new PHPMailer(true);     // Passing `true` enables exceptions
            $mail->isSMTP();
            $mail->Host = $smtp->server;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp->user_email;
            $mail->Password = $smtp->user_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = $smtp->port;
            $mail->isHTML(true);

            $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
             );

            $mail->Subject = 'Email Testing @HexCodeSoftwares';

            $mailContent = "<h1>Testing Email</h1><p>This is a test email I\'m sending using SMTP mail server.</p>";
            $mail->Body = $mailContent;

            $mail->setFrom($smtp->user_email, $smtp->from_name);
            // $mail->addAddress($smtp['user_email']);
            $mail->addAddress('phpmyths@gmail.com');

            if($mail->send()){
                return response()->json([
                    'message' => 'Message has been sent',
                    'result' => 'success',
                ]);
            }else{
                return response()->json([
                    'message' => $mail->ErrorInfo,
                    'result' => 'fail',
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'message' => $mail->ErrorInfo,
                'result' => 'fail',
            ]);
        }
    }

    public function create_group(){
        $smtpGroups = \DB::table("smtp_groups")
            ->select("smtp_groups.id","smtp_groups.group_name",\DB::raw("GROUP_CONCAT(smtps.account_name) as account_name"))
            ->leftjoin("smtps",\DB::raw("FIND_IN_SET(smtps.id,smtp_groups.smtp_ids)"),">",\DB::raw("'0'"))
            ->where('smtp_groups.user_id',getPrimaryUserId(Auth::user()->id))
            ->groupBy("smtp_groups.id")
            ->get();
        $smtps=Smtp::where('user_id',getPrimaryUserId(Auth::user()->id))->get();
        return view('user.smtp.add-new-group',['smtpGroups'=>$smtpGroups,'smtps'=>$smtps]);
    }
    public function save_group(Request $request){

        $request->validate([
            'group_name' => 'required|max:128',
            'smtp_id' => 'required',
        ]);

        $smtpGroup=new SmtpGroup();

        $smtpGroup->group_name=$request->input('group_name');
        $smtpGroup->user_id=getPrimaryUserId(Auth::user()->id);
        $smtp_ids=implode(',',$request->input('smtp_id'));
        $smtpGroup->smtp_ids=$smtp_ids;

        $smtpGroup->save();
        session()->flash('classes', 'alert-success');
        return redirect()->route('create-smtp-group')->with('message', 'SMTP Group Saved Successfully!');
    }
}
