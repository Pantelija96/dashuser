<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertRequest;
use App\Http\Requests\LoginRequest;
use App\Models\LokacijaApp;
use App\Models\NazivServisa;
use App\Models\Partner;
use App\Models\Tehnologije;
use App\Models\TipServisa;
use App\Models\TipUgovora;
use App\Models\VrstaSenzora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
}
