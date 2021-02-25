var driverApi = function () {
    var url = URL_API;
    var getToken = function (params) {
        jQ.ajax({
            type: "post",
            url: url + '/auth/login',
            data: { email: params.email, password: params.password},
        }).done(function (response, status, xhr) {
            var jwt = xhr.getResponseHeader('Authorization');
            localStorage.setItem('huerinToken', jwt);
        }).fail(function (err) {
            console.log(err)
        });
    };

    var refreshToken = function () {
        token = localStorage.getItem('huerinToken');
        if(token === 'undefined' || token === null) {
            getToken();
            token = localStorage.getItem('huerinToken');
        } else {
            jQ.ajax({
                type: "get",
                url: url + '/auth/refresh',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", token);
                }
            }).done(function (response, status, xhr) {
                var jwt = xhr.getResponseHeader('Authorization');
                localStorage.setItem('huerinToken', jwt);
            }).fail(function (err) {
                console.log(err)
            });
        }
        return localStorage.getItem('huerinToken');
    };
    return {
        init: function(par) {
            getToken(par);
        },
        refreshToken: function () {
           return  refreshToken();
        }
    };
}();
jQuery(document).ready(function () {
   driverApi.init(PARAMSLOGIN)
})
