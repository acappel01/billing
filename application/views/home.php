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
			<button id='loadEOB'>LoadEOB</button>
			<button id='getTick'>getTick</button>
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
	#returnedList {
		list-style : none;
	}
	#returnedList li {
		clear : both;
		height : 45px;
	}
	#returnedList li *{
		float : left;
		border-style : none;
	}
	#returnedList :nth-child(1) *{
		background : lightblue;
	}
	#returnedList li :nth-child(1) { width :  70px; }
	#returnedList li :nth-child(2) { width :  80px; }
	#returnedList li :nth-child(3) { width :  70px; }
	#returnedList li :nth-child(4) { width :  70px; }
	#returnedList li :nth-child(5) { width : 100px; }
	#returnedList li :nth-child(6) { width : 100px; }
	#returnedList li :nth-child(7) { width :  90px; }
	#returnedList li :nth-child(8) { width :  90px; }
	#returnedList li :nth-child(9) { width :  50px; }
	#returnedList li :nth-child(10){ width : 100px; }
	#returnedList li :nth-child(11){ width :  50px; }
	#returnedList li :nth-child(12){ width :  80px; }
	#returnedList li :nth-child(13){ width :  90px; }
	#returnedList li :nth-child(14){ width :  80px; }
	#returnedList li :nth-child(15){ width :  80px; }
	#returnedList li :nth-child(16){ width :  70px; }
</style>
<script>
	$(document).ready(function(){
		//setup();
		$('#loadEOB').click(function(){
			$('#mainDisplay').html('loading EOB');
			var target = 'index.php?/loadEOB/go'
			var request=$.post(target,'',function(data){
				$('#mainDisplay').html(data);
			});
		});

		$('#getTick').click(function(){
			$('#debug').html('getting tick');
			var target = 'index.php?/action/getapi'
			var request=$.post(target,'',function(data){ $('#mainDisplay').html(data); });
		});
		$('#getTick').hide();

		$('#getList').click(function(){
			$('#debug').html('Getting List');
			var target = 'index.php?/action/getList'
			var request=$.post(target,'',function(data){
				var a = $.parseJSON(data);
				var rtn = '<ul id="returnedList">';
				$.each(a,function(){
					rtn += '<li>'
					$.each(this,function(){
						rtn += '<div>'+this+'</div>';
					});
					rtn += '</li>'
				});
				rtn = rtn + '</ul>';
				$('#mainDisplay').html(rtn);
			});
		});
		$('#getList').hide();

	}); // END document.ready funcion
/*
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
*/
</script>
</html>
