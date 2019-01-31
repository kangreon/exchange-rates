<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30.01.2019
 * Time: 14:03
 */

namespace App\CBR;

use App\Currency;
use App\Rate;
use Carbon\Carbon;
use GuzzleHttp\Client;

class CBRParser
{
    private const GET_RATES = 'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=%s&date_req2=%s&VAL_NM_RQ=%s';
    private $lastErrorMessage = "";

    private function getRatesURI($startDate, $endDate, $currency) {
        return sprintf( CBRParser::GET_RATES, $startDate, $endDate, $currency);
    }

    function parseResponse($source) {
        $xml = new \SimpleXMLElement($source);

        $list = Array();
        for($i = 0; $i < $xml->count(); $i++) {
            $date = (string)$xml->Record[$i]->attributes()->Date;

            // Exchange rate
            $value = (string)$xml->Record[$i]->Value;
            $valueFloat = (float)str_replace(',', '.', $value);

            $list[] = Array('date' => $date, 'value' => $valueFloat);
        }

        return $list;
    }

    function getRatesOfMouth($currency) {
        $date = Carbon::now();
        $date2Format = $date->format('d/m/Y');
        $date->addMonth(-1);
        $date1Format = $date->format('d/m/Y');

        $uri = $this->getRatesURI($date1Format, $date2Format, $currency);

        $client = new Client();
        try {
            $response =  $client->get($uri);
            $source = (string)$response->getBody();
            return $this->parseResponse($source);

        } catch(\Throwable $e) {
            $this->lastErrorMessage = $e->getMessage();
            return false;
        }
    }

    function saveRates($currencyName, $rates) {
        $currency = Currency::where("code", $currencyName)->first();
        if (!$currency) {
            $this->lastErrorMessage = "Currency \"$currencyName\" not exist in DB";
            return false;
        }

        $currencyRecord = $currency->rates();
        foreach ($rates as $item) {
            $rate = Rate::firstOrNew([
                'date' => $item['date'],
                'currency_id' => $currency->id
            ]);
            $rate->value = $item['value'];

            $currencyRecord->save($rate);
        }

        return true;
    }

    function getData() {
        $items = Currency::all();
        foreach ($items as $item) {
            $response = $this->getRatesOfMouth($item->code);

            if ($response) {
                $this->saveRates($item->code, $response);
            } else {
                $this->lastErrorMessage = "Error getting rates: " . $this->lastErrorMessage;
                return false;
            }
        }

        return true;
    }

}
