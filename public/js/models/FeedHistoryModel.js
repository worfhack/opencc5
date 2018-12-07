define(['underscore','backbone','moment'],function (_,Backbone,moment) {
    return Backbone.Model.extend({

        url : gl_page_info.baseUrl + "/tourdetail/",
    });
});