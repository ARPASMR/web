
    $(document).ready(function () {

        // ### LogIn ###
        $("#loginForm").submit(function (e) {
            $("#loginError").css("display",'none');
            e.preventDefault();
            var args = "Email=" + $("#Email").val();
            args += "&Password=" + $("#Password").val();
            args += "&tkn=" + $("#tkn").val();
            args += "&login=true";
            $.ajax({
                type: "POST",
                url: "login.php",
                data:  args,
                dataType: "text",
                success: function(response) {
                    if(response=="OK"){
                        location.reload(true);
                    } else {
                        this.error();
                    }
                },
                error:function (response) {
                    $("#loginError").css("display",'block');
                }
            });
        });

        // ### LogOut ###
        $("#logoutButton").click(function(){
            var args = "logout=true";
            args += "&tkn=" + $("#tkn").val();
            $.ajax({
                type: "POST",
                url: "login.php",
                data:  args,
                dataType: "text",
                success: function(response) {
                    location.reload(true);
                }
            });
        });


	var idUtenteInModifica = getURLParameter("id");
	var remoteOptions = {
                        url: 'login.php',
                        type: 'POST',
                        data: {
                            'verificaEmail': 'true',
                            'tkn': $("#tkn").val()
                        	}
			};
	
        $("form#modificaUtente").validate({
            rules: {
                Nome: {
                    required: true
                },
                Cognome: {
                    required: true
                },
                Email: {
                    required: true,
                    email: true,
                    remote: (idUtenteInModifica != null) ? false : remoteOptions
                }/*,
                Password: {
                    required: true
                },
                LivelloUtente: {
                    required: true
                }*/
            },
            messages: {
                Email: {
                    remote: 'Esiste gia\' un utente con questa email.'
                }
            }
        });


    });

    function applicaStazioniAssegnate(idTabella, IDutente, toDo){
        var IDs = '';
        $.each($('#'+idTabella+' input[type=checkbox]:checked'), function(){
            IDs += $(this).val()+',';
        });
        IDs = IDs.replace(/\,+$/g, '');

        var params = {
            "do": toDo,
            "IDutente": IDutente,
            "IDstazione": IDs
        };

        httpRequest("stazioniAssegnate.php", params, "get");

    }

    function httpRequest(path, parameters, method){
        method = method || "post";
        var form = $('<form></form>');
        form.attr("method", "get");
        form.attr("action", path);
        $.each(parameters, function(key, value) {
            var field = $('<input></input>');
            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);
            form.append(field);
        });
        $(document.body).append(form);
        form.submit();
    }


function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}
