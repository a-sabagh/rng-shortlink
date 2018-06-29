jQuery(document).ready(function($){
    var admin_ajax = SHL_OBJ.admin_url;
    $(".shl-pagination-list li a").on("click",function(e){
        e.preventDefault();
        var item_class = $(this).attr("class");
        switch (item_class) {
            case 'paginate':
                console.log("paginate");
                break;
            case 'next':
                console.log("next");
                break;
            case 'prev':
                console.log("prev");
                break;
                
            default:
                console.log("default");
                break;
        }
    });
});