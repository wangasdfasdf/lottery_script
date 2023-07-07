<?php

namespace App\Http\Controllers;

use App\Models\Filter\BjdcResultFilter;
use App\Services\BjdcResultService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Wang9707\MakeTable\Exceptions\TipsException;
use Wang9707\MakeTable\Response\Response;

class WalletController extends Controller
{
    private string $url = 'https://apilist.tronscanapi.com/api/token_trc20/transfers';


    public function index(Request $request)
    {
        $data = $request->all();

        $result = Http::get($this->url, $data);
        $result = $result->body();


        return \json_decode($result, true);

    }
}
