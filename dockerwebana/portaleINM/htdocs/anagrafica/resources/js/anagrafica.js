
    function aggiornaAnagrafica(){
        $("form#filtroAnagrafica").submit();
    }

    function aggiornaFiltri(){
        
        var regione = $('#regione').val();

        // alla selezione di "regione"
        if(regione=="ALL"){
            $('#provincia').prop("disabled", true);
            $('#rete').prop("disabled", true);
            $('#allerta').prop("disabled", true);
        }
        else if(regione=="lombardia"){
            $('#provincia').prop("disabled", false);
            $('#rete').prop("disabled", false);
            $('#allerta').prop("disabled", false);
        }
        else if(regione=="extra"){
            $('#provincia').prop("disabled", false);
            $('#rete').prop("disabled", true);
            $('#allerta').prop("disabled", true);
        }

        // visualizza/nasconde LABEL
        $("form#filtroAnagrafica input, form#filtroAnagrafica select").each(function(){
            var idThis = $(this).attr("id");
            if($(this).prop("disabled")==true){
                $(this).css("display", 'none');
                $("label#label-"+idThis).css("display", 'none');
            } else {
                $(this).css("display", 'inline');
                $("label#label-"+idThis).css("display", 'inline');
            }
        });
    }
    
    function aggiornaConteggioStazioni() {
    	var stz = $('table#listaStazioni tr:visible').length;
    	$('#stationsCount').text(stz);
    }


    function esportaAnagrafica(doWhat){

		var form = $('#filtroAnagrafica');
		$("<input type='text' name='do' value='" + doWhat + "' hidden id='hiddenDo' />").appendTo(form);
		form.submit();
		$('#hiddenDo').remove();
		
        /*var IDs = '';
        $.each($('table.lista tbody tr.recordLista'), function(){
            if($(this).css('display')=="table-row"){
                var obj = $(this).find(".idEntita")[0];
                IDs += $(obj).text()+',';
            }
        });
        IDs = IDs.replace(/\,+$/g, '');

        var IDsInput = document.createElement("input");
        IDsInput.type = "text";
        IDsInput.name = "ids";
        IDsInput.value =  IDs;

        var exportForm = document.createElement("form");
        exportForm.target = "_blank";
        exportForm.method = "POST";
        exportForm.action = url;
        exportForm.appendChild(IDsInput);
        document.body.appendChild(exportForm);
        exportForm.submit();
        document.body.removeChild(exportForm);*/
		
    }



    $(document).ready(function () {

	// ## Inizializza datepickers
        $('.datepicker').datepicker( { changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd"
            },
            $.datepicker.regional[ "it" ]
        );
		
        // #### Ordinamento Stazioni ####
        $("table#listaStazioni").tablesorter({
            sortList: [[5,0]],
            widgets: ["filter"]
        });

        // #### Ordinamento Sensori ####
        $("table#listaSensori").tablesorter({
            sortList: [[6,0]],
            widgets: ["filter"]
        });

        // #### Ordinamento Utenti ####
        $("table#listaUtenti").tablesorter({
            sortList: [[2,0]]
        });

        // #### Ordinamento Annotazioni ####
        $("#listaAnnotazioni").tablesorter({
            sortList: [[6,1]]
        });

        // #### Ordinamento tipologie ####
        $("#listaTipologie").tablesorter({
            sortList: [[0,0]]
        });

        // #### Crea/Modifica Stazione ####
        $("form#modificaStazione").validate({
            rules: {
                IDstazione:{
                    required: true,
                    maxlength: 5
                },
                IDrete:{
                    required: true
                },
                NOMEstazione: {
                    maxlength: 40
                },
                NOMEweb: {
                    maxlength: 50
                },
                NOMEhydstra: {
                    maxlength: 30
                },
                CGB_Nord: {
                    integer: true,
                    maxlength: 8
                },
                CGB_Est: {
                    integer: true,
                    maxlength: 8
                },
                lat: {
                    number: true,
                    max: 99,
                    maxDigits: 8
                },
                lon: {
                    number: true,
                    max: 99,
                    maxDigits: 8
                },
                Quota: {
                    integer: true,
                    maxlength: 5
                },
                Attributo: {
                    maxlength: 50,
                    required: false
                },
                Localita: {
                    maxlength: 100
                },
                Comune: {
                    maxlength: 50,
                    required: true
                },
                Provincia: {
                    maxlength: 2
                },
                ProprietaStazione: {
                    maxlength: 20
                },
                ProprietaTerreno: {
                    maxlength: 20
                },
                Manutenzione: {
                    maxlength: 20
                },
                NoteManutenzione: {
                    maxlength: 30
                },
                DataLogger: {
                    maxlength: 25
                },
                NoteDL: {
                    maxlength: 30
                },
                Connessione: {
                    maxlength: 20
                },
                NoteConnessione: {
                    maxlength: 30
                },
                Alimentazione: {
                    maxlength: 10
                },
                NoteAlimentazione: {
                    maxlength: 30
                }
            }
        });

        // #### Crea/Modifica Sensore ####
        initDatePicker($("#DataInizio"));
        initDatePicker($("#DataFine"));
        $("form#modificaSensore").validate({
            rules: {
                IDsensore:{
                    required: true,
                    maxlength: 5
                },
                DataInizio:{
                    ISO8601CompleteDate: true
                },
                DataFine:{
                    ISO8601CompleteDate: true
                },
                NOMEtipologia: {
                    required: true
                },
                IDstazione: {
                    required: true
                },
                AggregazioneTemporale: {
                    integer: true,
                    maxlength: 3
                },
                NoteAT: {
                    maxlength: 30
                },
                QuotaSensore : {
                    integer: true,
                    maxlength: 5
                },
                QSedificio: {
                    number: true,
                    max: 999,
                    maxDigits: 1
                },
                QSsupporto: {
                    number: true,
                    max: 999,
                    maxDigits: 1
                },
                NoteQS: {
                    maxlength: 30
                }
            }
        });

        // #### Crea/Modifica Strumento ####
        initDatePicker($("#DataIstallazione"));
        initDatePicker($("#DataDisistallazione"));
        $("form#modificaStrumento").validate({
            rules: {
                DataIstallazione:{
                    ISO8601CompleteDate: true
                },
                DataDisistallazione:{
                    ISO8601CompleteDate: true
                },
                Marca: {
                    maxlength: 10
                },
                Modello: {
                    maxlength: 20
                },
                Note: {
                    maxlength: 30
                }
            }
        });

        // #### Crea/Modifica Convenzione ####
        initDatePicker($("#Stipula"));
        initDatePicker($("#Scadenza"));
        $("form#modificaConvenzione").validate({
            rules: {
                Stipula:{
                    ISO8601CompleteDate: true
                },
                Scadenza:{
                    ISO8601CompleteDate: true
                },
                CodiceArch: {
                    maxlength: 10
                },
                Riferimento: {
                    maxlength: 100
                },
                Note: {
                    maxlength: 100
                }

            }
        });


        // #### Aggiungi/Rimuovi Lista Nera ####
         $("form#modificaListaNera").validate({
            rules: {
                Note: {
                    required: true,
                    maxlength: 150
                }

            }
        });

    });

