$(".del-btn").click(function(){
		var sid=$(this).parents('tr').find("td:first").html()
		console.log(sid)
		if (confirm("确定删除该文章？")) {  
			$.ajax({
				type:"post",
				url:"../adminajax/deleteVipShop",
				data:{
					sid:sid
				},
				dataType:"json",
				success:function(res){
					// console.log(res);
					if(res.status===0){
						alert("删除成功");
						window.location.reload()
					}else{
						alert("删除失败");
					}
				},
				error:function(res){
					alert("网络出错！");
				}
			}); 
       	} 
		return false;
	})
	
//$(".edit-btn").click(function(){
//	var sid=$(this).parents('tr').find("td:first").html()
//	console.log(sid)
//   $(".tpl-left-nav-item a").removeClass("active");
//   $(".tpl-left-nav-item .editA").addClass("active");
//   window.location.href="http://sale.imchaotan.com/api/admin/nav?shopId="+sid;
//   return false;
//})