var brojRedova = brojRedovaIzBaze;
var brojAktivnihRedova = brojRedovaIzBaze;
var stavke = [];
var aktivneStavke = [];
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
        next: function(index){
            return true;
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
            for (var i = 0; i < aktivneStavke.length; i++) {

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

                var krajDatum = new Date($("#krajDatum" + (aktivneStavke[i])).val());
                var brojDanaUmesecu = (new Date(krajDatum.getFullYear(), krajDatum.getMonth()+1, 0)).getDate();
                $("krajDatum"+1+"_submit").val(krajDatum);
                console.log(krajDatum);
                console.log(brojDanaUmesecu);
                if (isNaN(krajDatum.getTime()) || krajDatum.getDate() < brojDanaUmesecu) {
                    greske.push("#krajDatum" + (aktivneStavke[i]) + "Error");
                }

                var naknada = $("#naknada" + (aktivneStavke[i])).val();
                if (parseFloat(naknada) <= 0) {
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
                console.log(greske);
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

    //Date time picker podesavanja
    $('.pickadate-selectors').pickadate({
        selectYears: true,
        selectMonths: true,
        monthsFull: ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar'],
        weekdaysShort: ['Ned', 'Pon', 'Uto', 'Sre', 'Čet', 'Pet', 'Sub'],
        today: 'Danas',
        clear: 'Poništi',
        formatSubmit: 'yyyy/mm/dd 12:00:00'
    });

    //podesavanje za dodavanje i brisanje redova
    for(var i = 0; i < brojRedovaIzBaze; i++){
        aktivneStavke.push(i+1);
    }

    $("#aktivneStavke").val(aktivneStavke);
    izabraniSenzori();

    console.log("na pocetku");
    console.log("broj redova = "+brojRedova);
    console.log("brojAktivnihRedova = "+brojAktivnihRedova);
    console.log(aktivneStavke);
    console.log(senzoriUgovora);
})

function dodajNoviRed(){
    brojRedova ++;
    brojAktivnihRedova ++;

    var stavkeFakture = `<select name="stavkaFakture`+brojRedova+`" id="stavkaFakture`+brojRedova+`" onchange="izbranaStavkaFakture(`+brojRedova+`)" data-placeholder="Stavka fakture" class="select"> <option></option>`;
    for(var i=0; i<stavke.length; i++){
        stavkeFakture+= `<option value="`+stavke[i].idStavkaFakture+`|`+stavke[i].idVrstaSenzora+`">`+stavke[i].naziv+`</option>`;
    }
    stavkeFakture += `</select>
        <label id="stavkaFakture`+brojRedova+`Error" for="stavkaFakture`+brojRedova+`" class="validation-error-label" style="display: none;">Obavezno polje!</label>
        <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>
        <input type="hidden" name="idKomUslov`+brojRedova+`" id="idKomUslov`+brojRedova+`" value="0"/>`;
    $("#stavkaFaktureDiv").append(stavkeFakture);

    var pocetakDatum = `<input type="text" name="pocetakDatum`+brojRedova+`" id="pocetakDatum`+brojRedova+`" class="form-control pickdate-novi" placeholder="Datum početak">
                        <label id="pocetakDatum`+brojRedova+`Error" for="pocetakDatum`+brojRedova+`" class="validation-error-label" style="display: none;">Mora biti prvi dan u mesecu!</label>
                        <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#pocetakDiv").append(pocetakDatum);

    var krajDatum = `<input type="text" name="krajDatum`+brojRedova+`" id="krajDatum`+brojRedova+`" class="form-control pickdate-novi" placeholder="Datum kraj">
                     <label id="krajDatum`+brojRedova+`Error" for="krajDatum`+brojRedova+`" class="validation-error-label" style="display: none;">Mora biti poslednji dan u mesecu!</label>
                     <span class="divider`+brojRedova+`" style="width: 100%; min-height: 3px; display: inline-block;"></span>`;
    $("#krajDiv").append(krajDatum);

    var naknada = `<input type="number" name="naknada`+brojRedova+`" id="naknada`+brojRedova+`" class="form-control" min="0.01" value="0" step="0.01">
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
                        <li class="text-danger-800" style="padding-top: 10px;"><a href="#"  onclick="obrisiRed(`+brojRedova+`)" data-popup="tooltip" title="Obriši red"><i style="font-size: 20px;" class="icon-trash"></i></a></li>
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

    aktivneStavke.push(brojRedova);
    $("#aktivneStavke").val(aktivneStavke);
    enableIzmenu();
    console.log("dodavanje");
    console.log("broj redova = "+brojRedova);
    console.log("brojAktivnihRedova = "+brojAktivnihRedova);
    console.log(aktivneStavke);
}

function izabraniSenzori(){
    stavke = [];
    var senzoriName = naziviSenzora.split("|");

    dohvatiStavkeFakutre(0,"",0);

    for(var i = 0; i < senzoriUgovora.length; i++){
        dohvatiStavkeFakutre(1,senzoriName[i],senzoriUgovora[i]);
    }
}

function dohvatiStavkeFakutre(flag, sufiks, idSenzor){
    //dohvatanje stavki fakture
    $.ajax({
        type: "GET",
        url: baseUrl+'ajax/getstavke/'+flag,
        success: function(data) {
            //console.log(data);
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

function izbranaStavkaFakture(idReda){
    var idStavke = $("#stavkaFakture"+idReda+" option:selected").val().split('|')[0];
    for(var i=0; i<stavke.length; i++){
        if(parseInt(idStavke) === stavke[i].idStavkaFakture){
            $("#naknada"+idReda).val(stavke[i].naknada);
        }
    }
}

function obrisiRed(idRed){
    brojAktivnihRedova--;
    //ukljanja se po id-evima : stavkaFakture1, pocetakDatum1, krajDatum1, naknada1, status1, min1, max1, linkBrisanje1

    $("#stavkaFakture"+idRed).select2('destroy');
    document.getElementById("stavkaFakture"+idRed).remove();
    document.getElementById('stavkaFakture'+idRed+'Error').remove();

    document.getElementById('pocetakDatum'+idRed).remove();
    //document.getElementById('pocetakDatum'+idRed+'Error').remove();

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
    enableIzmenu();

    var index = aktivneStavke.indexOf(idRed);
    aktivneStavke.splice(index, 1);
    $("#aktivneStavke").val(aktivneStavke);


    console.log("brisanje");
    console.log("broj redova = "+brojRedova);
    console.log("brojAktivnihRedova = "+brojAktivnihRedova);
    console.log(aktivneStavke);
}

function deaktivirajUgovor(id){
    var notice = new PNotify({
        title: 'Confirmation',
        text: '<p>Da li ste sigurni da želite da deaktivirate ugovor?</p>',
        hide: false,
        type: 'warning',
        addclass: 'bg-telekom-slova',
        confirm: {
            confirm: true,
            buttons: [
                {
                    text: 'Da',
                    addClass: 'btn-sm'
                },
                {
                    text: 'Ne',
                    addClass: 'btn-sm'
                }
            ]
        },
        buttons: {
            closer: false,
            sticker: false
        },
        history: {
            history: false
        }
    });

    notice.get().on('pnotify.confirm', function() {
        //proslediti na deaktivaciju
        var idUgovor = parseInt(id);
        if(idUgovor !== 0){
            $.ajax({
                type: 'GET',
                url: baseUrl+'ajax/deaktivirajugovor/'+idUgovor,
                success: function (data) {
                    window.location.replace(baseUrl+'home');
                },
                error: function (xhr, status, error) {
                    new PNotify({
                        title: 'Greška!',
                        text: 'Desila se neočekivana greška, proveriti console!',
                        addclass: 'bg-telekom-slova',
                        hide: false,
                        buttons: {
                            sticker: false
                        }
                    });
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
        else{
            new PNotify({
                title: 'Greška!',
                text: 'Desila se neočekivana greška, id = 0!',
                addclass: 'bg-telekom-slova',
                hide: false,
                buttons: {
                    sticker: false
                }
            });
        }
    });
}

function obrisiJednokratnuStavku(id){
    var notice = new PNotify({
        title: 'Confirmation',
        text: '<p>Da li ste sigurni da želite da izbriste komercijalni uslov?</p>',
        hide: false,
        type: 'warning',
        addclass: 'bg-telekom-slova',
        confirm: {
            confirm: true,
            buttons: [
                {
                    text: 'Obriši',
                    addClass: 'btn-sm'
                },
                {
                    text: 'Poništi',
                    addClass: 'btn-sm'
                }
            ]
        },
        buttons: {
            closer: false,
            sticker: false
        },
        history: {
            history: false
        }
    });

    notice.get().on('pnotify.confirm', function() {
        //proslediti na deaktivaciju
        var idKomUslov = parseInt($("#idKomUslov"+id).val());
        if(idKomUslov !== 0){
            $.ajax({
                type: 'GET',
                url: baseUrl+'ajax/deletekomuslov/'+idKomUslov,
                success: function (data) {
                    obrisiRed(id);
                    enableIzmenu();
                },
                error: function (xhr, status, error) {
                    new PNotify({
                        title: 'Greška!',
                        text: 'Desila se neočekivana greška, proveriti console!',
                        addclass: 'bg-telekom-slova',
                        hide: false,
                        buttons: {
                            sticker: false
                        }
                    });
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
        else{
            new PNotify({
                title: 'Greška!',
                text: 'Desila se neočekivana greška, id = 0!',
                addclass: 'bg-telekom-slova',
                hide: false,
                buttons: {
                    sticker: false
                }
            });
        }
    });
}

function enableIzmenu(){
    console.log('enable izmenu');
    document.getElementById("submitIzmene").disabled = false;
}
