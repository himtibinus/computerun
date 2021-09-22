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
     * Utility function to fetch the YAML file
     *
     * @param  int  $id
     * @return array
     */
    public static function parseYamlFromPage($id)
    {
        $index_path = public_path() . '/docs/2021/pages/' . $id . '/index.yml';
        if (!file_exists($index_path)){
            return false;
        }
        return Yaml::parse(file_get_contents($index_path));
    }

    /**
     * Utility function to get excerpt from a valid document
     *
     * @param  int  $id
     * @return array
     */
    public static $plaintextProperties = ['kicker', 'title', 'description', 'text'];
    public static function getExcerptFromWidget($widget, $recursive, $limit)
    {
        $result = '';

        if (isset($widget['type']) && $widget['type'] === 'markdown'){
            if (isset($widget['file']) && !isset($widget['text']) && file_exists(public_path() . $widget['file'])){
                $widget['text'] = strip_tags(new \Parsedown())->text(file_get_contents(public_path() . $widget['file']));
            } else $widget['text'] = strip_tags((new \Parsedown())->text($widget['text']));
        }

        foreach(PagesController::$plaintextProperties as $property) if (isset($widget[$property])){
            $result .= $widget[$property] . ' ';
            $limit -= strlen($widget[$property]) + 1;
        }

        // Remove last space
        if (strlen($result) > 0){
            $result = substr($result, 0, -1);
            $limit++;
        }
        if ($limit < 0) $result = substr($result, 0, $limit - 1) . '…';
        else if (($recursive === true || is_int($recursive)) && isset($widget['children'])){
            foreach ($widget['children'] as $c){
                $extracted = PagesController::getExcerptFromWidget($c, $recursive, $limit);
                if (strlen($extracted) > 0 && strlen($result) > 0) $extracted = ' ' . $extracted;
                $result .= $extracted;
                $limit -= strlen($extracted);
                if ($limit < 0){
                    $result = substr($result, ($recursive === true) ? true : ($recursive - 1), $limit - 1) . '…';
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Check whether the YAML file is valid
        $index_data = $this->parseYamlFromPage($id);
        if ($index_data === false || !isset($index_data['@manifest'])){
            abort(404);
            return;
        }
        // Return view with unparsed data (parsing is done on view)
        return view('components.page', $index_data);
    }
}
