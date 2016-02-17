var id_tag;

function showTag(id){
	if(id_tag != undefined)
	{
		document.getElementById("tag_"+id_tag).style.display = "none";
	}
	id_tag = id;
	sendRequest(id);
}

function sendRequest(id){
	var myUrl = "generate_tag.php?doc_id="+id;
	
	$.ajax({
		url: myUrl,
		success: function(response){
			document.getElementById("tag_"+id).innerHTML = response;
			document.getElementById("tag_"+id_tag).style.display = "block";
		}
	});

}

