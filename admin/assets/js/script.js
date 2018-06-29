jQuery(document).ready(function ($) {
    var admin_ajax = SHL_OBJ.admin_url;

    /**/
    function paginate_lists(page) {
        $.ajax({
            url: admin_ajax,
            type: "POST",
            data: {
                action: "click_view_paginate",
                page : page
            },
            beforeSend: function(){},
            success: function(){},
            error: function(){}
        });
    }

    /**/
    function next_list(current) {
        $.ajax({
            url: admin_ajax,
            type: "POST",
            data: {
                action: "click_view_next",
                page : current+1
            },
            beforeSend: function(){},
            success: function(){},
            error: function(){}
        });
    }

    /**/
    function prev_list(current) {
        $.ajax({
            url: admin_ajax,
            type: "POST",
            data: {
                action: "click_view_prev",
                page : current-1
            },
            beforeSend: function(){},
            success: function(){},
            error: function(){}
        });
    }

    /**/
    $(".shl-pagination-list li a").on("click", function (e) {
        e.preventDefault();
        var item_class = $(this).attr("class");
        switch (item_class) {
            case 'paginate':
                var page = $(this).data("paginate");
                paginate_lists(page);
                break;
            case 'next':
                var current = $(".shl-pagination-list li a.current").data("paginate");
                next_list(current);
                break;
            case 'prev':
                var current = $(".shl-pagination-list li a.current").data("paginate");
                prev_list(current);
                break;
            default:
                return true;
                break;
        }//end switch
    });
});