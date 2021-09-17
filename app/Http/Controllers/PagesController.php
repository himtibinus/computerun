<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Check whether $id/index.yml exists
        $index_path = public_path() . '/docs/2021/pages/' . $id . '/index.yml';
        if (!file_exists($index_path)){
            abort(404);
            return;
        }
        // Check whether the YAML file is valid
        $index_data = Yaml::parse(file_get_contents($index_path));
        if ($index_data === false || !isset($index_data['@manifest'])){
            abort(404);
            return;
        }
        // Return view with unparsed data (parsing is done on view)
        return view('components.page', $index_data);
    }
}
