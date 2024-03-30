<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use URL;
use App;
use App\Mail\TestMail;
use App\Models\EmailLog;
use App\Models\EmailSchedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Carbon\Carbon;

use App\Models\Recipient;
use App\Models\EmailTemplate;
use App\Models\Smtp as Sm;
use App\Models\SmtpGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

class MailController extends Controller
{
   public function __construct(){
      ini_set('set_time_limit', 0);
   }
   public function basic_email() {
      set_time_limit(0);

      require base_path("vendor/autoload.php");
      $mail = new PHPMailer(true);

      $smtps=[
         [
            'MAIL_MAILER'=>'smtp',
            'MAIL_HOST'=>'smtp.mail.yahoo.com',
            'MAIL_PORT'=>'587',
            'MAIL_USERNAME'=>'haya01pandit@yahoo.com',
            'MAIL_PASSWORD'=>'ulsayotfmnbtexsr',
            'MAIL_ENCRYPTION'=>'tls',
            'FROM_NAME'=>'Ashish',
            'FROM_ADDRESS'=>'haya01pandit@yahoo.com',
         ],
         [
            'MAIL_MAILER'=>'smtp',
            'MAIL_HOST'=>'smtp.gmail.com',
            'MAIL_PORT'=>'2525',
            'MAIL_USERNAME'=>'haya01pandit@gmail.com',
            'MAIL_PASSWORD'=>'dhizskoqifxjwvep',
            'MAIL_ENCRYPTION'=>'tls',
            'FROM_NAME'=>'Sonu',
            'FROM_ADDRESS'=>'haya01pandit@gmail.com',
         ],
         [
            'MAIL_MAILER'=>'smtp',
            'MAIL_HOST'=>'smtp.gmail.com',
            'MAIL_PORT'=>'2525',
            'MAIL_USERNAME'=>'adhayadigitalsolutions@gmail.com',
            'MAIL_PASSWORD'=>'zskxvcmgilfpbzla',
            'MAIL_ENCRYPTION'=>'tls',
            'FROM_NAME'=>'Adhaya',
            'FROM_ADDRESS'=>'adhayadigitalsolutions@gmail.com',
         ]

      ];

      $emails=[
         'haya01pandit@gmail.com',
         'haya01pandit@yahoo.com',
         'haya01pandit@hotmail.com',
         'ashish.ui1987@gmail.com',
         'adhayadigitalsolutions@gmail.com',
         'hardikresortsorchha@gmail.com',
         'technocom.help@gmail.com',
         'vikramadityapandey1202@gmail.com',
         'ashish_9889103086@rediffmail.com',
         'mishra.ashwani1989@gmail.com',
         'phpmyths@gmail.com',
      ];

      $email_count=count($emails);
      $smtp_count=count($smtps);

      $html="<h1>Hi, welcome user!</h1>";

      $i=0;
      foreach($emails as $email){

         try {

            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $smtps[$i]['MAIL_HOST'];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $smtps[$i]['MAIL_USERNAME'];                     //SMTP username
            $mail->Password   = $smtps[$i]['MAIL_PASSWORD'];                               //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port       = 587;    //465                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->SMTPOptions = array(
               'ssl' => array(
               'verify_peer' => false,
               'verify_peer_name' => false,
               'allow_self_signed' => true
               )
            );

            //Recipients
            $mail->setFrom($smtps[$i]['FROM_ADDRESS'], $smtps[$i]['FROM_NAME']);
            $mail->addAddress($email);     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent to - '.$email." using ".$smtps[$i]['FROM_ADDRESS']."<br><br>";

            $i++;
            if($i >= $smtp_count){
                $i = 0;
            }

         } catch (Exception $e) {
            echo "<br>Message could not be sent. Mailer Error: {$mail->ErrorInfo}<br><br>";
         }

         $mail->getSMTPInstance()->reset();
         $mail->clearAddresses();
         sleep(1);
      }


      dd('Mail Send Successfully !!');
   }

   public function create_email(){
      $recipients = Recipient::where('user_id', getPrimaryUserId(Auth::user()->id))->get();
      $smtpGroups = SmtpGroup::where('user_id', getPrimaryUserId(Auth::user()->id))->get();
      $smtps = Sm::where('user_id', getPrimaryUserId(Auth::user()->id))->get();
      $email_templates = EmailTemplate::where('user_id', getPrimaryUserId(Auth::user()->id))->get();
      return view('user.email.create',compact('recipients','smtpGroups','email_templates','smtps'));
   }
   public function prepare_to_send_email(Request $request){

        $validated = $request->validate([
            'smtp_group_id' => 'required_without:smtp_ids',
            'recipient_list' => 'required',
            'email_subject' => 'required',
            'template_body' => 'required',
            'smtp_ids' => 'required_without:smtp_group_id',
        ]);

        $smtp_group_id=$request->input('smtp_group_id') ?? NULL;
        $recipient_list=$request->input('recipient_list');
        $email_subject=$request->input('email_subject');
        $template_body=$request->input('template_body');
        $smtp_ids=$request->input('smtp_ids') ?? NULL;

        if($smtp_group_id){
            $smtps = SmtpGroup::where('id', $smtp_group_id)->first()->toArray();
            $smtp_ids=explode(',',$smtps['smtp_ids']);
            $smtps = Sm::whereIn('id', $smtp_ids)->get()->toArray();
        }else{
            $smtps = Sm::whereIn('id', $smtp_ids)->get()->toArray();
        }
        $recipients = Recipient::where('user_id', getPrimaryUserId(Auth::user()->id))->where('id',$recipient_list)->first();

        $inputFileName = public_path('uploads/'.$recipients['file_name']);

        $csv = array_map("str_getcsv", file($inputFileName,FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($csv);
        $columns=[];
        foreach ($keys as $key){
            $columns[]=cleanString($key);
        }
        $recipentMeta=json_encode($columns,true);

        $headerRecord='';
        if( ($handle = fopen( $inputFileName, "r")) !== FALSE) {
            $rowCounter = 0;
            while (($rowData = fgetcsv($handle, 0, ",")) !== FALSE) {
                if( 0 === $rowCounter) {
                    $headerRecord = $rowData;

                } else {
                    foreach( $rowData as $key => $value) {
                        $assocData[ $rowCounter - 1][ $columns[$key]] = $value;
                    }
                }
                $rowCounter++;
            }
            fclose($handle);
        }
        $batchNumber=time();

      return view('user.email.prepare',compact('smtps','recipentMeta','email_subject','template_body','assocData','batchNumber'));

   }
   public function send_email(Request $request){

      set_time_limit(0);

      require base_path("vendor/autoload.php");
      $mail = new PHPMailer(true);

      $counter=$request->input('counter');
      $smtp_id=$request->input('smtp');
      $recipient_list=$request->input('recipient_list');
      $recipient_email=$request->input('recipient');
      $email_subject=$request->input('email_subject');
      $template_body=$request->input('template_body');
      $batch_number=$request->input('batch_number');

      $smtp = Sm::where('id', $smtp_id)->first()->toArray();

      if(!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)){
         // continue;
      }

      $status='';
      $from_email='';
      try {

         $mail->isSMTP();
         $mail->Host       = $smtp['server'];
         $mail->SMTPAuth   = true;
         $mail->Username   = $smtp['user_email'];
         $mail->Password   = $smtp['user_password'];
         $mail->SMTPSecure = 'tls';
         $mail->Port       = 587;
         $mail->Timeout     =   35;

         $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
         );

         $mail->setFrom($smtp['from_email'], $smtp['from_name']);
         $mail->addAddress($recipient_email);

         $mail->isHTML(true);
         $mail->Subject = $email_subject;
         $mail->Body    = $template_body;

         $from_email=$smtp['from_email'];

         $mail->send();

         $status='sent';
         $comments='';

      } catch (Exception $e) {
         $comments= "<br>Message could not be sent. Mailer Error: {$mail->ErrorInfo}<br><br>";
         $status='fail';
      }

      $email_log=[
         'user_id'=>getPrimaryUserId(Auth::user()->id),
         'recipent_email'=>$recipient_email,
         'email_body'=>$template_body ?? '',
         'status'=>$status,
         'comments'=>$comments,
         'from_email'=>$from_email,
         'batch_number'=>$batch_number,
         'created_at'=>Carbon::now()
      ];

      DB::table('email_logs')->insert($email_log);

      $mail->getSMTPInstance()->reset();
      $mail->clearAddresses();
      sleep(15);

      return response()->json([
         'counter' => $counter,
         'from_email' => $from_email,
         'status'=>$status,
         'comment'=>$comments,
     ]);

   }


    public function list_all_email(){
        $email_logs=DB::table('email_logs')
                  ->select('recipent_email', 'from_email','status','comments','created_at')
                  ->orderByRaw('created_at DESC')
                  ->where('user_id',auth()->user()->id)->get();

        return view('user.email.email_log',compact('email_logs'));
    }

    public function schedule_email(Request $request){
        try{
            $postData = $request->all();
            $emailSchedule = new EmailSchedule();

            $email_date=$postData['schedule_date_time'];
            $schedule_date_time=date('Y-m-d H:i',strtotime($email_date));
            $schedule_time=date('H:i',strtotime($email_date));
            $emailSchedule->user_id=getPrimaryUserId(Auth::user()->id);
            $emailSchedule->schedule_date_time=$schedule_date_time;
            $emailSchedule->schedule_time=$schedule_time;
            $emailSchedule->recipient_list=$postData['recipient_list'];
            $emailSchedule->email_subject=$postData['email_subject'];
            $emailSchedule->template_body=$postData['template_body'];
            $emailSchedule->smtp_group_id=$postData['smtp_group_id'] ? $postData['smtp_group_id'] : '';
            $emailSchedule->smtp_ids=$postData['smtp_ids'] ? $postData['smtp_ids'] : '';
            $emailSchedule->status='pending';
            $emailSchedule->batch_number=time();

            $emailSchedule->save();
            echo json_encode(['success' => true,'message' => 'Email Schedule saved']);
            die;
        }catch(Exception $e){
            echo json_encode(['success' => false,'error' => $e->getMessage()]);
            die;
        }
    }
    public function email_schedule_list(Request $request){
        $schedule_list = EmailSchedule::where('user_id', getPrimaryUserId(Auth::user()->id))->orderBy('created_at','DESC')->get();
        return view('user.email.email_schedule_list',compact('schedule_list'));
    }
    public function download_failed_emails(Request $request){
        $batch_number = $request->route('batch_number');
        $failed_emails = EmailLog::where(['batch_number'=>$batch_number,'status'=>'fail'])->pluck('recipent_email');

        $csv_data = new Collection();
        if ($failed_emails->count() === 0) {
            return $csv_data;
        }

        foreach ($failed_emails as $recipent_email) {
            $collection = collect([
                'recipent_email' => $recipent_email,
            ]);
            $csv_data->push($collection);
        }

        $csv_headers = ['recipent_email'];
        $csv_column_names = [ 'recipent_email'];

        $data = '"';
        $data .= implode('","', $csv_headers);
        $data .= '"';
        $data .= "\r\n";

        foreach ($csv_data as $row) {
            $arr_data = [];
            foreach ($csv_column_names as $column_name) {
                array_push($arr_data, $row->get($column_name));
            }
            $data .= implode('","', $arr_data);
            $data .= "\r\n";
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.time().'.csv"',
        ];
        return Response::make($data, 200, $headers);;
    }
}
