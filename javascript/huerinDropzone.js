var optionsH = {};
var constructTemplate = (customOptions) => {
   optionsH = {
       // The configuration we've talked about above
       url:customOptions.url!=null?customOptions.url:WEB_ROOT+"/ajax/add-documento.php",
       dictDefaultMessage:'Click o arastre un archivo para agregar',
       autoProcessQueue: true,
       addRemoveLinks: true,
       parallelUploads: 100,
       maxFiles: 1,
       uploadMultiple: false,
       dictRemoveFile:'Eliminar',
       // The setting up of the dropzone
       init: function() {
           var currentForm =  jQ(this.element).parents('form:first');
           /*var myDropzone = this;
           //cambiamos el boton que hara que suba el archivo

           currentForm[0].querySelector("input[type=submit]").addEventListener("click", function(e) {
               // Make sure that the form isn't actually being sent.
               e.preventDefault();
               e.stopPropagation();
               myDropzone.processQueue();
           });*/
           // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
           // of the sending event because uploadMultiple is set to true.
           this.on("sendingmultiple", function(file,xhr,formData) {

               // Gets triggered when the form is actually being sent.
               // Hide the success button or the complete form.
           });
           this.on('sending',function(file,xhr,formData){
               //se agregan todos los elementos del formulario ala cabezera para enviarlo por post
               [...currentForm[0].elements].forEach((input) => {
                   formData.append(input.name,input.value);
                   });
           //     currentForm[0].querySelector('input[type=text]').each((el)=>{ console.log(el); } );
            });
           this.on("successmultiple", function(files, response) {
               // Gets triggered when the files have successfully been sent.
               // Redirect user or notify of success.
           });
           this.on("errormultiple", function(files, response) {
               // Gets triggered when there was an error sending the files.
               // Maybe show form again, and notify user of error
           });
           this.on("maxfilesexceeded", function(file) {
               this.removeAllFiles();
               this.addFile(file);
           });
           this.on("complete", function(file) {
               this.removeFile(file);
           });
           this.on('success',function(file,response){
              var respSplit = response.split("[#]");
              if(respSplit[0]=='ok'){
                  ShowStatusPopUp(respSplit[1]);
                  jQ("#"+respSplit[3]).html(respSplit[2]);
                  close_popup();
              }else
              {
                  ShowStatusPopUp(respSplit[1]);
              }
           });
       }
   }
   if(customOptions!=null){
       Object.keys(customOptions).forEach(
           (key) => { optionsH.key=customOptions[key];}
       );
   }
};
var createDropzone =(element,options) => {
    'use strict';
    if(options==null){
        constructTemplate();
    }
    else{
        constructTemplate(options);
    }
    options = optionsH;
    new Dropzone(element,options);
};
