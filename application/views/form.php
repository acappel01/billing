<h3>Form</h3>
<div id = 'msg'>Message</div>
<div>
	<form id = 'formId' method = 'post'>
		<ul>
			<li>
				<div>Name</div><input type = 'text' name = 'ename' />
			</li>
			<li>
				<div>Type</div><input type = 'text' name = 'etype' />
			</li>
			<li>
				<button id = 'send'>Send</button>
			</li>
		</ul>
	</form>
</div>
<style>
	#formId ul { list-style : none; }
	#formId ul :nth-child(3) button {
		background : yellow;
		width : 60px;
	}
	#formId li { height : 40px; clear : both; }
	#formId li * {
		float : left;
		margin : 0px;
	}
	#formId li :nth-child(1) { width : 100px; }
	#formId li :nth-child(2) { width : 100px; margin-left : 10px; margin-top : 3px; height : 30px;}
</style>
<script>
	// this is to short circut the form subimt function
	$('#formId').submit(function(event){ return false; });
	// Form validation and submittion example
	$('#send').click(function(event){
		var thisForm = document.getElementById('formId');
		var formData = $(thisForm).serialize();
		var target = 'index.php?/action/addRecord';
		var ele1 = thisForm.ename.value;
		if(ele1 == ''){
			$('#debug').html('bad input');
		}else{
			var request=$.post(target,formData,function(data){
				$('#debug').html(data);
			});
			thisForm.ename.value = '';
		}
	});
</script>
