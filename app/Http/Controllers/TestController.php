<?php

namespace App\Http\Controllers;

use App\CBR\CBRParser;
use App\CurrencyAPI\CurrencyAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    function returnData($data, $errorText = "", $custom = []) {
        if (!$data) {
            $result = [
                "status" => "fail",
                "description" => $errorText
            ];
        } else {
            $result = [
                "status" => "ok",
                "data" => $data
            ];

            if ($custom) {
                $result = array_merge($result, $custom);
            }
        }

        return $result;
    }

    function test() {
        $parser = new CBRParser();
        return (string)$parser->getData();
    }

    function test2() {
        $api = new CurrencyAPI();
        $data = $api->getCurrencies();
        return $this->returnData($data);
    }

    function test3(Request $request, $code = "", $start = "", $finish = "") {

        $validator = Validator::make($request->route()->parameters(), [
            'code' => 'required',
            'start' => 'required|date|date_format:d.m.Y',
            'finish' => 'required|date|date_format:d.m.Y|after_or_equal:start'
        ]);

        if ($validator->fails()) {
            return $this->returnData(false, $validator->errors());
        }

        $api = new CurrencyAPI();
        $data = $api->getList(
            Carbon::createFromFormat('d.m.Y', $start),
            Carbon::createFromFormat('d.m.Y', $finish),
            $code
        );

        return $this->returnData($data, $api->getLastError(), [
            'name' => $api->getCurrencyName()
        ]);
    }
}
