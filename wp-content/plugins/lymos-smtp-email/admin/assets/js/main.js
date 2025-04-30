jQuery(function($){
	'use strict';

	var $lymos_loading = {
		show: function(){
			$("#lse-loading").show();
		},
		hide: function(){
			$("#lse-loading").hide();
		}
	};
	var lse = {
    	getList: function(page){
			var keyword = $("#lse-keyword").val();
			if(typeof page === "undefined" || ! page){
				page = 0;
			}
			var params = {action: "ajaxLseList", keyword: keyword, page: page, _wpnonce: $("#lymos-email-nonce").val()};
            $lymos_loading.show();
            $.ajax({
                type: "GET",
                data: params,
                url: ajaxurl,
                success: function(res){
                    $lymos_loading.hide();
                    if(res.status == 1){
                        var html = "";
                        for(var i in res.data.list){
                            var item = res.data.list[i];
                            html += "<tr>" +
                                '<td>' + item.id + '</td>' +
                                "<td>" + item.email + "</td>" +
                                "<td>" + item.subject + "</td>" +
                                `<td><a href="javascript:void(0);" class="lse-showbody">Show Body</a><div class="lse-email-body">
									<a class="lse-btn-close" href="javascript:void(0);">X</a>` + item.body + "</div></td>" +
                                "<td>" + item.added_date + "</td>" +
                                "</tr>";
                        }
    					$("#lse-table tbody").html(html);
    					$("#total-items").html(res.data.count);

    					var page = res.data.page,
    					 	page_total = Math.round(res.data.count / res.data.pagesize);
    					$("#page").html(page);
    					$("#current-page").val(page);
    					if(page > 1){
							$("#lse-page-prev").addClass("active");
    					}else{
    						$("#lse-page-prev").removeClass("active");
    					}
    					if(page_total == 0){
    						page_total = 1;
    					}
    					if(page == page_total){
    						$("#lse-page-next").removeClass("active");
    					}else{
    						$("#lse-page-next").addClass("active");
    					}
    					$("#total-page").html(page_total);
                  	}
               	}
            });
    	}
    }

	$(".lse-email-table").on("click", ".lse-showbody", function(){
		const $this = $(this);
		const $target = $this.parent().find(".lse-email-body");
		$target.clone().appendTo("body");
		$("body > .lse-email-body").find(".lse-btn-close").addClass("lse-show");
		$("#lse-loading").addClass("none-img");
		$lymos_loading.show();
	});

	$("body").on("click", ".lse-btn-close.lse-show", function(e){
		const $this = $(this);
		const $target = $this.parent();
		$target.remove();
		$("#lse-loading").removeClass("none-img");
		$lymos_loading.hide();
	});

    $(".lse-page").on("click", "#lse-page-next.active", function(){
    	var page = parseInt($("#current-page").val()) + 1;
    	lse.getList(page);
    });

	$(".lse-page").on("click", "#lse-page-prev.active", function(){
    	var page = parseInt($("#current-page").val()) - 1;
    	lse.getList(page);
    });

	$(".lse-tab").on("click", ".tab-item", function(){
		var $this = $(this);
		$(".tab-item").removeClass("active");
		$this.addClass("active");
		var $target = $("." + $this.data("target"));
		$(".lse-cont-tab").removeClass("active");
		$target.addClass("active");
	});

	$("#lse-btn-add").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$lymos_loading.show();
		form_data += "&action=ajaxSaveSmtp";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$lymos_loading.hide();
				alert(res.data);
				lse.getList();
			}
		});
	});

	$("#lse-btn-message").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$lymos_loading.show();
		form_data += "&action=ajaxSaveMessage";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$lymos_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#lse-btn-license").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$lymos_loading.show();
		form_data += "&action=checkLicense";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$lymos_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#lse-list-search").on("click", function(){
       lse.getList();
    });
    lse.getList();


});