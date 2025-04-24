jQuery(function($){
	'use strict';

	var $lybp_loading = {
		show: function(){
			$("#lybp-loading").show();
		},
		hide: function(){
			$("#lybp-loading").hide();
		}
	};
	var lybp = {
    	getList: function(page){
			var keyword = $("#lybp-keyword").val();
			if(typeof page === "undefined" || ! page){
				page = 0;
			}
			var params = {action: "ajaxLybpList", keyword: keyword, page: page};
            $lybp_loading.show();
            $.ajax({
                type: "GET",
                data: params,
                url: ajaxurl,
                success: function(res){
                    $lybp_loading.hide();
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
    					$("#lybp-table tbody").html(html);
    					$("#total-items").html(res.data.count);

    					var page = res.data.page,
    					 	page_total = Math.round(res.data.count / res.data.pagesize);
    					$("#page").html(page);
    					$("#current-page").val(page);
    					if(page > 1){
							$("#lybp-page-prev").addClass("active");
    					}else{
    						$("#lybp-page-prev").removeClass("active");
    					}
    					if(page_total == 0){
    						page_total = 1;
    					}
    					if(page == page_total){
    						$("#lybp-page-next").removeClass("active");
    					}else{
    						$("#lybp-page-next").addClass("active");
    					}
    					$("#total-page").html(page_total);
                  	}
               	}
            });
    	}
    }

    $(".lybp-page").on("click", "#lybp-page-next.active", function(){
    	var page = parseInt($("#current-page").val()) + 1;
    	lybp.getList(page);
    });

	$(".lybp-page").on("click", "#lybp-page-prev.active", function(){
    	var page = parseInt($("#current-page").val()) - 1;
    	lybp.getList(page);
    });

	$(".lybp-tab").on("click", ".tab-item", function(){
		var $this = $(this);
		$(".tab-item").removeClass("active");
		$this.addClass("active");
		var $target = $("." + $this.data("target"));
		$(".lybp-cont-tab").removeClass("active");
		$target.addClass("active");
	});

	$("#lybp-btn-add").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$lybp_loading.show();
		form_data += "&action=ajaxAddLybpRule";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$lybp_loading.hide();
				alert(res.data);
				lybp.getList();
			}
		});
	});

	$("#lybp-btn-backup-db").on("click", function(){
		var $this = $(this);
		$lybp_loading.show();
		var data = {action: "ajaxLybpBackupDb"};
		$.ajax({
			type: "POST",
			data: data,
			url: ajaxurl,
			success: function(res){
				$lybp_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#lybp-btn-backup-file").on("click", function(){
		var $this = $(this);
		$lybp_loading.show();
		var data = {action: "ajaxLybpBackupFile"};
		$.ajax({
			type: "POST",
			data: data,
			url: ajaxurl,
			success: function(res){
				$lybp_loading.hide();
				alert(res.data);
			}
		});
	});
	

	$("#lybp-btn-message").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$lybp_loading.show();
		form_data += "&action=ajaxSaveMessage";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$lybp_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#lybp-list-search").on("click", function(){
       lybp.getList();
    });
   // lybp.getList();


});