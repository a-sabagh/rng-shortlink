jQuery(document).ready(function ($) {
    var admin_ajax = SHL_OBJ.admin_url;

    /* click on numeric items */
    function paginate_lists(page) {
        $.ajax({
            url: admin_ajax,
            type: "POST",
            data: {
                action: "click_view_paginate",
                page : page
            },
            beforeSend: function(){ $(".shl-preloader").show(); },
            success: function(respons){
                $(".shl-click-view-table tbody").html(respons.report);
                $(".shl-pagination-list").html(respons.pagination);
                $(".shl-preloader").hide();
            },
            error: function(){ console.log("Error with rng-shortlink plugin ajax report problem"); }
        });
    }

    /* click on next item */
    function next_list(current) {
        $.ajax({
            url: admin_ajax,
            type: "POST",
            data: {
                action: "click_view_next",
                page : current+1
            },
            beforeSend: function(){ $(".shl-preloader").show(); },
            success: function(respons){
                $(".shl-click-view-table tbody").html(respons.report);
                $(".shl-pagination-list").html(respons.pagination);
                $(".shl-preloader").hide();
            },
            error: function(){ console.log("Error with rng-shortlink plugin ajax report problem"); }
        });
    }

    /* click on previous item */
    function prev_list(current) {
        $.ajax({
            url: admin_ajax,
            type: "POST",
            data: {
                action: "click_view_prev",
                page : current-1
            },
            beforeSend: function(){ $(".shl-preloader").show(); },
            success: function(respons){
                $(".shl-click-view-table tbody").html(respons.report);
                $(".shl-pagination-list").html(respons.pagination);
                $(".shl-preloader").hide();
            },
            error: function(){ console.log("Error with rng-shortlink plugin ajax report problem"); }
        });
    }

    /* pagination click event */
    $(".shl-pagination-list").on("click", "li a", function (e) {
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