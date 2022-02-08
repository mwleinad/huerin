var driverApi = function () {
    var url = URL_API;
    var generateToken = function (params) {
        jQ.ajax({
            type: "post",
            async: false,
            url: url + '/auth/login',
            data: { email: params.email, password: params.password},
        }).done(function (response, status, xhr) {
            var jwt = xhr.getResponseHeader('Authorization');
            localStorage.setItem('huerinToken', jwt);
        }).fail(function (err) {
        });
    };
    var getToken = () => {
        return localStorage.getItem('huerinToken');
    }
    var setHeader = function (xhr) {
        xhr.setRequestHeader('Authorization', getToken)
    }
    var refreshToken = function () {
        var currentToken = getToken()
        if(currentToken === null || currentToken === 'undefined') {
            generateToken(PARAMSLOGIN)
        } else {
            jQ.ajax({
                type: "GET",
                async: false,
                url: url + '/auth/refresh',
                data: {},
                beforeSend: setHeader,
            }).done(function (response, status, xhr) {
                var jwt = xhr.getResponseHeader('Authorization');
                localStorage.setItem('huerinToken', jwt);
            }).fail(function (err) {
            });
        }

        return getToken()
    };
    return {
        init: (param) => generateToken ( param ),
        setHeader: () => setHeader(),
        refreshToken: () => refreshToken(),
    };
}();
jQuery(document).ready(function () {
   driverApi.init(PARAMSLOGIN)
})
