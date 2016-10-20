<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
?>

<html>

<head>

<title>test</title>


<style type="text/css">
#leftPannel {
	border: 1px solid blue;
	float: left;
	padding: 10px;
	margin-right: 20px;
}

#rightPannel {
	border: 1px solid blue;
	float: left;
	padding: 10px;
	margin-left: 20px;
}

div.clear {
	clear: both;
}

div.form {
	border: 1px solid gray;
	margin: 15px auto;
	padding: 15px;
}
</style>



<script src="http://cdn.bootcss.com/jquery/3.1.1/jquery.js"></script>

<script>
$(document).ready(function(){


	console.log($("#post_resources_body").val());
	
  $("#get_all_resources").click(function(){
	  console.log("get all resources");  
	  $.get("/flowmanage/api.php?api=/test/&pageSize=5&pageNumber=1",function(data,status){
		  console.log("get method:");
		    console.log("Data: " + JSON.stringify(data) + "\nStatus: " + status);
		  });
  });

  $("#post_resources").click(function(){
	  
	  console.log("post_resources");  
	  
	  $.post("/flowmanage/api.php?api=/test/&pageSize=5&pageNumber=1",$("#post_resources_body").val(),function(data,status){
		  console.log("post method:");
		    
		  }
		  ).done(function(data){
		  console.log("Data: " + JSON.stringify(data) + "\nStatus: " + status);
	  }).fail(function(){console.log("fail");});

  });

  

  
});
</script>





</head>


<body>

	<div id="leftPannel">

		<ul>
			<li>menu1</li>
			<li>menu2</li>
		</ul>


	</div>

	<div id="rightPannel">
		<div class="form">
			get all sources <input id="get_all_resources" type="submit"
				name="get_all_resources" value="get_all_resources"></input>

		</div>

		<div class="form">

			<input id="post_resources_body" type="text" name="post_resources_body" value='{"data":"my test data!!!!!"}'></input>
			post_resources <input id="post_resources" type="submit"
				name="post_resources" value="post_resources"></input>

		</div>


		<div class="form"></div>
		<div class="form"></div>
		<div class="form"></div>
		<div class="form"></div>
		<div class="form"></div>
		<div class="form"></div>
	</div>

	<div class="clear"></div>


</body>
</html>
