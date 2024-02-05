<?php

namespace App\Jobs;

use App\Models\BugReport;
use App\Models\Email;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MassEmailExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 720;

    /**
     * Create a new job instance.
     */
    public function __construct(private $storage_path, private $start, private $end, private $server) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $named_array = $this->parseWorksheet($this->getSheetFromS3()->getSheet(0));
            $named_array = array_map('array_filter', $named_array);
            $named_array = array_filter($named_array);

            $chain = [];
            for($i = $this->start; $i <= $this->end; $i++) {
                if(array_key_exists($i, $named_array))
                    $chain[] = new EmailJob($named_array[$i], $this->server);
            }
            $chain[] = new RemoveS3Entry($this->storage_path);

            Bus::chain($chain)->dispatch();
        } catch (Exception $e) {
            BugReport::create([
                'title' => get_class($e),
                'body' => $e->getMessage(),
            ]);
        }
    }

    protected function getSheetFromS3()
    {
        // Retrieve file from S3 Storage
        $fileContents = Storage::disk('s3')->get($this->storage_path);

        // Create a temporary file path on your local server
        $tempFilePath = tempnam(sys_get_temp_dir(), 'excel');

        // Save the file contents to the temporary file
        file_put_contents($tempFilePath, $fileContents);

        /**  Identify the type of $inputFileName  **/
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tempFilePath);
        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);
        /**  Load $inputFileName to a Spreadsheet Object  **/

        return $reader->load($tempFilePath);
    }

    protected function parseWorksheet($worksheet)
    {
        foreach ($worksheet->getRowIterator() as $i => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            //    even if a cell value is not set.
            // For 'TRUE', we loop through cells
            //    only when their value is set.
            // If this method is not called,
            //    the default value is 'false'.
            $array[$i] = [];

            foreach ($cellIterator as $cell) {
                $calculetedValue=$cell->getOldCalculatedValue();

                if($calculetedValue=='' or $calculetedValue==NULL){
                    $value=$cell->getValue();
                }else{
                    $value=$cell->getOldCalculatedValue();
                }

                $array[$i][] = $value;
            }
        }

        /** Turn into Associative Array **/
        // Shift the keys from the first row:
        $keys = array_shift($array);
        $index = null;

        $named = [];
        // Loop and build remaining rows into a new array:

        foreach($array as $ln => $vals) {

            // Using specific index or row numbers?
            $key = !is_null($index) ? $vals[$index] : $ln;

            // Combine keys and values:
            $named[$key] = array_combine($keys, $vals);
        }
        /** End turning into Associative Array **/

        $named = array_map('array_filter', $named);
        return array_filter($named);
    }
}
