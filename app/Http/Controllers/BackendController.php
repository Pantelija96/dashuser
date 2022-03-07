<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Http\Requests\InsertRequest;
use App\Http\Requests\LoginRequest;
use App\Models\LokacijaApp;
use App\Models\NazivServisa;
use App\Models\Partner;
use App\Models\StavkaFakture;
use App\Models\Tehnologije;
use App\Models\TipServisa;
use App\Models\TipUgovora;
use App\Models\User;
use App\Models\VrstaSenzora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
}
