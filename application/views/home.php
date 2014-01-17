<html>
<head>
	<script src='jquery.js'></script>
</head>
<body>
	<div id='mainBody'>
		Body
		<div id='mainMenu'>
			Menu
			<button id='getList'>GetList</button>
		</div>
		<div id='mainDisplay'>
			Display	
		</div>
		<div id='debug'>
			Debug
		</div>
	</div>
<!--
	<div>
		Form
		<?php #echo $form ?>
	</div>
--!>
</body>
<style>
	#mainBody    { border-color : blue; }
	#mainMenu    { border-color : yellow; }
	#mainDisplay { border-color : green; }
	#debug       { border-color : red; }
	div {
		border-style : ridge;
		border-size : 1px;
		border-color : gray;
		margin : 5px;
		padding : 5px;
	}
</style>
<script>
	$(document).ready(function(){
		//setup();
		$('#getList').click(function(){
			$('#debug').html('Getting List');
			var target = 'index.php?/action/getList'
			var request=$.post(target,'',function(data){
				$('#mainDisplay').html(data);
			});
		});
	});
	function setup(){
		var target = 'index.php?/controller/function'
		$('#viewTables').click(function(){
			var request=$.post(target,'',function(data){
				$('#id').html(data);
			});
		});
	}
	function postData(target,htmlId){
		var request=$.post(target,'',function(data){
			$(htmlId).html(data);
		}).fail(function(data){
			var error = request.status + " " + request.statusText;
			$(htmlId).html('Post Error ' + error);
		});
	}
</script>
</html>
