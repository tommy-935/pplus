jQuery(function ($) {
    const $loading = $("#big-loading");

    // Duplicate Post
    $(".btn-losml-duplicate").on("click", function () {
        const $btn = $(this);
        const requestData = {
            id: $btn.data("id"),
            langid: $btn.data("langid"),
            post_type: $btn.data("post_type"),
            nonce: $btn.data("nonce"),
            action: "make_duplicate_post"
        };

        $loading.show();

        $.post(ajaxurl, requestData, function (response) {
            $loading.hide();
            if (response.status === 1) {
                $btn.replaceWith(`<a href="${response.data.url}">Edit</a>`);
                alert("Duplicated successfully");
            } else {
                alert("Duplication failed: " + response.data.error);
            }
        });
    });

    // Save Language Form
    $("#losml-btn-save").on("click", function () {
        const $form = $(this).closest("form");
        const formData = $form.serialize();

        $loading.show();

        $.post(ajaxurl, formData, function (response) {
            $loading.hide();
            if (response.status === 1) {
                alert("Saved successfully");
            } else {
                alert("Save failed: " + response.data.error);
            }
        });
    });

    // Toggle Translation Box
    $(".losml-toggle-trans").on("click", function () {
        $(this).closest(".losml-trans-item").next(".losml-string-trans-box").toggleClass("losml-show-trans-box");
    });

    // Save Translated String
    $(".losml-btn-string-save").on("click", function () {
        const $btn = $(this);
        const $input = $btn.closest(".losml-string-trans-actions").prev("textarea");
        const requestData = {
            id: $btn.data("id"),
            lang_id: $btn.data("lang-id"),
            string_id: $btn.data("string-id"),
            trans_text: $input.val(),
            action: "save_losml_string_translations"
        };

        $loading.show();

        $.post(ajaxurl, requestData, function (response) {
            $loading.hide();
            if (response.status === 1) {
                alert("Saved successfully");
            } else {
                alert("Save failed: " + response.data.error);
            }
        });
    });

    // Auto reload after adding tag via AJAX
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (
            xhr &&
            xhr.readyState === 4 &&
            xhr.status === 200 &&
            typeof settings.data === "string" &&
            settings.data.includes("action=add-tag") &&
            !xhr.responseText.includes("wp_error")
        ) {
            location.reload();
        }
    });


    'use strict';

	var $losml_loading = {
		show: function(){
			$("#losml-loading").show();
		},
		hide: function(){
			$("#losml-loading").hide();
		}
	};
	var losml = {
    	getList: function(page){
			return ;
			var keyword = $("#losml-keyword").val();
			if(typeof page === "undefined" || ! page){
				page = 0;
			}
			var params = {action: "ajaxLseList", keyword: keyword, page: page, _wpnonce: $("#lymos-email-nonce").val()};
            $losml_loading.show();
            $.ajax({
                type: "GET",
                data: params,
                url: ajaxurl,
                success: function(res){
                    $losml_loading.hide();
                    if(res.status == 1){
                        var html = "";
                        for(var i in res.data.list){
							var resend = '<a href="javascript:void(0);" class="losml-resend" data-id="{$item.id}">Resend</a>';
							var opened = item.opened;
							if(lymosstmp_license == 'invalid'){
								resend = '<a href="javascript:void(0);" class="losml-resend-pro">Get Pro</a>';
								opened = '';
							}
                            var item = res.data.list[i];
                            html += "<tr>" +
                                '<td>' + item.id + '</td>' +
                                "<td>" + item.email + "</td>" +
                                "<td>" + item.subject + "</td>" +
                                `<td><a href="javascript:void(0);" class="losml-showbody">Show Body</a><div class="losml-email-body">
									<a class="losml-btn-close" href="javascript:void(0);">X</a>` + item.body + "</div></td>" +
                                "<td>" + item.added_date + "</td>" +
								"<td>" + opened + "</td>" +
								"<td>" + item.status + "</td>" +
								`<td>
									${resend}
								</td>` +
                                "</tr>";
                        }
    					$("#losml-table tbody").html(html);
    					$("#total-items").html(res.data.count);

    					var page = res.data.page,
    					 	page_total = Math.round(res.data.count / res.data.pagesize);
    					$("#page").html(page);
    					$("#current-page").val(page);
    					if(page > 1){
							$("#losml-page-prev").addClass("active");
    					}else{
    						$("#losml-page-prev").removeClass("active");
    					}
    					if(page_total == 0){
    						page_total = 1;
    					}
    					if(page == page_total){
    						$("#losml-page-next").removeClass("active");
    					}else{
    						$("#losml-page-next").addClass("active");
    					}
    					$("#total-page").html(page_total);
                  	}
               	}
            });
    	}
    }

	$(".losml-email-table").on("click", ".losml-showbody", function(){
		const $this = $(this);
		const $target = $this.parent().find(".losml-email-body");
		$target.clone().appendTo("body");
		$("body > .losml-email-body").find(".losml-btn-close").addClass("losml-show");
		$("#losml-loading").addClass("none-img");
		$losml_loading.show();
	});

	$("body").on("click", ".losml-btn-close.losml-show", function(e){
		const $this = $(this);
		const $target = $this.parent();
		$target.remove();
		$("#losml-loading").removeClass("none-img");
		$losml_loading.hide();
	});

    $(".losml-page").on("click", "#losml-page-next.active", function(){
    	var page = parseInt($("#current-page").val()) + 1;
    	losml.getList(page);
    });

	$(".losml-page").on("click", "#losml-page-prev.active", function(){
    	var page = parseInt($("#current-page").val()) - 1;
    	losml.getList(page);
    });

	$(".losml-tab").on("click", ".tab-item", function(){
		var $this = $(this);
		$(".tab-item").removeClass("active");
		$this.addClass("active");
		var $target = $("." + $this.data("target"));
		$(".losml-cont-tab").removeClass("active");
		$target.addClass("active");
	});

	$("#losml-btn-setting").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$losml_loading.show();
		form_data += "&action=losml_save_setting";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$losml_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#losml-btn-message").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$losml_loading.show();
		form_data += "&action=ajaxSaveMessage";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$losml_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#losml-resend").on("click", function(){
		var $this = $(this);
		
		var form_data = 'id=' + $this.data("id");
		$losml_loading.show();
		form_data += "&action=lymos_smtp_resend";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$losml_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#losml-btn-license").on("click", function(){
		var $this = $(this);
		var $form = $this.parents("form");
		var form_data = $form.serialize();
		$losml_loading.show();
		form_data += "&action=checkLicense";
		$.ajax({
			type: "POST",
			data: form_data,
			url: ajaxurl,
			success: function(res){
				$losml_loading.hide();
				alert(res.data);
			}
		});
	});

	$("#losml-list-search").on("click", function(){
       losml.getList();
    });
    // losml.getList();

});
