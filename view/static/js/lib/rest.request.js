/**
 * ajax请求封装.
 * @type {{ajaxSilence: RestRequest.ajaxSilence}}
 */
var RestRequest = {
    /**
     * 静默请求（无请求特效和前、后置处理）
     * @param url
     * @param params
     * @param success
     * @param async
     * @param fail
     */
    restRequest: function(url, params, success, method, async, fail) {
        var obj = null;
        async = async || true;
        method = method || "POST";
        if (params) {
            params = JSON.stringify(params);
        }
        $.ajax({
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            type: method,
            url: url,
            data:params,
            async: async,
            timeout : 30000,
            cache : false,

            success: function(data){
                success(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var result = jqXHR.responseJSON ? jqXHR.responseJSON : JSON.parse(jqXHR.responseText);
                if (fail) {
                    fail(result);
                }
            }
        });
    },

    get: function(url, success, fail, async) {
        this.restRequest(url, {}, success, 'GET', async, fail);
    },

    post: function (url, params, success, fail, async) {
        this.restRequest(url, params, success, 'POST', async, fail);
    },

    put: function (url, params, success, fail, async) {
        this.restRequest(url, params, success, 'PUT', async, fail);
    },

    delete: function (url, success, fail, async) {
        this.restRequest(url, {}, success, 'DELETE', async, fail);
    }
};