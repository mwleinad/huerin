function pure_autocomplete(inp,type,ruta, fillable = null, parent_id = null) {
    var responseKeyValueJson = [];
    var currentFocus;

    inp.addEventListener("input", function(e) {
        resetValues()
        var a, b, i;
        var val = this.value;
        closeAllLists();
        if (!val){

            return false;
        }
        currentFocus = -1;
        a = document.createElement("div");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        this.parentNode.appendChild(a);
        GetData(val,type,ruta).then(function(res) {
            responseKeyValueJson =  res;
            if(responseKeyValueJson.length > 0) {
                responseKeyValueJson.forEach(function (objeto){
                    var str = objeto.key+' ' + objeto.value;
                    str = str.toUpperCase();
                    var valM = val.toUpperCase();
                    var indexInit =  str.indexOf(valM);
                    if (str.substr(indexInit, val.length).toUpperCase() === val.toUpperCase()) {
                        b = document.createElement("div");
                        b.setAttribute('title',str);
                        b.innerHTML = boldString(val.toUpperCase(),str);
                        b.innerHTML += "<input type='hidden' value='" + JSON.stringify(objeto) + "'>";
                        b.addEventListener("click", function (e) {
                            var inpJson = JSON.parse(this.getElementsByTagName("input")[0].value)
                            inp.value = inpJson.value;
                            if(fillable.length > 0)
                                fillData(inpJson)
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                })
            }
            else{
                b = document.createElement("div");
                b.innerHTML = "<span style='font-family: Courier New, Courier, monospace !important'>"+boldString("No se encontraron registros ","No se encontraron registros ")+"</span>";
                a.appendChild(b);
            }
        },function(error) {
            alert(error);
        })
    });
    inp.addEventListener("keydown", function (e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        var control = document.getElementById(this.id + "autocomplete-list");
        if(x)
            x = x.getElementsByTagName("div");
        else
            return;
        if(x.length<=0)
            return;

        if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) { //up
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });

    function boldString(cadena,cadenaCompleta) {
        for (var i = 0; i < cadenaCompleta.length; i++) {
            if (cadenaCompleta.substring(i, i + cadena.length) == cadena) {
                cadenaCompleta= cadenaCompleta.substring(0, i) + cadena.bold() + cadenaCompleta.substring(i + cadena.length, cadenaCompleta.length);
                return cadenaCompleta;
            }
        }
        return cadenaCompleta;
    }
    function addActive(x) {
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
            x[currentFocus].classList.add("autocomplete-active");
    }
    function scrollContent(px,selector){
        var ident =  selector.id;
        var top = $('#'+ident).position().top;
        $("#"+ident).css("top", top+px);
    }
    function removeActive(x) {
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(element) {
        delete responseKeyValueJson;
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (element !== x[i] && element !== inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    function GetData(word, type, ruta) {
        var formData = new FormData();
        formData.append("type", type);
        formData.append("query", word);
        if(parent_id !== null)
            formData.append("parent_id", parent_id);

        var xmlhttp = new XMLHttpRequest();
        return new Promise(function(resolve,reject){
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState === 4) {
                    if (xmlhttp.status !== 200) {
                        reject("Error, status code = " + xmlhttp.status);
                    }else{
                        resolve(JSON.parse(xmlhttp.responseText));
                    }
                }
            }
            xmlhttp.open("post", ruta,true);
            xmlhttp.send(formData);
        });
    }
    function fillData(data) {
        for (field of fillable) {
            if(document.getElementById(field)!=null)
                document.getElementById(field).value = data[field]
        }
    }
    function resetValues () {
        if(document.getElementById('customer_exists') !== null)
            document.getElementById('customer_exists').value = ''
        if(document.getElementById('contract_exists') !== null)
            document.getElementById('contract_exists').value = ''
    }
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}
