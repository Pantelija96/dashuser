<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertRequest;
use App\Http\Requests\LoginRequest;
use App\Models\LokacijaApp;
use App\Models\NazivServisa;
use App\Models\Partner;
use App\Models\StavkaFakture;
use App\Models\Tehnologije;
use App\Models\TipServisa;
use App\Models\TipUgovora;
use App\Models\VrstaSenzora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
        return dd($request->all());
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
            //return response(['error' => "Desila se greska! Id greske : insert-$idGreske"]);
        }
        if($insertResult){
            return redirect()->back()->with(['success' => "Uspesno - $text!"]);
            //return response(['success'=>"Uspesno - $text!"]);
        }
        else{
            Log::error("Greska pri dodavanju $text insert-$idGreske");
            return redirect()->back()->with(['error' => "Desila se greska! Id greske : insert-$idGreske"]);
            //return response(['error' => "Desila se greska! Id greske : insert-$idGreske"]);
        }
    }
    public function dodajLokacijuApp(InsertRequest $request){
        return $this->insert('lokacije aplikacije', LokacijaApp::class, $request->input('naziv'), $request->input('prikazi'), 1);
    }
    public function dodajNazivServisa(InsertRequest $request){
        return $this->insert('naziv servisa', NazivServisa::class, $request->input('naziv'), $request->input('prikazi'), 2);
    }
    public function dodajPartnera(InsertRequest $request){
        return $this->insert('partner', Partner::class, $request->input('naziv'), $request->input('prikazi'), 3);
    }
    public function dodajTehnologije(InsertRequest $request){
        return $this->insert('tehnologije', Tehnologije::class, $request->input('naziv'), $request->input('prikazi'), 4);
    }
    public function dodajTipServisa(InsertRequest $request){
        return $this->insert('tip servisa', TipServisa::class, $request->input('naziv'), $request->input('prikazi'), 5);
    }
    public function dodajTipUgovora(InsertRequest $request){
        return $this->insert('tip ugovora', TipUgovora::class, $request->input('naziv'), $request->input('prikazi'), 6);
    }
    public function dodajVrstuSenzora(InsertRequest $request){
        return $this->insert('vrsta senzora', VrstaSenzora::class, $request->input('naziv'), $request->input('prikazi'), 7);
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
}
