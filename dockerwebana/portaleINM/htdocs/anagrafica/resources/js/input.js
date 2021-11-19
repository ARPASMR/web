
    function initDatePicker(inputOBJ){
        $(inputOBJ).datepicker( { changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd"
            },
            $.datepicker.regional[ "it" ]
        );
    }

    jQuery.validator.addMethod("integer", function(value, element) {
        return this.optional(element) || /^-?\d+$/.test(value);
    }, "Inserire un numero intero.");

    jQuery.validator.addMethod('maxDigits', function(value, element, params) {
        var pattern = new RegExp("^\\d+(?:\\.\\d{0,"+params+"})?$");
        var result = pattern.test(value);
        console.debug('new: '+result.toString());
        return this.optional(element) || result;
    }, "Inserire massimo {0} valori decimali.");

    // Matches a date in yyyy-mm-dd format from between 1900-01-01 and 2099-12-31
    jQuery.validator.addMethod(
        "ISO8601CompleteDate",
        function(value, element) {
            var re = new RegExp("^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$");
            return this.optional(element) || re.test(value);
        },
        "Formato non valido. (YYYY-MM-DD)"
    );
	
	jQuery.validator.addMethod("greaterThan", 
		function(value, element, params) {
			if(value != null && value != ''){
				if (!/Invalid|NaN/.test(new Date(value))) {
					return new Date(value) > new Date($(params).val());
				}

				return isNaN(value) && isNaN($(params).val()) 
					|| (Number(value) > Number($(params).val())); 
			} else{return true;}},'Deve essere maggiore della data di inizio.');
	
		jQuery.validator.addMethod("lowerThan", 
		function(value, element, params) {

			if (!/Invalid|NaN/.test(new Date(value))) {
				return new Date(value) < new Date($(params).val());
			}

			return isNaN(value) && isNaN($(params).val()) 
				|| (Number(value) < Number($(params).val())); 
	},'Deve essere minore di {0}.');
