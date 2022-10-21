<?php

namespace App\Utilities;

use App\Models\RequiredList;

class RequiredListsCheck
{
    private array $requiredListsWithItems;

    private array $productIdsWeNeed;

    private string $scannedRz;

    private array $answer;

    public function __construct($scannedRz)
    {
        $this->scannedRz = $scannedRz;
        $this->answer = $this->emptyAnswer();
        $this->getRequiredListsWithItems();
        $this->productIdsWeNeed = $this->getProductIdsWeNeed();
        $this->buildAnswer();
    }

    public function answer()
    {
        return $this->answer;
    }

    private function getRequiredListsWithItems(): void
    {
        $this->requiredListsWithItems = RequiredList::whereActive(true)->with('requiredListItems', function ($query) {
            return $query->where('count', '>', 0);
        })->orderBy('priority', 'ASC')->get()->toArray();
    }

    private function emptyAnswer(): array
    {
        return [
            'scannedRz' => $this->scannedRz,
            'need' => false,
            'idOfList' => null,
        ];
    }

    private function getProductIdsWeNeed(): array
    {
        $productsWeNeed = [];
        foreach ($this->requiredListsWithItems as $requiredListWithItems) {
            foreach ($requiredListWithItems['required_list_items'] as $requiredListItem) {
                if (!in_array($requiredListItem['rz'], $productsWeNeed))
                    $productsWeNeed[] = $requiredListItem['rz'];
            }
        }
        return $productsWeNeed;
    }

    private function buildAnswer()
    {
        if (in_array($this->scannedRz, $this->productIdsWeNeed)) {
            $this->answer['need'] = true;
            $this->answer['idOfList'] = $this->getIdOfList();
        }

    }

    private function getIdOfList()
    {
        foreach ($this->requiredListsWithItems as $requiredListWithItems) {
            foreach ($requiredListWithItems['required_list_items'] as $requiredListItem) {
                if ($requiredListItem['rz'] == $this->scannedRz)
                    return $requiredListWithItems['id'];
            }
        }
    }
}
