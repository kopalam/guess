!function(){
		var base64,width,height,imgData;
		$("#image").change(function(){
			objUrl = getObjectURL(this.files[0]);
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
				imgContext.drawImage(this,width, height);
				base64=canvas.toDataURL("image/jpg",1);
			}); 
		}
		
		
		
		
		$("#fonrmlineBtn").click(function(){
			var article_title=$("#article_title").val();
			var shop_name=$("#shop_name").val();
			var article_writer=$("#article_writer").val();
			var location=$("#location").val();
			var telphone=$("#telphone").val();
			var abstract=$('#abstract').val();
			var address=$('#address').val();
			var sole=$('#sole:checked').val();
			var tips=$('#tips').val();
			var cons=$('#cons').val();
			var contents=$('#contents').val();
			var tags_id=$("#tags_id").val();

			
			var result=configFn.checkInput([
					{name:"文章标题",value:article_title},
					// {name:"发布时间",value:usertimeVal},
					// {name:"关键字",value:userseoVal},
					// {name:"分类",value:userclassVal}
				]);
				
			
			if(result){
				// console.log(base64);
				console.log(sole);
				$(".loadTip").show();
				var image=dataURLtoBlob(base64);
				var inputData = new FormData();
				inputData.append("image",image);
				$.ajax({
					type:"post",
					url:"https://sale.imchaotan.com/api/upload/uploadImage",
					data:inputData,
					dataType:"json",
					processData: false,
					contentType: false,
					cache: false,
					success:res=>{
						// console.log(res);
						// console.log(sole);
						if(res.status===0){
							$(".loadTip").html("图片上传成功");
							$.ajax({
								type:"post",
								url:"https://sale.imchaotan.com/api/adminAjax/addArticle",
								data:{
									"article_title":article_title,
									"shop_name":shop_name,
									"article_writer":article_writer,
									"location":location,
									"image":res.data.src,
									"telphone":telphone,
									"address":address,
									"tips":tips,
									"cons":cons,
									"sole":sole,
									"contents":contents,
									"tags_id":tags_id
								},
								dataType:"json",
								success:res=>{
									// console.log(res);

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
		
		function dataURLtoBlob (urlData){
	        var bytes=window.atob(urlData.split(',')[1]);//去掉url的头，并转换为byte  
	        //处理异常,将ascii码小于0的转换为大于0
	        var ab = new ArrayBuffer(bytes.length);
	        var ia = new Uint8Array(ab);
	        for (var i = 0; i < bytes.length; i++) {
	            ia[i] = bytes.charCodeAt(i);
	        }
	        return new Blob( [ab] , {type : 'image/png'});// 此处type注意与photoClip初始化中的outputType类型保持一致
		}
		
		//建立一個可存取到該file的url
		function getObjectURL (file) {
			var url = null ; 
			if (window.createObjectURL!=undefined) { // basic
				url = window.createObjectURL(file) ;
			} else if (window.URL!=undefined) { // mozilla(firefox)
				url = window.URL.createObjectURL(file) ;
			} else if (window.webkitURL!=undefined) { // webkit or chrome
				url = window.webkitURL.createObjectURL(file) ;
			}
			return url ;
		}
	}()