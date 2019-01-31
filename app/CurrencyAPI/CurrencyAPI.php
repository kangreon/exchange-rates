<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30.01.2019
 * Time: 21:07
 */

namespace App\CurrencyAPI;


use App\Currency;
use App\Rate;
use Carbon\Carbon;

class CurrencyAPI
{
    private $lastError;
    private $currencyName;

    function getOtherRates($currencyId, $start, $finish) {
        return Rate::where('currency_id', $currencyId)
            ->where('date', '>=', $start)
            ->where('date', '<=', $finish)
            ->orderBy('date', 'asc')
            ->get();
    }

    function getList(Carbon $startDate, $finishDate, $currencyCode) {
        $currency = Currency::where("code", $currencyCode)->first();
        if (!$currency) {
            $this->lastError = "Currency with code \"$currencyCode\" not exist.";
            return false;
        }

        $this->currencyName = $currency->name;
        $rates = $this->getOtherRates($currency->id, $startDate, $finishDate)->all();

        $result = [];
        foreach ($rates as $item) {
            $result[] = [
                'value' => $item->value,
                'date' => $item->date
            ];
        }

        if (count($result) === 0) {
            $this->lastError = "Elements not found.";
        }


        return $result;
    }

    function getCurrencies() {
        $items = Currency::all();
        if (!$items) {
            return false;
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'name' => $item['name'],
                'code' => $item['code']
            ];
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @return mixed
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }
}
