<?php

namespace App\Http\Controllers;

use App\Models\NazivServisa;
use App\Models\Partner;
use App\Models\Tehnologije;
use App\Models\TipServisa;
use App\Models\TipUgovora;
use App\Models\Ugovor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    private $data = [];
    private function homePageData($homeType){
        $this->data = [];

        $this->data['homeType'] = $homeType == null ? 1 : $homeType;
        $this->data['nazivi_servisa'] = NazivServisa::all();
        $this->data['tipovi_ugovora'] = TipUgovora::all();
        $this->data['partneri'] = Partner::all();
        $this->data['tipovi_servisa'] = TipServisa::all();
        $this->data['tipovi_tehnologije'] = Tehnologije::all();
        $this->data['sviUgovori'] = Ugovor::all();
    }


    public function loginPage(){
        return view('pages.login');
    }

    public function test(){
        //return dd(Auth::user()->getUloga->naziv);
        //return (Auth::user()->ime.' '.Auth::user()->prezime.' => '.Auth::user()->getUloga->naziv);
        return view('admin.stavkefakture');
    }

    public function home($homeType = null){
        $this->homePageData($homeType);
        return view('pages.home', $this->data);
    }

    public function search(Request $request, $homeTypeVar = null){
        $homeType = intval($homeTypeVar == null ? $request->input('view') : $homeTypeVar);
        $this->homePageData($homeType);

        //izvrsiti pretragu
        /*$pretraga = Ugovor::where([
            ''
            //'naziv_ugovra' => $request->input('pretraga') == null ? $request->input('nazivUgovora') : $request->input('pretraga'),
            //'naziv_ugovra' => $request->input('pretraga'),
            'broj_ugovora' => $request->input('brojUgovora'),
            'naziv_kupac' => $request->input('nazivKorisnika'),
            'connectivity_plan' => $request->input('connPlan'),
            'id_user' => $request->input('idKorisnika'),
            //'kam' => $request->input('kam'),
            'pib' => $request->input('pib'),
            'segment' => $request->input('segment')
        ])->get();*/

        //return dd();

        $datum_pretraga = $request->input('datumPotpisa');


        $pretraga = Ugovor::where('naziv_ugovra', 'LIKE', '%'.$request->input('pretraga').'%')
            ->where('naziv_ugovra', 'LIKE', '%'.$request->input('nazivUgovora').'%')
            ->where('broj_ugovora', 'LIKE', '%'.$request->input('brojUgovora').'%')
            ->where('naziv_kupac', 'LIKE', '%'.$request->input('nazivKorisnika').'%')
            ->where('connectivity_plan', 'LIKE', '%'.$request->input('connPlan').'%')
            ->where('id_kupac', 'LIKE', '%'.$request->input('idKorisnika').'%')
            ->where('pib', 'LIKE', '%'.$request->input('pib').'%')
            ->where('segment', 'LIKE', '%'.$request->input('segment').'%')
            ->where('kam', 'LIKE', '%'.$request->input('kam').'%')
            ->when($datum_pretraga, function ($query, $datum_pretraga){
                $query->whereDate('datum_potpisivanja', '=', date('Y-m-d',strtotime($datum_pretraga)));
            })
            //->whereDate('datum_potpisivanja', '=', date('Y-m-d',strtotime($request->input('datumPotpisa'))))
            ->get();

        return view('pages.search', $this->data);
    }
}
