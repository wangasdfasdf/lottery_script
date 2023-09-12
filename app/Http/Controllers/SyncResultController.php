<?php

namespace App\Http\Controllers;

use App\Models\BjdcResult;
use App\Models\MatchResult;
use App\Models\PlsResult;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Wang9707\MakeTable\Response\Response;

class SyncResultController extends Controller
{
    public function bjdc(Request $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $id = $request->input('id');
        $data = BjdcResult::query()->where('id', '>', $id)->get();

        return Response::success($data);
    }

    public function pls(Request $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $id = $request->input('id');
        $data = PlsResult::query()->where('id', '>', $id)->get();

        return Response::success($data);
    }


    public function jc (Request $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $id = $request->input('id');
        $data = MatchResult::query()->where('id', '>', $id)->get();

        return Response::success($data);
    }

}
