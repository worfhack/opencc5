define(['underscore', 'backbone', 'moment'], function (_, Backbone, moment) {
    return Backbone.Collection.extend({


        url: function () {
            var ref_tour = this.ref_tour,
                page = this.page,
                directCustomer = this.directCustomer;
            return gl_page_info.baseUrl + "/tour/" + ref_tour + "/feed?page="+page+"&directCustomer=" + directCustomer;
        },
        initialize: function (options) {
            var collection = this;
            collection.ref_tour = options.ref_tour;
            collection.directCustomer = options.directCustomer;
            collection.page = 0;

        },
        next:function () {
            var collection = this;
            collection.page++;
            collection.fetch();
        }
    });
});