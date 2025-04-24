jQuery(function($){
	'use strict';

	var $por_loading = {
		show: function(){
			$("#lwc-loading").show();
		},
		hide: function(){
			$("#lwc-loading").hide();
		}
	};
	var por = {
    	getList: function(page){
			var keyword = $("#lwc-keyword").val();
			if(typeof page === "undefined" || ! page){
				page = 0;
			}
			var params = {action: "ajaxLybpList", keyword: keyword, page: page};
            $por_loading.show();
            $.ajax({
                type: "GET",
                data: params,
                url: ajaxurl,
                success: function(res){
                    $por_loading.hide();
                    if(res.status == 1){
                        var html = "";
                        for(var i in res.data.list){
                            var item = res.data.list[i];
                            html += "<tr>" +
                                '<td>' + item.id + '</td>' +
                                "<td>" + item.ip + "</td>" +
                                "<td>" + item.email + "</td>" +
                                "<td>" + item.status + "</td>" +
                                "<td>" + item.added_date + "</td>" +
                                "</tr>";
                        }
    					$("#lwc-table tbody").html(html);
    					$("#total-items").html(res.data.count);

    					var page = res.data.page,
    					 	page_total = Math.round(res.data.count / res.data.pagesize);
    					$("#page").html(page);
    					$("#current-page").val(page);
    					if(page > 1){
							$("#lwc-page-prev").addClass("active");
    					}else{
    						$("#lwc-page-prev").removeClass("active");
    					}
    					if(page_total == 0){
    						page_total = 1;
    					}
    					if(page == page_total){
    						$("#lwc-page-next").removeClass("active");
    					}else{
    						$("#lwc-page-next").addClass("active");
    					}
    					$("#total-page").html(page_total);
                  	}
               	}
            });
    	}
    }

   


	$("#lwc-btn-save").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$por_loading.show();
		form_data += "&action=ajaxPorSettingSave";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$por_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#lwc-gen-api").on("click", function(){
		var $this = $(this);
		$por_loading.show();
		var data = {action: "ajaxPorApiKey"};
		$.ajax({
			type: "POST",
			data: data,
			url: ajaxurl,
			success: function(res){
				$por_loading.hide();
				$("#por_api_key").val(res);
			}
		});
	});

	

	$("#lwc-btn-message").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$por_loading.show();
		form_data += "&action=ajaxSaveMessage";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$por_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#lwc-list-search").on("click", function(){
       por.getList();
    });
   // por.getList();


});