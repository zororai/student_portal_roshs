<?php

namespace App\Jobs;

use App\Helpers\SmsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendParentSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phone;
    public $message;
    public $parentId;
    public $studentId;

    public $tries = 3;
    public $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phone, string $message, int $parentId = null, int $studentId = null)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->parentId = $parentId;
        $this->studentId = $studentId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $result = SmsHelper::sendSms($this->phone, $this->message);

        if ($result['success']) {
            Log::info('SMS job completed successfully', [
                'phone' => $this->phone,
                'parent_id' => $this->parentId,
                'student_id' => $this->studentId
            ]);
        } else {
            Log::error('SMS job failed', [
                'phone' => $this->phone,
                'parent_id' => $this->parentId,
                'student_id' => $this->studentId,
                'error' => $result['message'] ?? 'Unknown error'
            ]);
            
            $this->fail(new \Exception($result['message'] ?? 'SMS sending failed'));
        }
    }
}
