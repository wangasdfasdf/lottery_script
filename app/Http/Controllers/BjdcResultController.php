<?php

namespace App\Http\Controllers;

use App\Models\Filter\BjdcResultFilter;
use App\Services\BjdcResultService;
use Exception;
use Illuminate\Http\Request;
use Wang9707\MakeTable\Exceptions\TipsException;
use Wang9707\MakeTable\Response\Response;

class BjdcResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param BjdcResultFilter $filter
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, BjdcResultFilter $filter): \Illuminate\Http\Response
    {
        $data = BjdcResultService::instance()->getResourceList($filter, $request, []);

        return Response::success($data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        $data = $request->all();
        BjdcResultService::instance()->create($data);

        return Response::success();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): \Illuminate\Http\Response
    {
        $info = BjdcResultService::instance()->getById($id);

        return Response::success($info);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): \Illuminate\Http\Response
    {
        $data = $request->all();
        BjdcResultService::instance()->updateById($id, $data);

        return Response::success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws TipsException
     */
    public function destroy(int $id): \Illuminate\Http\Response
    {
        BjdcResultService::instance()->deleteById($id);

        return Response::success();
    }
}
