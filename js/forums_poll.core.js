function loadpoll(id)
	{
		$("#loading_poll").html("«агрузка опроса... <img src=\"pic/loading.gif\" border=\"0\" />");
		$("#loading_poll").fadeIn("fast");
		$("#poll_container").fadeIn("slow", function () {
		 
		  $.post("forums_poll.core.php", {action:"load",id:id}, function (r){ $("#poll_container").html(r); 
			if($("#results").hasClass("results"))
			{
				$("div[id='poll_result']").each(function(){
					var percentage = $(this).attr("name");
					
					$(this).css({width: "0%"}).animate({
					width: percentage+"%"}, 1600);
					
					});
			 $("#loading").fadeOut("fast"); 		
			}
		 
		},"html" );});
	}
	function vote(id)
	{
		var pollId = $("#pollId").val();
		var choice = $("#choice").val();
		var id = $("#id").val();
		$("#poll_container").empty();
		$("#poll_container").append("<div id=\"loading_poll\" style=\"display:none\"><\/div>");
		$("#loading_poll").fadeIn("fast", function () {$("#loading_poll").html(loadpoll(id));});
		/// снова попытаемс€ запустить показ опрос, после вывода ошибки выше.
			$.post("forums_poll.core.php",{action:"vote",pollId:pollId,choice:choice,id:id}, function(r)
			{
				if(r.status == 0 )
				$("#loading_poll").fadeIn("fast", function () {$("#loading_poll").empty(); $("#loading_poll").html(r.msg);});
				else if(r.status == 1 )
				{
				$("#loading_poll").empty();
				loadpoll(id);
				}
			},"json");
		
	
	}
	function addvote(val)
	{
		$("#choice").val(val);
		$("#vote_b").show("fast");
	}