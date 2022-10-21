<?php

namespace App\Utilities;

use App\Events\NewScanFileUpload;
use App\Models\NewItemScan\ItemToScan;

class NewItemScanGenerator
{
    public array $databaseArrays;
    private int $scanId;
    private array $rows;
    private NewScanFileUpload $broadcast;

    public function __construct(array $rows, int $scanId, $broadcast)
    {
        $this->rows = $rows;
        $this->scanId = $scanId;
        $this->broadcast = $broadcast;
        $this->createDatabaseArray();

    }

    public function generate()
    {
        $chunks = 0;
        foreach ($this->databaseArrays as $bdArray) {
            ItemToScan::insert($bdArray);
            $chunks++;
            broadcast($this->broadcast->setChunksUploaded($chunks));
        }
        broadcast($this->broadcast->next('fileUploadingChunks'));

        return true;

    }

    protected function createDatabaseArray(): void
    {
        $chunks = 1;
        $rowsInChunk = 0;
        foreach ($this->rows as $row) {
            $this->databaseArrays[$chunks][] = [
                'new_item_scan_id' => $this->scanId,
                'product' => $row[1],
                'product_description' => $row[2],
                'serial_number' => $row[3],
                'defect' => $row[4],
                'date_from_distributor' => $row[5],
                'date_from_customer' => $row[6],
                'remarks' => $row[7],
                'product_checked' => $row[8],
                'description' => $row[9],
                'remarks_product_code' => $row[10],
                'remarks_defect_description' => $row[11],
                'remarks_sn' => $row[12],
                'status' => $row[13],
                'qty' => $row[14],
            ];
            $rowsInChunk++;
            if ($rowsInChunk >= 1500) {
                $rowsInChunk = 0;
                broadcast($this->broadcast->setChunksCreated($chunks));
                $chunks++;
            }
        }
        broadcast($this->broadcast->setChunksCreated($chunks));
        broadcast($this->broadcast->next('fileMakingChunks'));

    }
}
