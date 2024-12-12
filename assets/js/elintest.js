$('document').ready(function() {
	$(function () { $('#jstree-div').jstree(
		{core: {
			data:  [
				{ "id" : "ajson1", "parent" : "#", "text" : "Simple root node" },
				{ "id" : "ajson2", "parent" : "#", "text" : "Root node 2" },
				{ "id" : "ajson3", "parent" : "ajson2", "text" : "Child 1" },
				{ "id" : "ajson4", "parent" : "ajson2", "text" : "Child 2" },
			 ]
		}}
	); });
	$('#btn-jstree').on('click', function() {
		console.log('ezaza00');
		createJstreeData();
	});
})

function createJstreeData()
{
		$('#btn-jstree').prop('disabled', true);
		$.ajax({
			url: load_jstree_url,
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				$('#modalLoader').modal({keyboard: false, backdrop: true });
				$('#modalLoader').modal('show');
				$('#modalLoader').modal('handleUpdate');
			},
			success: function(result) {
				console.log('success');
				console.log(result);
				loadJstreeData(result);

				$('#btn-jstree').removeClass('disabled');
				$('#modalLoader').modal('hide');
			},
			error: function(result) {
				console.log('error');
				$('#address-list-btn').prop('disabled', false);
				$('#modalLoader').modal('hide');
			},
			complete: function(result) {
				console.log('complete');
				loadJstreeData(result);
				$('#modalLoader').modal('hide');
			}
		});
}

function loadJstreeData(json){
	console.log('loadjstreedata');
	$('#modalLoader').modal('hide');

	$('#jstree-div').jstree(
		{core: {
			data:  [
				{ "id" : "ajson1", "parent" : "#", "text" : "Simple root node" },
				{ "id" : "ajson2", "parent" : "#", "text" : "Root node 2" },
				{ "id" : "ajson3", "parent" : "ajson2", "text" : "Child 1" },
				{ "id" : "ajson4", "parent" : "ajson2", "text" : "Child 2" },
			 ]
		}}
	)

}