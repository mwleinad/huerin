Dropzone.autoDiscover = false;
Event.observe(window, 'load', function () {

    if (document.getElementById("addPersonal"))
        Event.observe($('addPersonal'), "click", AddPersonalDiv);

    AddEditPersonalListeners = function (e) {
        var el = e.element();
        var del = el.hasClassName('spanDelete');
        var id = el.identify();
        if (del == true) {
            DeletePersonalPopup(id);
            return;
        }

        del = el.hasClassName('spanEdit');
        if (del == true) {
            EditPersonalPopup(id);
        }
        del = el.hasClassName('spanShowFile');
        if (del == true) {
            ShowFilePopup(id);
        }
    }

    $('contenido').observe("click", AddEditPersonalListeners);

});

function changePassword() {
    var conf = confirm('¿ Esta seguro de realizar esta accion ?');

    if (!conf)
        return;

    jQ.ajax({
        url: WEB_ROOT + '/ajax/personal.php',
        data: {type: 'changePass'},
        type: 'POST',
        beforeSend: function () {
            jQ('#loadPrint').html("<p>Espere un momento cambiando contraseñas...</p>");
            jQ('#loadPrint').show();
        },
        success: function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                jQ('#loadPrint').html("");
                jQ('#load-print').hide();
                ShowStatusPopUp(splitResp[1]);
                jQ('#contenido').html(splitResp[2]);
            } else {
                jQ('#loadPrint').html("");
                jQ('#loadPrint').hide();
                ShowStatusPopUp(splitResp[1]);
            }
        },
        error: function () {
            alert('error')
        }
    });
}

function deleteExpediente(id, personalId) {
    var conf = confirm('¿ Esta seguro de realizar esta accion ?');

    if (!conf)
        return;

    jQ.ajax({
        url: WEB_ROOT + '/ajax/personal.php',
        data: {type: 'deleteExpediente', id: id, personalId: personalId},
        type: 'POST',
        beforeSend: function () {
        },
        success: function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                ShowStatusPopUp(splitResp[1]);
                jQ('#content-expedientes').html('');
                jQ('#content-expedientes').html(splitResp[2]);
                LoadBoxDropzone();
            } else {
                ShowStatusPopUp(splitResp[1]);
            }
        },
        error: function () {
            alert('error')
        }
    });
}

function EditPersonalPopup(id) {
    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/personal.php',
        {
            method: 'post',
            parameters: {type: "editPersonal", personalId: id},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                FViewOffSet(response);
                Event.observe($('closePopUpDiv'), "click", function () {
                    EditPersonalPopup(0);
                });
                Event.observe($('editPersonal'), "click", EditPersonal);
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function EditPersonal() {
    new Ajax.Request(WEB_ROOT + '/ajax/personal.php',
        {
            method: 'post',
            parameters: $('personalForm').serialize(true),
			onLoading:function(){
				$('loader').style.display = 'block';
				document.getElementsByClassName('button_grey')[0].style.display = 'none';
			},
            onSuccess: function (transport) {
				$('loader').style.display = 'none';
				document.getElementsByClassName('button_grey')[0].style.display = 'block';
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] == "fail") {
                    ShowStatusPopUp(splitResponse[1])
                } else {
                    ShowStatusPopUp(splitResponse[1])
                    $('contenido').innerHTML = splitResponse[2];
                    AddPersonalDiv(0);
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function DeletePersonalPopup(id) {
    var message = "Realmente deseas eliminar este contador?";
    if (!confirm(message)) {
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/personal.php',
        {
            method: 'post',
            parameters: {type: "deletePersonal", personalId: id},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                ShowStatus(splitResponse[1])
                $('contenido').innerHTML = splitResponse[2];
                AddPersonalDiv(0);
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function AddPersonalDiv(id) {
    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }

    new Ajax.Request(WEB_ROOT + '/ajax/personal.php',
        {
            method: 'post',
            parameters: {type: "addPersonal"},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                FViewOffSet(response);
                Event.observe($('btnAddPersonal'), "click", AddPersonal);
                Event.observe($('fviewclose'), "click", function () {
                    AddPersonalDiv(0);
                });
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function AddPersonal() {
    new Ajax.Request(WEB_ROOT + '/ajax/personal.php',
        {
            method: 'post',
            parameters: $('personalForm').serialize(true),
			onLoading:function(){
            	$('loader').style.display = 'block';
				document.getElementsByClassName('button_grey')[0].style.display = 'none';
			},
            onSuccess: function (transport) {
				$('loader').style.display = 'none';
				document.getElementsByClassName('button_grey')[0].style.display = 'block';
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] == "fail") {
                    ShowStatusPopUp(splitResponse[1])
                } else {
                    ShowStatusPopUp(splitResponse[1])
                    $('contenido').innerHTML = splitResponse[2];
                    AddPersonalDiv(0);
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}
function ShowFilePopup(id) {
    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }
    new Ajax.Request(WEB_ROOT + '/ajax/personal.php',
        {
            method: 'post',
            parameters: {type: "showFile", personalId: id},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                FViewOffSet(response);
                Event.observe($('closePopUpDiv'), "click", function () {
                    EditPersonalPopup(0);
                });
                LoadBoxDropzone();
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}

function LoadBoxDropzone() {
    jQ('#file-up form.dropzone').each(function () {
        var _ext = jQ(this).data('ext').length ? jQ(this).data('ext') : '.pdf'
        var sp = (this.id).split('_');
        var existFile = jQ('#exist_file' + sp[1] + sp[2]).val();
        if (existFile)
            var defaultMessage = 'Ya existe un archivo, haga click en el icono de abajo <img src="' + WEB_ROOT + '/images/downCloud24.png"> para vista preliminar, de lo contrario arraste o click  en esta zona para actualizar<br>Si desea eliminar archivo click en  <img src="' + WEB_ROOT + '/images/deleteCloud24.png">';
        else
            var defaultMessage = 'Arrastre o click en esta zona para subir archivo';
        jQ(this).dropzone({
            dictDefaultMessage: defaultMessage,
            dictCancelUpload: 'Cancelar',
            dictCancelUploadConfirmation: '¿ Esta seguro de cancelar la carga ?',
            url: WEB_ROOT + '/ajax/expediente.php',
            paramName: 'file_' + sp[1] + sp[2],
            addRemoveLinks: true,
            dictRemoveFile: 'Eliminar',
            maxFileSize: 2,
            autoProcessQueue: true,
            maxFiles: 1,
            uploadMultiple: false,
            acceptedFiles: _ext,
            dictInvalidFileType: 'La extension de archivo no es valida',
            init: function () {
                var my = this;
                this.on('sending', function (file, xhr, formData) {
                    formData.append('idp', sp[1]);
                    formData.append('ide', sp[2]);
                });
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("complete", function (file) {
                    this.removeFile(file);
                });
                this.on('success', function (file, response) {
                    var splitResp = response.split('[#]');
                    if (splitResp[0] == 'ok') {
                        jQ('#content-expedientes').html(splitResp[1]);
                        LoadBoxDropzone();
                    } else
                        alert(splitResp[1]);
                });
                this.on('error', function (error, errorMessage, xhr) {
                    if (error.status == 'error') {
                        ShowErrorOnPopup(errorMessage, 1);
                    }
                })
            }
        });
    });
}
function deleteExpediente(id, personalId) {
    var conf = confirm('¿ Esta seguro de realizar esta accion ?');

    if (!conf)
        return;

    jQ.ajax({
        url: WEB_ROOT + '/ajax/personal.php',
        data: {type: 'deleteExpediente', id: id, personalId: personalId},
        type: 'POST',
        beforeSend: function () {
        },
        success: function (response) {
            var splitResp = response.split("[#]");
            if (splitResp[0] == 'ok') {
                ShowStatusPopUp(splitResp[1]);
                jQ('#content-expedientes').html('');
                jQ('#content-expedientes').html(splitResp[2]);
                LoadBoxDropzone();
            } else {
                ShowStatusPopUp(splitResp[1]);
            }
        },
        error: function () {
            alert('error')
        }
    });
}

function openEditWorkTeam() {
    jQ.ajax({
        url: WEB_ROOT + '/ajax/workTeam.php',
        data: {type: 'editWorkTeam', id: this.id},
        type: 'POST',
        success: function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
        error: function () {
            alert('error')
        }
    });
}
function deleteWorkTeam() {
    var flag = confirm('¿esta seguro de eliminar este registro?')
    if(!flag) return
    jQ.ajax({
        url: WEB_ROOT + '/ajax/workTeam.php',
        data: {type: 'deleteWorkTeam', id: this.id},
        type: 'POST',
        success: function (response) {
            var splitResp = response.split('[#]')
            ShowStatusPopUp(splitResp[1]);
            if (splitResp[0] == 'ok')
                jQ('#content_work_team').html(splitResp[2]);
        },
        error: function () {
            alert('error')
        }
    });
}

function saveWorkTeam() {
    var form = jQ(this).parents('form:first');
    jQ.ajax({
        url: WEB_ROOT + '/ajax/workTeam.php',
        data: form.serialize(true),
        type: 'POST',
        beforeSend: function () {
            jQ('#btnWorkTeam').hide();
            jQ('#loader').show();
        },
        success: function (response) {
            var splitResp = response.split("[#]");
            jQ('#btnWorkTeam').show();
            jQ('#loader').hide();
            ShowStatusPopUp(splitResp[1]);
            if (splitResp[0] == 'ok') {
                jQ('#content_work_team').html(splitResp[2]);
                close_popup();
            }
        },
        error: function () {
            alert('error')
        }
    });
}
function ExportToExcel()
{
    var resp = confirm("Esta seguro de generar este reporte? El proceso puede tardar varios minutos.");
    if(!resp)
        return;
    jQ.ajax({
        url:WEB_ROOT+'/ajax/personal.php',
        type:'post',
        data:{ 'type':'exportarExcel'},
        beforeSend: function () {

        },
        success:function (response) {
            window.location = response
        },
        error:function () {
            alert("Error al mostrar informacion!!");
        }
    });
}
jQ(document).on('click', '#btnWorkTeam', saveWorkTeam);
jQ(document).on('click', '.spanEditWorkTeam',openEditWorkTeam);
jQ(document).on('click', '.spanDelWorkTeam',deleteWorkTeam);
jQ(document).on('click', '#addWorkTeam', function () {
    jQ.ajax({
        url: WEB_ROOT + '/ajax/workTeam.php',
        data: {type: 'addWorkTeam'},
        type: 'POST',
        success: function (response) {
            grayOut(true);
            $('fview').show();
            FViewOffSet(response);
        },
        error: function () {
            alert('error')
        }
    });
});

