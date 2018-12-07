define([
        'views/pages/AdminPageView',
        'views/pages/admin/PageAdminListingView',
    ],
    function(
        AdminPageView, PageAdminListingView

    ){
        //
        // Page View Factory
        //
        return function(pageType){
            //
            //
            //
            switch(pageType){

                case 'admin_index':
                    return PageAdminListingView;
                default :
                    // default case return an abstract page
                    return AdminPageView;
            }
        }
    }
)