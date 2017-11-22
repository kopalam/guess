!function(){
	var base64,width,height,imgData;
	$("#editvipcard-image").change(function(){
		var objUrl = app.getObjectURL(this.files[0]);
		console.log(objUrl);
		imgDeal(objUrl);
	})
	function imgDeal(url){
		$("#editvipcard-form-file-imgVal").show();
		$("#editvipcard-form-file-imgVal").attr("src",url);
		$("#editvipcard-form-file-imgVal").load(function() { 
			width=$("#editvipcard-form-file-imgVal").width();
			height=$("#editvipcard-form-file-imgVal").height();
			var	canvas=document.createElement("canvas"),
			imgContext=canvas.getContext('2d');
			canvas.width=width;
			canvas.height=height;
			imgContext.drawImage(this,0,0,canvas.width, canvas.height);
			base64=canvas.toDataURL("image/jpg",1);
		}); 
	}
		
		
		
		
		$("#editvipcard-uploadBtn").click(function(){		
//			console.log(base64);
			if(base64!==undefined){
				$(".editvipcard-loadTip").show();
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
							$(".editvipcard-loadTip").html("图片上传成功");
							$.ajax({
								type:"post",
								url:"../adminajax/VipCard",
								data:{
									"image":res.data.src,
								},
								dataType:"json",
								success:res=>{
									console.log(res);
									$(".editvipcard-loadTip").hide();
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
			}else{
				alert("请选择图片");
			}
		})
		
	}()
