<?php

namespace App\Exceptions;

use App\Models\BugReport;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Booking or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  Throwable  $exception
     * @return RedirectResponse
     */
    public function report(Throwable $exception)
    {
        if($this->shouldReport($exception)) {

            $file = "null";
            $trace = $exception->getTrace();
            if(array_key_exists(1, $trace)) {
                if(array_key_exists("file", $trace[1])) {
                    $file = $trace[1]["file"];
                }
            }

            try {
                $bug = BugReport::create([
                    'title' => get_class($exception) . "(" . $file . ")",
                    'body' => $trace[0]["file"] . " - " . $exception->getMessage(),
                ]);

                // $users = User::role('super_admin')->get();
                // Notification::send($users, new BugReported($bug));
            } catch (Exception $e) {
                // Handle any exceptions that might be thrown when creating the BugReport
                Log::error('Bug report creation failed: ' . $e->getMessage());
            }
        }

        parent::report($exception);
        return null;
    }
}
