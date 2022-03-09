<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\LokacijaApp;
use App\Models\NazivServisa;
use App\Models\Partner;
use App\Models\StavkaFakture;
use App\Models\Tehnologije;
use App\Models\TipServisa;
use App\Models\TipUgovora;
use App\Models\Ugovor;
use App\Models\User;
use App\Models\VrstaSenzora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    private $data = [];
    private function homePageData($homeType){
        $this->data = [];

        $this->data['homeType'] = $homeType == null ? 1 : $homeType;
        $this->data['nazivi_servisa'] = NazivServisa::wherePrikazi(true)->get();
        $this->data['tipovi_ugovora'] = TipUgovora::wherePrikazi(true)->get();
        $this->data['partneri'] = Partner::wherePrikazi(true)->get();
        $this->data['tipovi_servisa'] = TipServisa::wherePrikazi(true)->get();
        $this->data['tipovi_tehnologije'] = Tehnologije::wherePrikazi(true)->get();
        $this->data['sviUgovori'] = Ugovor::whereDekativiran(false)->get();
    }


    public function loginPage(){
        return view('pages.login');
    }

    public function home($homeType = null){
        $this->homePageData($homeType);
        return view('pages.home', $this->data);
    }

    public function search(Request $request, $homeTypeVar = null){
        $homeType = intval($homeTypeVar == null ? $request->input('view') : $homeTypeVar);
        $this->homePageData($homeType);

        $datum_pretraga = $request->input('datumPotpisa');
        $tehnologija_id = $request->input('tehnologija');
        $partner_id = $request->input('partner');

        $ugovori = Ugovor::join('tehnologije_ugovor', 'tehnologije_ugovor.id_ugovor', '=', 'ugovor.id')
            ->join('partner_ugovor', 'partner_ugovor.id_ugovor', '=', 'ugovor.id')
            ->where('naziv_ugovra', 'LIKE', '%'.$request->input('pretraga').'%')
            ->where('naziv_ugovra', 'LIKE', '%'.$request->input('naziv_ugovra').'%')
            ->where('broj_ugovora', 'LIKE', '%'.$request->input('broj_ugovora').'%')
            ->where('naziv_kupac', 'LIKE', '%'.$request->input('naziv_kupac').'%')
            ->where('connectivity_plan', 'LIKE', '%'.$request->input('connectivity_plan').'%')
            ->where('id_kupac', 'LIKE', '%'.$request->input('id_kupac').'%')
            ->where('pib', 'LIKE', '%'.$request->input('pib').'%')
            ->where('segment', 'LIKE', '%'.$request->input('segment').'%')
            ->where('kam', 'LIKE', '%'.$request->input('kam').'%')
            ->when($datum_pretraga, function ($query, $datum_pretraga){
                $query->whereDate('datum_potpisivanja', '=', date('Y-m-d',strtotime($datum_pretraga)));
            })
            ->when($tehnologija_id, function ($query, $tehnologija_id){
                $query->where('id_tehnologije', '=', $tehnologija_id);
            })
            ->when($partner_id, function ($query, $partner_id){
                $query->where('id_partner', '=', $partner_id);
            })
            ->get();


        $this->data['ugovori'] = $ugovori;

        $this->data['pretraga'] = $request->input('pretraga');
        $this->data['naziv_ugovora'] = $request->input('naziv_ugovora');
        $this->data['naziv_servisa'] = $request->input('naziv_servisa');
        $this->data['broj_ugovora'] = $request->input('broj_ugovora');
        $this->data['naziv_kupac'] = $request->input('naziv_kupac');
        $this->data['connectivity_plan'] = $request->input('connectivity_plan');
        $this->data['tip_ugovora'] = $request->input('tip_ugovora');
        $this->data['partner'] = $request->input('partner');
        $this->data['datum_potpisa'] = $request->input('datum_potpisa');
        $this->data['id_kupac'] = $request->input('id_kupac');
        $this->data['kam'] = $request->input('kam');
        $this->data['tip_servisa'] = $request->input('tip_servisa');
        $this->data['tehnologija'] = $request->input('tehnologija');
        $this->data['uo'] = $request->input('uo');
        $this->data['pib'] = $request->input('pib');
        $this->data['segment'] = $request->input('segment');

        return view('pages.search', $this->data);
    }

    public function addNewContract(){
        $this->homePageData(null);
        $this->data['lokacije_app'] = LokacijaApp::wherePrikazi(true)->get();
        $this->data['vrste_senzora'] = VrstaSenzora::wherePrikazi(true)->get();
        $this->data['stavke_fakture'] = StavkaFakture::wherePrikazi(true)->get();
        return view('pages.addNewContract', $this->data);
    }
    public function addNewUser($id = null){
        if($id){
            $this->data['korisnik'] = UserResource::make(User::whereId($id)->first())->resolve();
        }
        $this->data['korisnici'] = UserResource::collection(User::whereDeaktiviran(false)->get())->resolve();
        //return dd($this->data);
        return view('admin.addnewuser', $this->data);
    }

    public function dodajStavkuFakture($id = null){
        if($id){
            $this->data['stavka_fakture'] = StavkaFakture::whereId($id)->first();
        }
        $this->data['stavke_fakture'] = StavkaFakture::wherePrikazi(true)->get();
        return view('systemManaging.stavkafakture', $this->data);
    }
    public function dodajTipUgovora($id = null){
        if($id){
            $this->data['tip_ugovora'] = TipUgovora::whereId($id)->first();
        }
        $this->data['tipovi_ugovora'] = TipUgovora::wherePrikazi(true)->get();
        return view('systemManaging.tipugovora', $this->data);
    }
    public function dodajTipServisa($id = null){
        if($id){
            $this->data['tip_servisa'] = TipServisa::whereId($id)->first();
        }
        $this->data['tipovi_servisa'] = TipServisa::wherePrikazi(true)->get();
        return view('systemManaging.tipservisa', $this->data);
    }
    public function dodajTehnologije($id = null){
        if($id){
            $this->data['tip_tehnologije'] = Tehnologije::whereId($id)->first();
        }
        $this->data['tipovi_tehnologija'] = Tehnologije::wherePrikazi(true)->get();
        return view('systemManaging.tehnologija', $this->data);
    }
    public function dodajPartnera($id = null){
        if($id){
            $this->data['partner'] = Partner::whereId($id)->first();
        }
        $this->data['partneri'] = Partner::wherePrikazi(true)->get();
        return view('systemManaging.partner', $this->data);
    }
    public function dodajServis($id = null){
        if($id){
            $this->data['servis'] = NazivServisa::whereId($id)->first();
        }
        $this->data['servisi'] = NazivServisa::wherePrikazi(true)->get();
        return view('systemManaging.servis', $this->data);
    }
    public function dodajVrstuSenzora($id = null){
        if($id){
            $this->data['vrsta_senzora'] = VrstaSenzora::whereId($id)->first();
        }
        $this->data['vrste_senzora'] = VrstaSenzora::wherePrikazi(true)->get();
        return view('systemManaging.vrstasenzora', $this->data);
    }
    public function dodajLokacijuApp($id = null){
        if($id){
            $this->data['loakcija_app'] = LokacijaApp::whereId($id)->first();
        }
        $this->data['lokacije_app'] = LokacijaApp::wherePrikazi(true)->get();
        return view('systemManaging.lokacijaapp', $this->data);
    }
}
