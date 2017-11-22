$(".del-btn").click(function(){
		var sid=$(this).parents('tr').find("td:first").html()
		console.log(sid)
		if (confirm("确定删除该文章？")) {  
			$.ajax({
				type:"post",
				url:"",
				data:{
					sid:sid
				},
				dataType:"json",
				success:function(res){
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