<?php

namespace App\Console\Commands;

use App\Models\EmailSchedule;
use App\Models\Recipient;
use App\Models\SmtpGroup;
use App\Models\Smtp as Sm;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as E;

class TriggerEmailPerMinut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:per_minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            set_time_limit(0);

            require base_path("vendor/autoload.php");
            $mail = new PHPMailer(true);

            $endDate=date('Y-m-d H:i:s');
            $startDate=date("Y-m-d H:i:s", strtotime("-90 minutes"));

            $scheduleList = EmailSchedule::where('status','pending')
                                ->whereBetween('schedule_date_time',[$startDate, $endDate])
                                ->get();
            foreach($scheduleList as $schedule){
                $user_id=$schedule->user_id;
                $recipient_list=$schedule->recipient_list;
                $email_subject=$schedule->email_subject;
                $template_body=$schedule->template_body;
                $smtp_group_id=$schedule->smtp_group_id;
                $smtp_ids=$schedule->smtp_ids;
                $batch_number=$schedule->batch_number;
                if($smtp_group_id){
                    $smtps = SmtpGroup::where('id', $smtp_group_id)->first()->toArray();
                    $smtp_ids=explode(',',$smtps['smtp_ids']);
                    $smtps = Sm::whereIn('id', $smtp_ids)->get()->toArray();
                }else{
                    $smtps = Sm::where('id', $smtp_ids)->get()->toArray();
                }
                $recipients = Recipient::where('id',$recipient_list)->first();
                $inputFileName = public_path('uploads/'.$recipients['file_name']);

                $csv = array_map("str_getcsv", file($inputFileName,FILE_SKIP_EMPTY_LINES));
                $keys = array_shift($csv);
                $columns=[];
                foreach ($keys as $key){
                    $columns[]=cleanString($key);
                }
                if( ($handle = fopen( $inputFileName, "r")) !== FALSE) {
                    $rowCounter = 0;
                    while (($rowData = fgetcsv($handle, 0, ",")) !== FALSE) {
                        if($rowCounter > 0) {
                            foreach( $rowData as $key => $value) {
                                $assocData[ $rowCounter - 1][ $columns[$key]] = $value;
                            }
                        }
                        $rowCounter++;
                    }
                    fclose($handle);
                }
                echo "\nuser - " . $user_id;
                $smtpIndex=0;
                foreach ($assocData as $singleRecipient){
                    echo "\smtpIndex - " . $smtpIndex;
                    $status='';
                    $from_email='';
                    try {
                        $smtp = $smtps[$smtpIndex];
                        $mail->isSMTP();
                        $mail->Host       = $smtp['server'];
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $smtp['user_email'];
                        $mail->Password   = $smtp['user_password'];
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;

                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

                        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
                        $mail->addAddress($singleRecipient['email']);

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

                    $keys = array_keys($smtps);
                    $ordinal = (array_search($smtpIndex,$keys)+1)%count($keys);
                    $smtpIndex = $keys[$ordinal];

                    $email_log=[
                        'user_id'=>$user_id,
                        'recipent_email'=>$singleRecipient['email'],
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
                     sleep(1);
                }

                EmailSchedule::where("id", $schedule->id)->update(["status" => "completed"]);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
