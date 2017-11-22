!function(){
		var base64,width,height,imgData,imginitSrc;
		
		imginitSrc=$("#tpl-form-file-imgVal").attr("src");
		
		$("#image").change(function(){
			var objUrl = app.getObjectURL(this.files[0]);
			console.log(objUrl);
			imgDeal(objUrl);
		})
		
		function imgDeal(url){
			$("#tpl-form-file-imgVal").show();
			$("#tpl-form-file-imgVal").attr("src",url);
			$("#tpl-form-file-imgVal").load(function() { 
				width=$("#tpl-form-file-imgVal").width();
				height=$("#tpl-form-file-imgVal").height();
				var	canvas=document.createElement("canvas"),
				imgContext=canvas.getContext('2d');
				canvas.width=width;
    				canvas.height=height;
				imgContext.drawImage(this,0,0,canvas.width, canvas.height);
				base64=canvas.toDataURL("image/jpg",1);
			}); 
		}
		
		
		//增加商家
		$("#fonrmlineBtn").click(function(){
			var shop_name=$("#shop_name").val(),
				dishes=$("#dishes").val(),
				price=$("#price").val(),
				toper=$("#toper").val(),
				location=$("#location").val(),
				telphone=$("#telphone").val(),
				address=$('#address').val(),
				sole=$('#sole:checked').val(),
				amount=$('#amount').val(),
				cons=$('#cons').val(),
				use_rule=$('#use_rule').val();

			var result=app.checkInput([
					{name:"商家名称",value:shop_name}
				]);
				
			if(result){
//				 console.log(base64);
				$(".loadTip").show();
				var image=app.dataURLtoBlob(base64);
				var inputData = new FormData();
				inputData.append("image",image);
				$.ajax({
					type:"post",
					url:"../upload/uploadImage",
					data:inputData,
					dataType:"json",
					processData: false,
					contentType: false,
					cache: false,
					success:res=>{
						console.log(res);
						if(res.status===0){
							$(".loadTip").html("图片上传成功");
							$.ajax({
								type:"post",
								url:"../adminAjax/addVipShop",
								data:{

									"shop_name":shop_name,
									"dishes":dishes,
									"price":price,
									"toper":toper,
									"location":location,
									"image":res.data.src,
									"telphone":telphone,
									"address":address,
									"amount":amount,
									"sole":sole,
									"use_rule":use_rule,
									"cons":cons
								},
								dataType:"json",
								success:res=>{
									console.log(res);
									$(".loadTip").html("图片正在上传...");
									$(".loadTip").hide();
									if(res.status===0){
										window.location.reload();
									}
								},
								fail:res=>{
									console.log(res);
								}
							});
						}
					},
					fail:res=>{
						console.log(res);
					}
				});
			}
		})
		
		
		//该修商家
		$("#editlineBtn").click(function(){
			console.log($("#id").val());
			var id=$("#id").val(),
				shop_name=$("#shop_name").val(),
				dishes=$("#dishes").val(),
				price=$("#price").val(),
				toper=$("#toper").val(),
				location=$("#location").val(),
				telphone=$("#telphone").val(),
				address=$('#address').val(),
				sole=$('#sole:checked').val(),
				amount=$('#amount').val(),
				cons=$('#cons').val(),
				use_rule=$('#use_rule').val();


			
			var result=app.checkInput([
					{name:"商家名称",value:shop_name}
				]);
				
			if(result){
				/*判断图片资源是否有改变
				 * 没有改变直接上传
				 * 改变则先获取图片资源
				*/
				if(imginitSrc===$("#tpl-form-file-imgVal").attr("src")){
					console.log("https://sale.imchaotan.com"+$("#tpl-form-file-imgVal").attr("src"));
					ajaxFn("https://sale.imchaotan.com"+$("#tpl-form-file-imgVal").attr("src"),function(res){
						console.log(res);
						if(res.status===0){
							window.location.reload();
						}
					})
				}else{
					console.log(base64);
					$(".loadTip").show();
					$(".loadTip").html("图片正在上传...");
					var image=app.dataURLtoBlob(base64);
					var inputData = new FormData();
					inputData.append("image",image);
					$.ajax({
						type:"post",
						url:"../upload/uploadImage",
						data:inputData,
						dataType:"json",
						processData: false,
						contentType: false,
						cache: false,
						success:res=>{
							console.log(res);
							if(res.status===0){
								$(".loadTip").html("图片上传成功");
								ajaxFn(res.data.src,function(res){
									console.log(res);
									$(".loadTip").hide();
									if(res.status===0){
										window.location.reload();
									}
								})
							}
						},
						fail:res=>{
							console.log(res);
						}
					});
				}
					 
			}
			
			function ajaxFn(img,cb){
				console.log(img)
				$.ajax({
					type:"post",
					url:"../adminAjax/editVipShop",
					data:{
						"id":id,
						"shop_name":shop_name,
						"dishes":dishes,
						"price":price,
						"toper":toper,
						"location":location,
						"image":img,
						"telphone":telphone,
						"address":address,
						"amount":amount,
						"sole":sole,
						"use_rule":use_rule,
						"cons":cons
					},
					dataType:"json",
					success:res=>{
						cb&&cb(res)
					},
					fail:res=>{
						console.log(res);
					}
				});
			}
		})

	}()