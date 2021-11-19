/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: IT (Italian; Italiano)
 */
(function ($) {
	$.extend($.validator.messages, {
		required: "Campo obbligatorio.",
		remote: "Controllare questo campo.",
		email: "Inserire un indirizzo email valido.",
		url: "Inserire un indirizzo web valido.",
		date: "Inserire una data valida.",
		dateISO: "Inserire una data valida (ISO).",
		number: "Inserire un numero valido.",
		digits: "Inserire solo numeri.",
		creditcard: "Inserire un numero di carta di credito valido.",
		equalTo: "Il valore non corrisponde.",
		accept: "Inserire un valore con un&apos;estensione valida.",
		maxlength: $.validator.format("Non inserire pi&ugrave; di {0} caratteri."),
		minlength: $.validator.format("Inserire almeno {0} caratteri."),
		rangelength: $.validator.format("Inserire un valore compreso tra {0} e {1} caratteri."),
		range: $.validator.format("Inserire un valore compreso tra {0} e {1}."),
		max: $.validator.format("Inserire un valore minore o uguale a {0}."),
		min: $.validator.format("Inserire un valore maggiore o uguale a {0}.")
	});
}(jQuery));