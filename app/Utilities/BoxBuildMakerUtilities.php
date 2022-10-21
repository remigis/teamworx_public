<?php

namespace App\Utilities;

use App\Http\Requests\CreateBoxBuildRequest;
use App\Models\BoxBuild;
use App\Models\BoxBuildRequiredItems;
use App\Rules\UniqueInArray;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BoxBuildMakerUtilities
{
    private CreateBoxBuildRequest $request;

    private array $sheetArray;

    private array $highest;

    private array $rulesForFileValidator;

    public BoxBuild $boxBuild;

    public BoxBuildRequiredItems $requiredItems;


    public function __construct(CreateBoxBuildRequest $request)
    {
        $this->request = $request;
        $this->setSheetArray();
        $this->validateRows();
        $this->validateFileName();
    }

    public function make(): bool
    {
        return $this->addToDatabase();
    }

    private function setSheetArray(): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly([$this->request->sheet_name]);
        $reader->setReadEmptyCells(false);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($this->request->file('file'));

        $sheet = $spreadsheet->getSheetByName($this->request->sheet_name);
        if (!$sheet) {
            abort(422, 'Sheet with this name not found');
        }

        $this->highest = $spreadsheet->getActiveSheet()->getHighestRowAndColumn();

        $sheet->getStyle('A1:' . $this->highest['column'] . $this->highest['row'])->getNumberFormat()->setFormatCode('0');
        $this->sheetArray = $sheet->rangeToArray('A2:' . $this->highest['column'] . $this->highest['row'], null, true, true, true);
    }

    private function validateFileName(): void
    {
        Validator::make(
            ['file_name' => $this->request->file('file')->getClientOriginalName()],
            ['file_name' => 'required|string|unique:mysql.box_builds,name']
        )->validate();

    }

    private function validateRows(): void
    {
        $productConditionColumnCells = [];

        $this->generateRulesFromRequest();

        foreach ($this->sheetArray as $rowNumber => $row) {
            Validator::make($row, $this->rulesForFileValidator)->validate();

            $productConditionColumnCells['product_with_or_without_condition'][$rowNumber] = $row[$this->request->product_condition_column];
        }

        //Check if all products exist in goodsflow database (validation time increases to 3min. now is 15 sec.)
        /*Validator::make($this->productsWithouCondition($productConditionColumnCells), [
            'row.*' => 'required|exists:mysql2.flow_artikel,artikelnummer',
            'row' => ['required', 'array']],
            [
                'row.*.required' => 'Product/condition cell is empty in :attribute.',
                'row.*.exists' => 'Product not found in GoodsFlow database. :attribute.',
            ]
        )
            ->validate();*/

        Validator::make($productConditionColumnCells, [
            'product_with_or_without_condition' => [new UniqueInArray()],
        ])->validate();
    }

    private function generateRulesFromRequest(): void
    {

        $centers = [];

        foreach ($this->request->fulfilment_centers as $fulfilment_center) {
            $centers[$fulfilment_center['column']] = 'numeric|nullable';

        }

        $inputs = [
            $this->request->manufacturer => 'required|string',
            $this->request->vid_column => 'required|string',
            $this->request->product_condition_column => 'required|string',
        ];

        $this->rulesForFileValidator = array_merge($centers, $inputs);
    }

    private function addToDatabase(): bool
    {
        Lock::db(['box_builds', 'box_build_required_items']);
        $this->boxBuild = new BoxBuild();
        $this->boxBuild->name = $this->request->file('file')->getClientOriginalName();
        $this->boxBuild->save();


        foreach (array_chunk($this->generateInsertArray(), 3000) as $chunk) {
            $result = BoxBuildRequiredItems::insert($chunk);
        }

        Lock::remove();

        return $result;
    }

    private function generateInsertArray(): array
    {
        $priority = 0;
        $insertArray = [];
        foreach ($this->request->fulfilment_centers as $fulfilment_center) {
            ++$priority;
            foreach ($this->sheetArray as $rowValues) {

                $need = $rowValues[$fulfilment_center['column']] ?? 0;

                $insertArray[] = [
                    'vid' => $rowValues[$this->request->vid_column],
                    'manufacturer' => $rowValues[$this->request->manufacturer],
                    'warehouse_center_id' => $fulfilment_center['id'],
                    'product_condition' => $rowValues[$this->request->product_condition_column],
                    'box_build_id' => $this->boxBuild->id,
                    'required' => $need,
                    'need' => $need,
                    'priority' => $priority,
                ];
            }
        }
        return $insertArray;
    }


}
