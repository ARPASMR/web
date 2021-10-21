$(function(){
	var metadataInput = $("#metadataInput");
	var attivitaSelect = $('#attivitaSelect');
	setOnAttivitaSelectChange(metadataInput, attivitaSelect);
	checkAttivita(metadataInput, attivitaSelect);
	setOnChiusuraChangeListener();
	$.datetimepicker.setLocale('it');
	var dataInizio = $('#DataInizio');
	initializeDateTimePicker(dataInizio);
	if(dataInizio.val().indexOf('_') >= 0){
		dataInizio.val('');
	}
	var dataFine = $('#DataFine');
	initializeDateTimePicker(dataFine);
	if(dataFine.val().indexOf('_') >= 0){
		dataFine.val('');
	}
	if($('#Chiusura').val() == 'NO'){
		$("#dataChiusuraTicket").prop('disabled', true);
		$("#dataChiusuraTicket").val('');
		document.getElementById("dataChiusuraTicket").required = false;
	} else {
		document.getElementById("DataFine").required = true;
		document.getElementById("dataChiusuraTicket").required = true;
	}
	//Validazione form 
	$("form#modificaTicket").validate({
		rules: {
			DataFine: { greaterThan: "#DataInizio" },
			DataChiusura: { greaterThan: "#dataAperturaTicket" },
			Note: {
                    required: true,
                    maxlength: 250
                }
		}
	});
});

function setOnChiusuraChangeListener(){
	var chiusuraSelect = $('#Chiusura');
	chiusuraSelect.on('change', function(){
		var input = $(this);
		var dataFineInput = $('#DataFine');
		if(input.val() == "SI"){
			var showTicketButton = $('#showTicketButton');
			if(showTicketButton.val().indexOf('Apri') < 0){
					showTicketButton.trigger('click');
			}
			dataFineInput.prop("disabled", false);
			initializeDateTimePicker(dataFineInput, new Date());
			dataFineInput.prop('required', true); 
			$("#dataChiusuraTicket").prop('disabled', false);
			document.getElementById("dataChiusuraTicket").required = true;
		} else {
			dataFineInput.val('');
			dataFineInput.prop("disabled", true); 
			dataFineInput.prop('required', false);
			$("#dataChiusuraTicket").prop('disabled', true);
			$("#dataChiusuraTicket").val('');
			document.getElementById("dataChiusuraTicket").required = false;}
	});
}

function setOnAttivitaSelectChange(metadataInput, attivitaSelect){
	attivitaSelect.on('change', function(){checkAttivita(metadataInput, attivitaSelect);});
}

function checkAttivita(metadataInput, attivitaSelect){
	if(attivitaSelect.val() == ''){
		metadataInput.value = '';
		metadataInput.disabled = true;
	} else {
		metadataInput.disabled = false;
	}
}

function eliminaTicket(){
	var showTicketButton = $('#showTicketButton');
	showTicketButton.value = 'Apri';
	showTicketButton.show();
	$('#deleteTicketButton').hide();
	$("#ticketContainer").hide();
	$("#dataAperturaTicket").val('');
	$("#dataChiusuraTicket").val('');
	$('#prioritaSelect').val('');
	return false;
}
function initializeDateTimePicker(initJqueryObject, dateValue){
	var options = { format:'Y-m-d H:i', yearStart: 2009, yearEnd: (new Date()).getFullYear(), todayButton: true};
	if(dateValue){
		options.value = dateValue;
	}
	initJqueryObject.datetimepicker(options);
}

function apriTicket(event, isNew, hasEndDate){
	var dataApertura = $("#dataAperturaTicket");
	var dataChiusura = $("#dataChiusuraTicket");
	if(isNew){ initializeDateTimePicker(dataApertura, new Date()); initializeDateTimePicker(dataChiusura); dataChiusura.val(''); }
	else {
		initializeDateTimePicker(dataApertura);
		if(!hasEndDate && document.getElementById("dataChiusuraTicket").required){
			initializeDateTimePicker(dataChiusura, new Date());
		} else if(hasEndDate){
			initializeDateTimePicker(dataChiusura);
		} else {
			initializeDateTimePicker(dataChiusura);
			dataChiusura.val('');
		}
	}
	$("#ticketContainer").show();
	$(event.target).hide();
	$(event.target).val("Apri ticket");
	$('#deleteTicketButton').show();
	event.target.onclick = null;
	$(event.target).off('click');
	$(event.target).on('click', function(event){apriNuovoTicket(event)});
	return false;
}

function apriNuovoTicket(event){
	$(event.target).hide();
	initializeDateTimePicker($("#dataAperturaTicket"), new Date());
	initializeDateTimePicker($("#dataChiusuraTicket"));
	$("#dataChiusuraTicket").val('');
	$("#ticketContainer").show();
	$('#deleteTicketButton').show();
}

function onCheckboxStazioneChange(event){
	var isChecked = $(event.target).is(":checked");
	if(isChecked){
		$(".sensoriCheckbox").prop('checked', true);
	} else {
		$(".sensoriCheckbox").prop('checked', false);
	}
}

function onCheckboxSensoreChange(event){
	var isChecked = $(event.target).is(":checked");
	var stazioneCheckbox = $("#stazioneCheckbox");
	if(!isChecked){ 
		stazioneCheckbox.prop('checked', false);
	}
	else {
		// controllo se tutti i sensori sono flaggati
		var sensoriCheckboxes = $('.sensoriCheckbox');
		stazioneCheckbox.prop('checked', true);
		for(var i = 0; i < sensoriCheckboxes.length; i++){
			if(!$(sensoriCheckboxes[i]).is(":checked")){
				stazioneCheckbox.prop('checked', false);
			}
		}
	}
}

function enableCheckboxes(){
	// abilita per un momento le checkbox per permettere il submit dei valori
	$('input').prop("disabled", false);
	setTimeout(function() {$('input[type="checkbox"]').prop("disabled", true);})
}