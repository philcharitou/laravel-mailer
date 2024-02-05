<?php

namespace App\Jobs;

use App\Mail\ColdEmail;
use App\Mail\GenericEmail;
use App\Models\BugReport;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Email;
use App\Models\EmailAddress;
use App\Models\Template;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class RemoveS3Entry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(private $path)
    {
        $this->onQueue('processing');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Storage::delete($this->path);
        } catch (Exception $e) {
            BugReport::create([
                'title' => "RemoveS3Entry Class: " . get_class($e),
                'body' => $e->getMessage(),
            ]);
        }
    }
}
