function showFilters(){
    if($('#filteri').is(':visible')){
        $('#filteri').fadeOut("slow");
    }
    else{
        $('#filteri').fadeIn("slow");
    }
}

$(function() {
    $('.select').select2({
        minimumResultsForSearch: Infinity,
        placeholder: "",
        allowClear: true
    });

    $('.pickadate-format').pickadate({
        format: 'yyyy-mm-dd',
        formatSubmit: 'yyyy-mm-dd',
        hiddenPrefix: 'prefix__',
        hiddenSuffix: '__suffix',
        select: [2021, 10, 2],
        max: [moment().year(), moment().month(), moment().date()]
    });
})

function primeniFiltere(){
    alert("Primena filtera");
}
