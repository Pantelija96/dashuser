<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Http\Requests\InsertRequest;
use App\Http\Requests\LoginRequest;
use App\Models\KomercijalniUslovi;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use SoapClient;
use Symfony\Component\HttpFoundation\Response;

class BackendController extends Controller
{
    public function login(LoginRequest $request){
        //proveriti da li je korisnik u lokalnoj bazi -> korinsik
        //ako nije onda traziti na ldap-u
        //ako ga nema ni tamo izbaciti error

        $validated = $request->validated();

        if(Auth::attempt(['email' => $validated['email'], 'password' => $validated['lozinka'], 'deaktiviran' => false])){
            return redirect('/home');
        }
        else{
            //proveriti na ldap-u
            return redirect('/')->withErrors('Korisnik nije pronadjen!');
        }
    }
    public function logout(){
        Session::flush();
        return redirect('/');
    }

    public function addNewContract(Request $request){
        //return dd($request->all());
        $request->validate([
            'id_kupac' => 'required',
            'connectivity_plan' => 'required',
            'naziv_kupac' => 'required',
            'pib' => 'required',
            'mb' => 'required',
            'email' => 'required',
            'telefon' => 'required',
            'kam' => 'required',
            'segment' => 'required',

            "partner" => "required",
            "naziv_ugovora" => "required",
            "tip_servisa" => "required",
            "naziv_servisa" => "required",
            "tip_ugovora" => "required",
            "broj_ugovora" => "required",
            "datum" => "required",
            "datum_data" => "required",
            "zbirni_racun" => "required",
            "uo" => "required",
            "tip_tehnologije" =>"required",
            "vrsta_senzora" => "required",
            "lokacija_app" => "required"
        ]);
        $id_ugovor = 0;
        try {
            $id_ugovor = DB::table('ugovor')->
                insertGetId([
                    'id_user' => Auth::user()->id,
                    'id_tip_ugovora' => $request->input('tip_ugovora'),
                    'id_tip_servisa' => $request->input('tip_servisa'),
                    'id_naziv_servisa' => $request->input('naziv_servisa'),
                    'id_lokacija_app' => $request->input('lokacija_app'),
                    'connectivity_plan' => $request->input('connectivity_plan'),
                    'ip_adresa' => $request->input('ip_adresa'),
                    'naziv_servera' => $request->input('naziv_servera'),
                    'naziv_ugovra' => $request->input('naziv_ugovora'),
                    'broj_ugovora' => $request->input('broj_ugovora'),
                    'datum_potpisivanja' => date('Y-m-d H:i:s', strtotime($request->input('datum_data'))),
                    'ugovorna_obaveza' => $request->input('uo'),
                    'zbirni_racun' => $request->input('zbirni_racun'),
                    'napomena' => $request->input('napomena'),
                    'id_kupac' => $request->input('id_kupac'),
                    'naziv_kupac' => $request->input('naziv_kupac'),
                    'pib' => $request->input('pib'),
                    'mb' => $request->input('mb'),
                    'segment' => $request->input('segment'),
                    'email' => $request->input('email'),
                    'telefon' => $request->input('telefon'),
                    'kam' => $request->input('kam'),
                    'dekativiran' => false,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri dodavanju novog ugovora insert-ugovor-1 => ".$exception->getMessage());
            return redirect()->back()->with(['error' => "Desila se greska! Id greske : insert-ugovor-1"]);
        }

        if($id_ugovor > 0){
            foreach ($request->input('tip_tehnologije') as $id_tip_tehnologije){
                try{
                    DB::table('tehnologije_ugovor')
                        ->insert([
                            'id_tehnologije' => $id_tip_tehnologije,
                            'id_ugovor' => $id_ugovor
                        ]);
                }
                catch (\Exception $exception){
                    Log::error("Greska pri dodavanju novog ugovora insert-ugovor-2 => ".$exception->getMessage());
                    return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-ugovor-2"]);
                }
            }
            foreach ($request->input('partner') as $id_partner){
                try{
                    DB::table('partner_ugovor')
                        ->insert([
                            'id_partner' => $id_partner,
                            'id_ugovor' => $id_ugovor
                        ]);
                }
                catch (\Exception $exception){
                    Log::error("Greska pri dodavanju novog ugovora insert-ugovor-3 => ".$exception->getMessage());
                    return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-ugovor-3"]);
                }
            }
            foreach ($request->input('vrsta_senzora') as $id_vrsta_senzora){
                try{
                    DB::table('vrsta_senzora_ugovor')
                        ->insert([
                            'id_vrsta_senzora' => $id_vrsta_senzora,
                            'id_ugovor' => $id_ugovor
                        ]);
                }
                catch (\Exception $exception){
                    Log::error("Greska pri dodavanju novog ugovora insert-ugovor-4 => ".$exception->getMessage());
                    return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-ugovor-4 "]);
                }
            }
            if($request->input('aktivne_stavke') != null){
                //ima bar jedan komercijalni uslov
                $stavke = explode(",", $request->input('aktivne_stavke'));
                foreach ($stavke as $stavka){
                    if($request->input('stavka_fakture_'.$stavka) != null){
                        try{
                            $vrsta_senzora = intval(explode('|',$request->input('stavka_fakture_'.$stavka))[0]);
                            DB::table('komercijalni_uslovi')
                                ->insert([
                                    'id_user' => Auth::user()->id,
                                    'id_ugovor' => $id_ugovor,
                                    'id_vrsta_senzora' => $vrsta_senzora == 0 ? null : $vrsta_senzora,
                                    'id_stavka_fakture' => intval(explode('|',$request->input('stavka_fakture_'.$stavka))[1]),
                                    'datum_pocetak' => date('Y-m-d', strtotime($request->input('datum_pocetak_'.$stavka.'_data'))),
                                    'datum_kraj' => date('Y-m-d', strtotime($request->input('datum_kraj_'.$stavka.'_data'))),
                                    'naknada' => floatval($request->input('naknada_'.$stavka)),
                                    'status' => $request->input('status_').$stavka,
                                    'min' => intval($request->input('min_'.$stavka)),
                                    'max' => intval($request->input('max_'.$stavka)),
                                    'obrisana' => false,
                                    'uredjaj' => $request->input('uredjaj_' . $stavka) !== null,
                                    'sim_kartica' => $request->input('sim_'.$stavka) !== null,
                                    'id_user_obrisao' => 0
                                ]);
                        }
                        catch (\Exception $exception){
                            Log::error("Greska pri dodavanju novog ugovora insert-ugovor-5 => ".$exception->getMessage());
                            return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-ugovor-5"]);
                        }
                    }
                }
            }
            return redirect('/home');
        }
        else{
            Log::error("Greska pri dodavanju novog ugovora insert-ugovor-1");
            return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-ugovor-1"]);
        }

    }
    public function editContract(Request $request){
        //return dd($request->all());
        //postoji vec ugovor, ne dodaje se novi samo komercijalni uslovi
        if($request->input('aktivne_stavke') != null){
            //ima bar jedan komercijalni uslov
            $stavke = explode(",", $request->input('aktivne_stavke'));
            $id_ugovor = intval($request->input('id_ugovor'));
            foreach ($stavke as $stavka){
                if($request->input('id_komercijalni_uslov_'.$stavka) !== null){
                    //radi se update komercijalnog uslova
                    try {
                        $id_kom_uslov = intval($request->input('id_komercijalni_uslov_'.$stavka));
                        DB::table('komercijalni_uslovi')
                            ->where('id', '=', $id_kom_uslov)
                            ->update([
                                'datum_kraj' => date('Y-m-d', strtotime($request->input("datum_kraj_" . $stavka . "_data")))
                            ]);
                    }
                    catch (\Exception $exception){
                        Log::error("Greska pri editu postojecih komercijalnih uslova edit-ugovor-1 => ".$exception->getMessage());
                        return redirect()->back()->with(['greska' => "Desila se greska! Id greske : edit-ugovor-1"]);
                    }
                }
                else{
                    //radi se insert novog kom uslova
                    try{
                        $vrsta_senzora = intval(explode('|',$request->input('stavka_fakture_'.$stavka))[0]);
                        DB::table('komercijalni_uslovi')
                            ->insert([
                                'id_user' => Auth::user()->id,
                                'id_ugovor' => $id_ugovor,
                                'id_vrsta_senzora' => $vrsta_senzora == 0 ? null : $vrsta_senzora,
                                'id_stavka_fakture' => intval(explode('|',$request->input('stavka_fakture_'.$stavka))[1]),
                                'datum_pocetak' => date('Y-m-d', strtotime($request->input('datum_pocetak_'.$stavka.'_data'))),
                                'datum_kraj' => date('Y-m-d', strtotime($request->input('datum_kraj_'.$stavka.'_data'))),
                                'naknada' => floatval($request->input('naknada_'.$stavka)),
                                'status' => $request->input('status_').$stavka,
                                'min' => intval($request->input('min_'.$stavka)),
                                'max' => intval($request->input('max_'.$stavka)),
                                'obrisana' => false,
                                'uredjaj' => $request->input('uredjaj_' . $stavka) !== null,
                                'sim_kartica' => $request->input('sim_'.$stavka) !== null,
                                'id_user_obrisao' => 0
                            ]);
                    }
                    catch (\Exception $exception){
                        Log::error("Greska pri dodavanju novih komercijalnih uslova za posotjeci ugovor edit-ugovor-2 => ".$exception->getMessage());
                        return redirect()->back()->with(['greska' => "Desila se greska! Id greske : edit-ugovor-2"]);
                    }
                }
            }
            return redirect('/home');
        }
        return redirect()->back();
    }
    public function deleteKomUslov($id){
        try{
            $deleteResult = KomercijalniUslovi::where('id', $id)->update([
                'obrisana' => true,
                'datum_brisanja' => date("Y-m-d H:i:s"),
                'id_user_obrisao' => Auth::user()->id
            ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri brisanju komercijalnog uslova, greska: delete-kom-uslov-1 => ".$exception->getMessage());
            return response("Desila se greska! Id greske : delete-kom-uslov-1", Response::HTTP_BAD_REQUEST);
        }
        if($deleteResult){
            return response('',Response::HTTP_OK);
        }
        else{
            Log::error("Greska pri brisanju komercijalnog uslova, greska: delete-kom-uslov-1 => ".var_dump($deleteResult));
            return response("Desila se greska! Id greske : delete-kom-uslov-1", Response::HTTP_BAD_REQUEST);
        }
    }
    public function deaktivirajUgovor($id){
        try{
            $deleteResult = Ugovor::where('id', $id)->update([
                'dekativiran' => true
            ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri deaktivaciji ugovora delete-ugovor-1 => ".$exception->getMessage());
            return response("Desila se greska! Id greske : delete-ugovor-1", Response::HTTP_BAD_REQUEST);
        }
        if($deleteResult){
            return redirect('/home');
        }
        else{
            Log::error("Greska pri deaktivaciji ugovora delete-ugovor-2".var_dump($deleteResult));
            return response("Desila se greska! Id greske : delete-ugovor-2", Response::HTTP_BAD_REQUEST);
        }
    }

    public function addNewUser(Request $request){
        $request->validate([
            'ime' => 'required',
            'prezime' => 'required',
            'email' => 'required|email',
            'uloga' => 'required',
            'lozinka' => 'required',
            'lozinka_ponovo' => 'required'
        ]);

        try{
            $result = DB::table('users')
                ->insert([
                    'ime' => $request->input('ime'),
                    'prezime' => $request->input('prezime'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('lozinka')),
                    'id_uloga' => $request->get('uloga'),
                    'lastLogin' => date('Y-m-d H:i:s'),
                    'deaktiviran' => false
                ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri dodavanju korisnika id greske: korisnik-insert-1 => ".$exception->getMessage());
            return redirect()->back()->with(['greska' => "Greska pri dodavanju korisnika id greske: korisnik-insert-1."]);
        }
        if($result){
            return redirect('/addnewuser');
        }
        else{
            Log::error("Greska pri dodavanju korisnika id greske: korisnik-edit-1 => ");
            return redirect()->back()->with(['greska' => "Greska pri dodavanju korisnika id greske: korisnik-insert-1."]);
        }
    }
    public function editUser(Request $request){
        $request->validate([
            'id_korisnik' => 'required',
            'ime' => 'required',
            'prezime' => 'required',
            'email' => 'required|email',
            'uloga' => 'required'
        ]);

        try{
            $result = DB::table('users')->where('id','=',$request->input('id_korisnik'))
                ->update([
                    'ime' => $request->input('ime'),
                    'prezime' => $request->input('prezime'),
                    'email' => $request->input('email'),
                    'id_uloga' => $request->input('uloga')
                ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri editu korisnika id greske: korisnik-edit-1 => ".$exception->getMessage());
            return redirect()->back()->with(['greska' => "Greska pri editu korisnika id greske: korisnik-edit-1."]);
        }
        if($result){
            return redirect('/addnewuser');
        }
        else{
            Log::error("Greska pri editu korisnika id greske: korisnik-edit-1 => ");
            return redirect()->back()->with(['greska' => "Greska pri editu korisnika id greske: korisnik-edit-1."]);
        }
    }
    public function deleteUser($id){
        try{
            $deleteResult = User::where('id', $id)->update([
                'deaktiviran' => true
            ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri brisanju korisnika delete-korisnik-1 => ".$exception->getMessage());
            return response("Desila se greska! Id greske : delete-korisnik-1", Response::HTTP_BAD_REQUEST);
        }
        if($deleteResult){
            return response('',Response::HTTP_OK);
        }
        else{
            Log::error("Greska pri brisanju korisnika insert-korisnik-1".var_dump($deleteResult));
            return response("Desila se greska! Id greske : delete-korisnik-1", Response::HTTP_BAD_REQUEST);
        }
    }

    public function insert($text, $model, $naziv, $prikazi, $idGreske){
        try {
            $insertResult = $model::insert([
                'naziv' => $naziv,
                'prikazi' => $prikazi
            ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri dodavanju $text insert-$idGreske => ".$exception->getMessage());
            return redirect()->back()->with(['error' => "Desila se greska! Id greske : insert-$idGreske"]);
        }
        if($insertResult){
            return redirect()->back();
        }
        else{
            Log::error("Greska pri dodavanju $text insert-$idGreske");
            return redirect()->back()->with(['error' => "Desila se greska! Id greske : insert-$idGreske"]);
        }
    }
    public function dodajStavkuFakture(Request $request){
        $validated_data = $request->validate([
            'naziv' => ['required'],
            'naknada' => ['required'],
            'tip_naknade' => ['required']
        ]);

        try{
            $result = DB::table('stavka_fakture')->insert([
                'naziv' => $request->input('naziv'),
                'tip_naknade' => intval($request->input('tip_naknade')),
                'naknada' => floatval($request->input('naknada')),
                'zavisi_od_vrste_senzora' => $request->input('zavisi_od_vrste_senzora') !== null,
                'prikazi' => true
            ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri dodavanju stavke fakture insert-8 => ".$exception->getMessage());
            return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-8"]);
        }
        if($result){
            return redirect()->back();
        }
        else{
            Log::error("Greska pri dodavanju stavke fakture insert-8");
            return redirect()->back()->with(['greska' => "Desila se greska! Id greske : insert-8"]);
        }
    }
    public function dodajTipUgovora(InsertRequest $request){
        return $this->insert('tip ugovora', TipUgovora::class, $request->input('naziv'), true, 7);
    }
    public function dodajTipServisa(InsertRequest $request){
        return $this->insert('tip servisa', TipServisa::class, $request->input('naziv'), true, 6);
    }
    public function dodajTehnologije(InsertRequest $request){
        return $this->insert('tehnologija', Tehnologije::class, $request->input('naziv'), true, 5);
    }
    public function dodajPartnera(InsertRequest $request){
        return $this->insert('partner', Partner::class, $request->input('naziv'), true, 4);
    }
    public function dodajNazivServisa(InsertRequest $request){
        return $this->insert('servis', NazivServisa::class, $request->input('naziv'), true, 3);
    }
    public function dodajVrstuSenzora(InsertRequest $request){
        return $this->insert('vrsta senzora', VrstaSenzora::class, $request->input('naziv'), true, 2);
    }
    public function dodajLokacijuApp(InsertRequest $request){
        return $this->insert('lokacije aplikacije', LokacijaApp::class, $request->input('naziv'), true, 1);
    }

    public function delete($model, $id, $error_id, $text){
        try{
            $deleteResult = $model::where('id', $id)->update([
                'prikazi' => false
            ]);
        }
        catch (\Exception $exception){
            Log::error("Greska pri brisanju $text delete-$error_id => ".$exception->getMessage());
            return response("Desila se greska! Id greske : delete-$error_id", Response::HTTP_BAD_REQUEST);
        }
        if($deleteResult){
            return response('',Response::HTTP_OK);
        }
        else{
            Log::error("Greska pri brisanju $text insert-$error_id".var_dump($deleteResult));
            return response("Desila se greska! Id greske : delete-$error_id", Response::HTTP_BAD_REQUEST);
        }
    }
    public function deleteStavkaFakture($id){
        return $this->delete( StavkaFakture::class, $id, 8, 'stavka fakture');
    }
    public function deleteTipUgovora($id){
        return $this->delete(TipUgovora::class, $id, 7, 'tip ugovora');
    }
    public function deleteTipServisa($id){
        return $this->delete(TipServisa::class, $id, 6, 'tip servisa');
    }
    public function deleteTehnologije($id){
        return $this->delete(Tehnologije::class, $id, 5, 'tip servisa');
    }
    public function deletePartnera($id){
        return $this->delete(Partner::class, $id, 4, 'partner');
    }
    public function deleteServis($id){
        return $this->delete(NazivServisa::class, $id, 3, 'naziv servisa');
    }
    public function deleteVrstuSenzora($id){
        return $this->delete(VrstaSenzora::class, $id, 2, 'vrsta senzora');
    }
    public function deleteLokacijuApp($id){
        return $this->delete(LokacijaApp::class, $id, 1, 'lokacije aplikacije');
    }

    public function edit($model, $id, $error_id, $text, $data, $url){
        try{
            $editResult = $model::where('id', $id)->update($data);
        }
        catch (\Exception $exception){
            Log::error("Greska pri editu $text edit-$error_id => ".$exception->getMessage());
            return redirect()->back()->with(['error' => "Desila se greska! Id greske : edit-$error_id"]);
        }
        if($editResult){
            return redirect('/menage/'.$url);
        }
        else{
            Log::error("Greska pri editu $text edit-$error_id");
            return redirect()->back()->with(['error' => "Desila se greska! Id greske : edit-$error_id"]);
        }
    }
    public function editStavkaFakture(Request $request){
        $validated_data = $request->validate([
            'id_stavka_fakture' => 'required',
            'naziv' => 'required',
            'naknada' => 'required',
            'tip_naknade' => 'required'
        ]);

        $update_data = [
            'naziv' => $request->input('naziv'),
            'tip_naknade' => $request->input('tip_naknade'),
            'naknada' => $request->input('naknada'),
            'zavisi_od_vrste_senzora' => $request->input('zavisi_od_vrste_senzora') !== null
        ];

        return $this->edit(StavkaFakture::class, $request->input('id_stavka_fakture'), 8, 'stavka_fakture', $update_data, 'stavkafakture');
    }
    public function editTipUgovora(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(TipUgovora::class, $request->input('id_tip_ugovora'), 7, 'tip ugovora', $update_data, 'tipugovora');
    }
    public function editTipServisa(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(TipServisa::class, $request->input('id_tip_servisa'), 6, 'tip servisa', $update_data, 'tipservisa');
    }
    public function editTehnologije(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(Tehnologije::class, $request->input('id_tehnologija'), 5, 'tehnologija', $update_data, 'tehnologije');
    }
    public function editPartnera(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(Partner::class, $request->input('id_partner'), 4, 'partner', $update_data, 'partner');
    }
    public function editNazivServisa(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(NazivServisa::class, $request->input('id_naziv_servisa'), 3, 'naziv servisa', $update_data, 'nazivservisa');
    }
    public function editVrstaSenzora(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(VrstaSenzora::class, $request->input('id_vrsta_senzora'), 2, 'vrsta senzora', $update_data, 'vrstasenzora');
    }
    public function editLokacijuApp(EditRequest $request){
        $update_data = [
            'naziv' => $request->input('naziv')
        ];

        return $this->edit(LokacijaApp::class, $request->input('id_lokacija_aplikacije'), 1, 'lokacija aplikacije', $update_data, 'lokacijaapp');
    }



    public function getStavkaFakture($id){
        return StavkaFakture::whereId($id)->first();
    }
    public function getSoapUser($id){
        return [
            'id' => $id,
            'pib' => '123',
            'mbr' => '123',
            'email' => 'ast@asd',
            'telefon' => '0123',
            'kam' => 'kam',
            'segm' => 'seg'
        ];
    }

    public function soapTest(){
        //$soapclient = new SoapClient('https://www.w3schools.com/xml/tempconvert.asmx?WSDL');

        return dd(phpinfo());
    }
}
