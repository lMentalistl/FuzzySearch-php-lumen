<?php


namespace App\Http\Controllers;

use App\Airport;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use function Sodium\add;


class FuzzySearchController extends Controller
{
    private $airports;
    public function __construct()
    {
        apcu_clear_cache();
        $this->getAirports();
    }

    public function runFuzzySearch(Request $request)
    {
        $airport = mb_strtolower($request->get('airport'));

        $result = null;
        if(apcu_exists($airport))
        {
            $result = apcu_fetch($airport);
        }
        else
        {
            $result = $this->FuzzySearch($airport);
            apcu_add($airport, $result);
        }

        return response()->json(json_encode($result),200)->withHeaders(['Content-Type' => 'application/json']);
    }
    private function getAirports() //Получаем json данные в виде коллекции
    {
        if(apcu_exists('airportsList'))
        {

        }
        else{
            $file_path = realpath(__DIR__.'./../../../database/airports.json');
            $json = json_decode(file_get_contents($file_path), true);
            apcu_add('airportsList', $json);
        }
    }
    private function defineLanguage($input){
        if (preg_match("/^[a-z]+$/",$input))
        {
            return 'en';
        }
        else{
            return 'ru';
        }
        return 'ru';
    }
    private function FuzzySearch($input) //нечеткий поиск Левенштайна
    {

        $shortest = -1; //ближайшее расстояние Левенштайна
        $closest = []; //наиболее подходящее слово
        $allSimilars = [];
        $lng = $this->defineLanguage($input);
        foreach (apcu_fetch('airportsList') as $i =>$value)
        {

            $levDistance = levenshtein($input, mb_strtolower($value["cityName"][$lng])); //Расстояние Левенштайна
            $airport = $value;
            $airport['id'] = $i;

            if($levDistance == 0){
                $closest = $airport;
                $shortest = 0;
                //break;
            }
            if($levDistance <= $shortest || $shortest < 0)
            {
                $closest = $airport;
                $shortest = $levDistance;
            }
            if($levDistance <=2)
            {
                array_push($allSimilars, $airport);
            }

        }

        return ['closest'=>$closest, 'all'=>$allSimilars];
    }

}
