var brojRedova = 1;
var brojAktivnihRedova = 1;
var stavke = [];
var aktivneStavke = [1];
var proveraIp = false;

$(document).ready(function() {
    //Forma podesavanja
    $.fn.stepy.defaults.legend = false;
    $.fn.stepy.defaults.transition = 'fade';
    $.fn.stepy.defaults.duration = 250;
    $.fn.stepy.defaults.backLabel = '<i class="icon-arrow-left13 position-left"></i> Nazad';
    $.fn.stepy.defaults.nextLabel = 'Dalje <i class="icon-arrow-right14 position-right"></i>';

    $(".stepy-callbacks").stepy({
        transition: 'slide',
        next: function(index) {
            //validaciju za prvi korak
            var greske = [];
            var allIDs = [
                'idKorisnikaError',
                'connectivityPlanError',
                'nazivFirmeError',
                'pibError',
                'mbrError',
                'telefonError',
                'kamError',
                'segmentError',
                'nazivUgovoraError',
                'tipUgovoraError',
                'tipServisaError',
                'nazivServisaError',
                'partnerError',
                'tipTehnologijeError',
                'brojUgovoraError',
                'datumError',
                'zbirniRacunError',
                'uoError',
                'senzorError',
                'lokacijaError',
                'nazivServeraError',
                'ipAdresaError'
            ];

            var idKorisnika = ($("#idKorisnika").val() == "") ?  greske.push('idKorisnika') : $("#idKorisnika").val();
            var connectivityPlan = ($("#connectivityPlan").val() == "") ?  greske.push('connectivityPlan') : $("#connectivityPlan").val();
            var nazivFirme = ($("#nazivFirme").val() == "") ?  greske.push('nazivFirme') : $("#nazivFirme").val();
            var pib = ($("#pib").val() == "") ?  greske.push('pib') : $("#pib").val();
            var mbr = ($("#mbr").val() == "") ?  greske.push('mbr') : $("#mbr").val();
            var telefon = ($("#telefon").val() == "") ?  greske.push('telefon') : $("#telefon").val();
            var kam = ($("#kam").val() == "") ?  greske.push('kam') : $("#kam").val();
            var segment = ($("#segment").val() == "") ?  greske.push('segment') : $("#segment").val();
            var nazivUgovora = ($("#nazivUgovora").val() == "") ?  greske.push('nazivUgovora') : $("#nazivUgovora").val();
            var tipUgovora = ($("#tipUgovora").val() == "") ?  greske.push('tipUgovora') : $("#tipUgovora").val();
            var tipServisa = ($("#tipServisa").val() == "") ?  greske.push('tipServisa') : $("#tipServisa").val();
            var nazivServisa = ($("#nazivServisa").val() == "") ?  greske.push('nazivServisa') : $("#nazivServisa").val();
            var partner = ($("#partner").val() == null) ?  greske.push('partner') : $("#partner").val();
            var tipTehnologije = ($("#tipTehnologije").val() == null) ?  greske.push('tipTehnologije') : $("#tipTehnologije").val();
            var brojUgovora = ($("#brojUgovora").val() == "") ?  greske.push('brojUgovora') : $("#brojUgovora").val();
            var datum = ($("#datum").val() == "") ?  greske.push('datum') : $("#datum").val();
            var zbirniRacun = ($("#zbirniRacun").val() == "") ?  greske.push('zbirniRacun') : $("#zbirniRacun").val();
            var uo = ($("#uo").val() == "") ?  greske.push('uo') : $("#uo").val();
            var tipSenzora = ($("#tipSenzora").val() == null) ?  greske.push('senzor') : $("#tipSenzora").val();
            var lokacijaAplikacije = ($("#lokacijaAplikacije").val() == "") ?  greske.push('lokacija') : $("#lokacijaAplikacije").val();

            if(proveraIp) {
                var naizv = ($("#nazivServera").val() == "") ? greske.push('nazivServera') : $("#nazivServera").val();
                var ip = ($("#ipAdresa").val() == "") ? greske.push('ipAdresa') : $("#ipAdresa").val();

                var ipRegex = /^(?!0)(?!.*\.$)((1?\d?\d|25[0-5]|2[0-4]\d)(\.|$)){4}$/;
                if (!ipRegex.test(ip)) {
                    //nije prosao regularni izraz
                    greske.push('ipAdresa');
                }
            }

            for(var i = 0; i < allIDs.length; i++){
                $("#"+allIDs[i]).attr('style','display: none;');
            }
            if(greske.length === 0){
                prikaziStavkeFakture();
                return true;
            }
            else{
                for(var i = 0; i < greske.length; i++){
                    $("#"+greske[i]+"Error").attr('style','');
                }
                greske = [];
                //sweet alert
                new PNotify({
                    title: 'Greška!',
                    text: 'Nisu popunjena sva obavezna polja!',
                    addclass: 'bg-telekom-slova',
                    hide: false,
                    buttons: {
                        sticker: false
                    }
                });
                //dohvatiti sve izabrane senzore i ovde povuci sve info za stavke fakture OVDE STOJI SAMO ZBOG TESTA !!!!
                prikaziStavkeFakture();
                return false; //sledeci korak
            }
        },
        finish: function() {
            //provara inputa za komercijalne uslove
            var greske = [];
            if(aktivneStavke.length === 0){
                new PNotify({
                    title: 'Greška!',
                    text: 'Morate podesiti bar jednu stavku fakture!',
                    addclass: 'bg-telekom-slova',
                    hide: false,
                    buttons: {
                        sticker: false
                    }
                });
                return false;
            }
            for (var i = 1; i <= aktivneStavke.length; i++) {

                $("#stavkaFakture" + (aktivneStavke[i]) + "Error").css("display", "none");
                $("#pocetakDatum" + (aktivneStavke[i]) + "Error").css("display", "none");
                $("#krajDatum" + (aktivneStavke[i]) + "Error").css("display", "none");
                $("#naknada" + (aktivneStavke[i]) + "Error").css("display", "none");
                $("#status" + (aktivneStavke[i]) + "Error").css("display", "none");

                var stavka = $("#stavkaFakture" + (aktivneStavke[i])).val();
                if (stavka === "") {
                    greske.push("#stavkaFakture" + (aktivneStavke[i]) + "Error");
                }

                var pocetakDatum = $("#pocetakDatum" + (aktivneStavke[i])).val();
                var pocetakDatumValue = new Date($("#pocetakDatum"+(aktivneStavke[i])+"_hidden").val());
                if (pocetakDatum === "" || pocetakDatumValue.getDate() > 1) {
                    greske.push("#pocetakDatum" + (aktivneStavke[i]) + "Error");
                }

                var krajDatum = $("#krajDatum" + (aktivneStavke[i])).val();
                var krajDatumValue = new Date($("#krajDatum"+(aktivneStavke[i])+"_hidden").val());
                var brojDanaUmesecu = (new Date(krajDatumValue.getFullYear(), krajDatumValue.getMonth()+1, 0)).getDate();
                if (krajDatum === "" || krajDatumValue.getDate() < brojDanaUmesecu) {
                    greske.push("#krajDatum" + (aktivneStavke[i]) + "Error");
                }

                var naknada = $("#naknada" + (aktivneStavke[i])).val();
                if (parseInt(naknada) === 0) {
                    greske.push("#naknada" + (aktivneStavke[i]) + "Error");
                }

                var status = $("#status" + (aktivneStavke[i])).val();
                if (status === "") {
                    greske.push("#status" + (aktivneStavke[i]) + "Error");
                }
            }



            if (greske.length === 0) {
                new PNotify({
                    title: 'Uspešno popunjeno!',
                    text: 'Slanje...',
                    addclass: 'bg-success'
                });
                return true;
            }
            else {
                //console.log(greske);
                for (var i = 0; i < greske.length; i++) {
                    $(greske[i]).css("display", "block");
                }
                new PNotify({
                    title: 'Greška!',
                    text: 'Nisu popunjena sva obavezna polja!',
                    addclass: 'bg-telekom-slova',
                    hide: false,
                    buttons: {
                        sticker: false
                    }
                });
                return false;
            }
        },
        titleClick: true
    });

    $('.stepy-step').find('.button-next').addClass('btn bg-telekom-slova');
    $('.stepy-step').find('.button-back').addClass('btn bg-telekom-slova');

    //Select podesavanja
    $('.select').select2({
        minimumResultsForSearch: Infinity
    });

    $("#partner").on('select2:opening select2:closing', function( event ) {
        var $searchfield = $(this).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    });

    $("#tipTehnologije").on('select2:opening select2:closing', function( event ) {
        var $searchfield = $(this).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    });

    $("#tipSenzora").on('select2:opening select2:closing', function( event ) {
        var $searchfield = $(this).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    });

    //Date time picker podesavanja
    $('.pickadate-selectors').pickadate({
        selectYears: true,
        selectMonths: true,
        selectDay: false,
        monthsFull: ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar'],
        weekdaysShort: ['Ned', 'Pon', 'Uto', 'Sre', 'Čet', 'Pet', 'Sub'],
        today: 'Danas',
        clear: 'Poništi',
        formatSubmit: 'yyyy/mm/dd 12:00:00'
    });

    //Podesavanje pritiska na enter
    document.getElementById("idKorisnika").addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            dohvatiKorisnika();
        }
    });


})

function dohvatiKorisnika(){
    var idKorisnika = $("#idKorisnika").val();
    if(idKorisnika === ""){
        new PNotify({
            title: 'Greška!',
            text: 'Morate uneti Id korisnika!',
            addclass: 'bg-telekom-slova',
            hide: false,
            buttons: {
                sticker: false
            }
        });
        $("#nazivFirme").val("").prop("disabled", true);
        $("#pib").val("").prop("disabled", true);
        $("#mbr").val("").prop("disabled", true);
        $("#email").val("").prop("disabled", true);
        $("#telefon").val("").prop("disabled", true);
        $("#kam").val("").prop("disabled", true);
        $("#segment").val("").prop("disabled", true);
    }
    else{
        $.ajax({
            type: 'GET',
            url: baseUrl+'ajax/finduser/'+idKorisnika,
            success: function (data) {
                //console.log(data);
                //dobija se no account found kad se posalje pib koji su oni probali! -> 13034891

                if(data.response === false){
                    //nije nadjen korisnik ni u lokalu ni na mrezi
                    new PNotify({
                        title: 'Greška!',
                        text: 'Nije pronadjen korisnik sa zadatim id-om!',
                        addclass: 'bg-telekom-slova',
                        hide: false,
                        buttons: {
                            sticker: false
                        }
                    });
                    $("#nazivFirme").val("").prop("disabled", true);
                    $("#pib").val("").prop("disabled", true);
                    $("#mbr").val("").prop("disabled", true);
                    $("#email").val("").prop("disabled", true);
                    $("#telefon").val("").prop("disabled", true);
                    $("#kam").val("").prop("disabled", true);
                    $("#segment").val("").prop("disabled", true);
                }
                else{
                    //korisnik pronadjen na mrezi

                    var greska = data.response.split('<out:BusinessName xmlns:out="http://www.siebel.com/xml/TS1%20Account%20IOInternal">')[1];


                    if (typeof greska === 'undefined'){
                        //postoji greska
                        var greskaPre = data.response.split('<io:FaultMessageText>')[1];
                        var greskaText = greskaPre.split('</io:FaultMessageText>')[0];
                        new PNotify({
                            title: 'Greška!',
                            text: 'Desila se greska : '+greskaText+' !',
                            addclass: 'bg-telekom-slova',
                            hide: false,
                            buttons: {
                                sticker: false
                            }
                        });
                    }
                    else{
                        //console.log(data.response);

                        $("#lokalniKorisnik").val('0');
                        var dataRegex = />(.*?)</;
                        var readError = false;

                        try {
                            var nazivReg = /<out:BusinessName xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:BusinessName>/g;
                            var naziv0 = data.response.match(nazivReg);
                            var naziv = dataRegex.exec(naziv0)[1];

                            console.log("Naziv = " + naziv);

                            var pibReg = /<out:AccountPIB xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:AccountPIB>/g;
                            var pib0 = data.response.match(pibReg);
                            var pib = dataRegex.exec(pib0)[1];

                            console.log("PIB = " + pib);

                            var mbReg = /<out:AccountMB xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:AccountMB>/g;
                            var mb0 = data.response.match(mbReg);
                            var mb = dataRegex.exec(mb0)[1];

                            console.log("mb = " + mb);

                            var segmentReg = /<out:AccountSegment xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:AccountSegment>/g;
                            var segment0 = data.response.match(segmentReg);
                            var segment = dataRegex.exec(segment0)[1];

                            console.log("segment = " + segment);

                            try {
                                var emailReg = /<out:MainEmailAddress xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:MainEmailAddress>/g;
                                var email0 = data.response.match(emailReg);
                                var email = dataRegex.exec(email0)[1];
                            }
                            catch(e){
                                var email = "";
                            }
                            console.log("email = " + email);

                            var telefonlReg = /<out:AccountMainPhoneNumber xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:AccountMainPhoneNumber>/g;
                            var telefon0 = data.response.match(telefonlReg);
                            var telefon = dataRegex.exec(telefon0)[1];

                            console.log("telefon = " + telefon);

                            var kamReg = /<out:AccountTeam xmlns:out="http:\/\/www.siebel.com\/xml\/TS1%20Account%20IOInternal">(.*?)<\/out:AccountTeam>/g;
                            var kam0 = data.response.match(kamReg);
                            var kam = dataRegex.exec(kam0)[1];

                            console.log("email = " + kam);
                        }
                        catch (err){
                            console.log(err);
                            readError = true;
                        }

                        if(readError || naziv==="") {
                            new PNotify({
                                title: 'Greška!',
                                text: 'Desila se greska : neispravan ID korisnika !',
                                addclass: 'bg-telekom-slova',
                                hide: false,
                                buttons: {
                                    sticker: false
                                }
                            });

                            $("#nazivFirme").val(naziv).prop("disabled", true);
                            $("#pib").val(pib).prop("disabled", true);
                            $("#mbr").val(mb).prop("disabled", true);
                            $("#telefon").val(telefon).prop("disabled", true);
                            $("#kam").val(kam).prop("disabled", true);
                            $("#segment").val(segment).prop("disabled", true);
                            $("#email").val(email).prop("disabled", true);

                        }
                        else{
                            $("#nazivFirme").val(naziv).prop("readonly", true);
                            $("#pib").val(pib).prop("readonly", true);
                            $("#mbr").val(mb).prop("readonly", true);
                            $("#telefon").val(telefon).prop("readonly", true);
                            $("#kam").val(kam).prop("readonly", true);
                            $("#segment").val(segment).prop("readonly", true);
                            $("#email").val(email).prop("readonly", true);

                            //dohvatanje zbirnih racuna
                            var nizSvihInvoProfila = [];
                            var zbirniRacuni = [];
                            var sviProfiliReg = /<out:ComInvoiceProfile (.*?)<\/out:ComInvoiceProfile>/g;

                            nizSvihInvoProfila = data.response.match(sviProfiliReg);
                            //console.log(nizSvihInvoProfila);

                            for(var i = 0; i < nizSvihInvoProfila.length; i++){
                                //provera da li je mob
                                var mobRegex = /<out:BPProfileType>(.*?)<\/out:BPProfileType>/g;
                                var profileTip = nizSvihInvoProfila[i].match(mobRegex)[0];

                                if ( (profileTip.indexOf("Mobile") !== -1) || (profileTip.indexOf("mobile") !== -1) || (profileTip.indexOf("Mobilni") !== -1) || (profileTip.indexOf("mobilni") !== -1) ){
                                    var zibrniReg = /<out:BPCode>\w+.\w+<\/out:BPCode>/g;
                                    zbirniRacuni.push(nizSvihInvoProfila[i].match(zibrniReg));
                                }
                            }


                            var select = document.getElementById('zbirniRacun');

                            for (var i = 0; i < zbirniRacuni.length; i++) {
                                var option = document.createElement('option');
                                option.value = zbirniRacuni[i];
                                option.innerHTML = zbirniRacuni[i];
                                select.appendChild(option);
                            }

                            $('#zbirniRacun').select2({
                                minimumResultsForSearch: Infinity
                            });

                            new PNotify({
                                title: 'Uspešno!',
                                text: 'Pronađen korisnik!',
                                addclass: 'bg-success'
                            });
                        }
                    }
                }

            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
                new PNotify({
                    title: 'Greška!',
                    text: 'Serverska greska!',
                    addclass: 'bg-telekom-slova',
                    hide: false,
                    buttons: {
                        sticker: false
                    }
                });
                $("#nazivFirme").val("").prop("disabled", true);
                $("#pib").val("").prop("disabled", true);
                $("#mbr").val("").prop("disabled", true);
                $("#email").val("").prop("disabled", true);
                $("#telefon").val("").prop("disabled", true);
                $("#kam").val("").prop("disabled", true);
                $("#segment").val("").prop("disabled", true);
            }
        });
    }
}

function createConnPlan(){
    var text = "iot-cs/";

    var selectedText = $('#zbirniRacun').select2('data')[0].text.replaceAll('.','_');

    text += selectedText;

    $("#connectivityPlan").val(text);
}

function promenaLokacije(){
    var lokacija = $("#lokacijaAplikacije").val();
    if(parseInt(lokacija) === 2){
        $('#nazivWrap').css("display", "block");
        $('#ipAddresaWrap').css("display", "block");
        proveraIp = true;
    }
    else{
        $('#nazivWrap').css("display", "none");
        $('#ipAddresaWrap').css("display", "none");
        proveraIp = false;
    }
}

function izabraniSenzori(){
    stavke = [];
    var senzoriID = $("#tipSenzora").val();
    var senzoriName = $("#tipSenzora option:selected").toArray().map(item => item.text);
    dohvatiStavkeFakutre(0,"",0);

    for(var i = 0; i < senzoriID.length; i++){
        dohvatiStavkeFakutre(1,senzoriName[i],senzoriID[i]);
    }
}

function dohvatiStavkeFakutre(flag, sufiks, idSenzor){
    //dohvatanje stavki fakture
    $.ajax({
        type: "GET",
        url: baseUrl+'ajax/getstavke/'+flag,
        success: function(data) {
            //console.log(data);
            //stavke = []; treba prvo postaviti niz ids senzora!
            for(var i = 0; i < data.stavke.length; i++){
                var obj = {
                    naziv: data.stavke[i].naziv+' '+sufiks,
                    idStavkaFakture: data.stavke[i].idStavkaFakture,
                    naknada: data.stavke[i].naknada,
                    tipNaknade: data.stavke[i].tipNaknade,
                    idVrstaSenzora: idSenzor
                }

                stavke.push(obj);
            }
        },
        error: function (xhr, status, error){
            console.log(xhr);
            console.log(status);
            console.log(error);
        }
    });
}

function prikaziStavkeFakture(){
    //prikazati tek kad se ode na drugi korak! stavkaFakture1

    var select = document.getElementById('stavkaFakture1');

    for (var i = 0; i < stavke.length; i++) {
        var option = document.createElement('option');
        option.value = stavke[i].idStavkaFakture+"|"+stavke[i].idVrstaSenzora;
        option.innerHTML = stavke[i].naziv;
        select.appendChild(option);
    }

    $('#zbirniRacun').select2({
        minimumResultsForSearch: Infinity
    });

}

function dodajNoviRed(){
    brojRedova ++;
    brojAktivnihRedova ++;

    var stavkeFakture = `<select name="stavkaFakture`+brojRedova+`" id="stavkaFakture`+brojRedova+`" onchange="izbranaStavkaFakture(`+brojRedova+`)" data-placeholder="Stavka fakture" class="select"> <option></option>`;
    for(var i=0; i<stavke.length; i++){
        stavkeFakture+= `<option value="`+stavke[i].idStavkaFakture+`|`+stavke[i].idVrstaSenzora+`">`+stavke[i].naziv+`</option>`;
    }
    stavkeFakture += `</select>
        <label id="stavkaFakture`+brojRedova+`Error" for="stavkaFakture`+brojRedova+`" class="validation-error-label" style="display: none;">Obavezno polje!</label>
        <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#stavkaFaktureDiv").append(stavkeFakture);

    var pocetakDatum = `<input type="text" name="pocetakDatum`+brojRedova+`" id="pocetakDatum`+brojRedova+`" class="form-control pickdate-novi" placeholder="Datum početak">
                        <label id="pocetakDatum`+brojRedova+`Error" for="pocetakDatum`+brojRedova+`" class="validation-error-label" style="display: none;">Mora biti prvi dan u mesecu!</label>
                        <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#pocetakDiv").append(pocetakDatum);

    var krajDatum = `<input type="text" name="krajDatum`+brojRedova+`" id="krajDatum`+brojRedova+`" class="form-control pickdate-novi" placeholder="Datum kraj">
                     <label id="krajDatum`+brojRedova+`Error" for="krajDatum`+brojRedova+`" class="validation-error-label" style="display: none;">Mora biti poslednji dan u mesecu!</label>
                     <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#krajDiv").append(krajDatum);

    var naknada = `<input type="number" name="naknada`+brojRedova+`" id="naknada`+brojRedova+`" class="form-control" min="0" value="0" step="0.01">
                    <label id="naknada`+brojRedova+`Error" for="naknada`+brojRedova+`" class="validation-error-label" style="display: none;">Obavezno polje!</label>
                     <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#naknadaDiv").append(naknada);

    var status = `<select name="status`+brojRedova+`" id="status`+brojRedova+`" data-placeholder="Status `+brojRedova+`" class="select">
                                <option></option>
                                <option value="1">Aktivni</option>
                                <option value="2">Prijavljeni</option>
                                <option value="3">N/A</option>
                            </select>
                            <label id="status`+brojRedova+`Error" for="naknada`+brojRedova+`" class="validation-error-label" style="display: none;">Obavezno polje!</label>
                            <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#statusDiv").append(status);

    var min = `<input type="number" name="min`+brojRedova+`" id="min`+brojRedova+`" class="form-control" value="0" step="1">
                     <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#minDiv").append(min);

    var max = `<input type="number" name="max`+brojRedova+`" id="max`+brojRedova+`" class="form-control" value="0" step="1">
                     <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#maxDiv").append(max);

    var brisanje = `<ul class="icons-list text-center form-control" style="border: none;" id="linkBrisanje`+brojRedova+`">
                        <li class="text-danger-800" style="padding-top: 6px;"><a href="#"  onclick="obrisiRed(`+brojRedova+`)" data-popup="tooltip" title="Obriši red"><i style="font-size: 20px;" class="icon-trash"></i></a></li>
                    </ul><span class="divider`+brojRedova+`" style="width: 100%; min-height: 5px; display: inline-block;"></span>`;
    $("#akcijeDiv").append(brisanje);

    $('.select').select2({
        minimumResultsForSearch: Infinity
    });

    $('.pickdate-novi').pickadate({
        selectYears: true,
        selectMonths: true,
        monthsFull: ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar'],
        weekdaysShort: ['Ned', 'Pon', 'Uto', 'Sre', 'Čet', 'Pet', 'Sub'],
        today: 'Danas',
        clear: 'Poništi',
        formatSubmit: 'yyyy/mm/dd 12:00:00'
    });

    $("#aktivneStavke").val(aktivneStavke);
    aktivneStavke.push(brojRedova);
    /*console.log("dodavanje");
    console.log("broj redova = "+brojRedova);
    console.log("brojAktivnihRedova = "+brojAktivnihRedova);
    console.log(aktivneStavke);*/
}

function obrisiRed(idRed){
    brojAktivnihRedova--;
    //ukljanja se po id-evima : stavkaFakture1, pocetakDatum1, krajDatum1, naknada1, status1, min1, max1, linkBrisanje1

    $("#stavkaFakture"+idRed).select2('destroy');
    document.getElementById("stavkaFakture"+idRed).remove();
    document.getElementById('stavkaFakture'+idRed+'Error').remove();

    document.getElementById('pocetakDatum'+idRed).remove();
    document.getElementById('pocetakDatum'+idRed+'Error').remove();

    document.getElementById('krajDatum'+idRed).remove();

    document.getElementById('naknada'+idRed).remove();
    document.getElementById('naknada'+idRed+'Error').remove();

    $("#status"+idRed).select2('destroy');
    document.getElementById("status"+idRed).remove();
    document.getElementById('status'+idRed+'Error').remove();

    document.getElementById('min'+idRed).remove();
    document.getElementById('max'+idRed).remove();
    document.getElementById('linkBrisanje'+idRed).remove();

    $(".divider"+idRed).remove();

    var index = aktivneStavke.indexOf(idRed);
    aktivneStavke.splice(index, 1);
    $("#aktivneStavke").val(aktivneStavke);

    /*console.log("brisanje");
    console.log("broj redova = "+brojRedova);
    console.log("brojAktivnihRedova = "+brojAktivnihRedova);
    console.log(aktivneStavke);*/
}

function izbranaStavkaFakture(idReda){
    var idStavke = $("#stavkaFakture"+idReda+" option:selected").val().split('|')[0];
    for(var i=0; i<stavke.length; i++){
        if(parseInt(idStavke) === stavke[i].idStavkaFakture){
            $("#naknada"+idReda).val(stavke[i].naknada);
        }
    }
}
