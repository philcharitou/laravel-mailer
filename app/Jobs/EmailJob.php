<?php

namespace App\Jobs;

use App\Mail\Email as Email;
use App\Models\BugReport;
use App\Models\Template;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(private $array, private $server)
    {
        $this->onQueue('processing');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $array = $this->array;

            $keys = array_keys($array);

            switch($this->server) {
                case "sendgrid":
                    $from = "address@domain.com";
                case "outlook":
                    $from = "address@domain.com";
                case "gmail":
                    $from = "address@domain.com";
                default:
                    $from = null;
            }

            $to = array_key_exists('to', $array) ? $array['to'] : null;

            if($from && $to) {
                $template = Template::first();


                $template_text = $template->content;
                foreach($keys as $placeholder)
                {
                    $template_text = str_replace("[".$placeholder."]", $array[$placeholder], $template_text);
                }

                $signature_url = $template->signature_image ? Storage::drive('s3')->temporaryUrl($template->signature_image, now()->addDays(6)) : URL::asset('/img/signature_image.jpg') ;
                if($signature_url) $template_text = str_replace("[signature_image]", '<br><img src='.$signature_url.' alt="Signature Logo" style="height: 40px">', $template_text);

                Mail::mailer($this->server)->to($to)->send(new Email([
                    'from_address' => $from,
                    'to_address' => $to,
                    'cc_address' => '',
                    'bcc_address' => '',
                    'subject' => $template->subject,
                    'body' => $template_text,
                    'documents' => $template->documents,
                ]));
            }
        } catch (Exception $e) {
            BugReport::create([
                'title' => get_class($e),
                'body' => $e->getMessage(),
            ]);
        }
    }
}
