$(document).ready(function(){
    $('.select').select2();
});

$(function() {
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        columnDefs: [{
            orderable: false,
            width: '100px',
            targets: [ 5 ]
        }],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Filter:</span> _INPUT_',
            lengthMenu: '<span>Prikaži zapisa:</span> _MENU_',
            paginate: { 'first': 'Prva', 'last': 'Poslednja', 'next': '&rarr;', 'previous': '&larr;' },
            sEmptyTable: 'Prazna tabela!',
            sInfo: "Prikaz _START_ do _END_ od _TOTAL_ zapisa",
            sInfoEmpty: "Nema zapisa u tabeli!",
            sInfoFiltered: "(filtirirano od _MAX_ ukupno zapisa)",
            sSearch: "Pretraga:",
            sLoadingRecords: "Učitavanje",
            sProcessing: "Obrada..."
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });

    var lastIdx = null;
    var table = $('.datatable-highlight').DataTable();

    $('.datatable-highlight tbody').on('mouseover', 'td', function() {
        var colIdx = table.cell(this).index().column;

        if (colIdx !== lastIdx) {
            $(table.cells().nodes()).removeClass('active');
            $(table.column(colIdx).nodes()).addClass('active');
        }
    }).on('mouseleave', function() {
        $(table.cells().nodes()).removeClass('active');
    });

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
});


function proveraLozinke(){
    //alert ("TEST");
    var prvaLozinka = $("#lozinka").val();
    var ponovoLozinka = $("#lozinkaPonovo").val();

    if((prvaLozinka === ponovoLozinka) && ponovoLozinka !== ""){
        $("#dugme").prop('disabled',false);
    }
    else{
        $("#dugme").prop('disabled',true);
        new PNotify({
            title: 'Greška!',
            text: 'Lozinke se ne poklapaju!',
            addclass: 'bg-telekom-slova',
            hide: false,
            buttons: {
                sticker: false
            }
        });
    }
}

var postojiGreska = false;
function proveri(){
    var ime = $("#ime").val();
    var prezime = $("#prezime").val();
    var email = $("#email").val();
    var lozinka = $("#lozinka").val();
    var lozinkaPonovo = $("#lozinkaPonovo").val();
    var uloga = $("#uloga").val();

    $("#imeError2").attr('style','display: none;');
    $("#prezimeError2").attr('style','display: none;');
    $("#emailError2").attr('style','display: none;');
    $("#lozinkaError2").attr('style','display: none;');
    $("#lozinkaPonovoimeError2").attr('style','display: none;');
    $("#ulogaError2").attr('style','display: none;');

    if(ime === ""){
        postojiGreska = true;
        $("#imeError").html("Obavezno polje!").attr('style','');
    }
    else{
        postojiGreska = false;
        $("#imeError").attr('style','display: none;');
    }

    if(prezime === ""){
        postojiGreska = true;
        $("#prezimeError").html("Obavezno polje!").attr('style','');
    }
    else{
        postojiGreska = false;
        $("#prezimeError").attr('style','display: none;');
    }

    if(email === ""){
        postojiGreska = true;
        $("#emailError").html("Obavezno polje!").attr('style','');
    }
    else{
        postojiGreska = false;
        $("#emailError").attr('style','display: none;');
    }

    if((lozinka === lozinkaPonovo)){
        postojiGreska = false;
        $("#lozinkaError").attr('style','display: none;');
        $("#lozinkaPonovoError").attr('style','display: none;');
    }
    else{
        postojiGreska = true;
        $("#lozinkaError").html("Lozinke se ne poklapaju!").attr('style','');
        $("#lozinkaPonovoError").html("Lozinke se ne poklapaju!").attr('style','');
    }

    if(uloga === ""){
        postojiGreska = true;
        $("#ulogaError").html("Morate izabrati ulogu korisnika!").attr('style','');
    }
    else{
        postojiGreska = false;
        $("#ulogaError").attr('style','display: none;');
    }

    if(postojiGreska){
        new PNotify({
            title: 'Greška!',
            text: 'Ispravite greške za nastavak!',
            addclass: 'bg-telekom-slova',
            hide: false,
            buttons: {
                sticker: false
            }
        });
    }
    else{
        new PNotify({
            title: 'Uspešno popunjeno!',
            text: 'Slanje...',
            addclass: 'bg-success'
        });
        document.getElementById("forma").submit();
    }

}

function brisanjeUsera(idUser){
    var idKorisnika = parseInt(idUser);
    if(idKorisnika !== 0){
        var notice = new PNotify({
            title: 'Confirmation',
            text: '<p>Da li ste sigurni da želite da obršete korisnika portala?</p>',
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
            $.ajax({
                type: 'GET',
                url: baseUrl+'ajax/deletekorisnik/'+idKorisnika,
                success: function (data) {
                    new PNotify({
                        title: 'Uspeh!',
                        text: 'Uspešno obrisan korsnik!',
                        addclass: 'bg-success'
                    });
                    if(data.success){
                        window.location.reload();
                    }
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
}
