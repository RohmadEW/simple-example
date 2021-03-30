<?php

namespace App\Http\Controllers\Web\Backend\Admin\PSB;

use App\Http\Controllers\Controller;
use App\Listeners\LoginSuccess;
use App\Models\PSB\TA;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class TAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('TA/Index');
    }

    public function list()
    {
        $model = TA::query();

        return DataTables::of($model)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('TA/Form', [
            'currentNamePage' => 'psb-ta'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);

        $ta = TA::create($request->all());
        $this->changeTASession($ta);

        return response(["status" => true, "msg" => "Berhasil menyimpan data"]);
    }

    private function validation($request, $id = null)
    {
        $this->validate($request, [
            'nama' => 'required',
            'kode' => 'required',
        ]);
    }

    public function changeTASession($ta)
    {
        if ($ta->aktif) {
            TA::where('id', '<>', $ta->id)->update([
                'aktif' => false
            ]);

            LoginSuccess::session();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Inertia::render('TA/Form', [
            'currentNamePage' => 'psb-ta',
            'ta' => TA::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validation($request, $id);

        $ta = TA::find($id);
        $ta->update($request->all());
        $this->changeTASession($ta);

        if ($ta->aktif) {
            TA::where('id', '<>', $ta->id)->update([
                'aktif' => false
            ]);
        }

        return response(["status" => true, "msg" => "Berhasil menyimpan data"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ta = TA::find($id);
        if (!$ta->aktif) {
            $ta->delete();

            return response([
                "status" => true,
                "msg" => "Berhasil menghapus data"
            ]);
        }
    }
}
