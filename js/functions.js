function confirmDelUser() {
	return confirm("Weet je zeker dat de user verwijderd moet worden?");
}
function confirmDelSoortuur() {
	return confirm("Weet je zeker dat deze uurcode verwijderd moet worden?");
}

function confirmDelNieuwsbericht() {
	return confirm("Weet je zeker dat dit bericht verwijderd moet worden?");
}

function add_row() {
	var fieldElementsSoortUur=document.getElementById('dropdownSoortUren');
	var selectSoortUur = fieldElementsSoortUur.getAttribute("data-options");
	$rowno=$("#uren_table tr").length;
	$rowno=$rowno+1;
	$("#uren_table tr:last").after("" +
		"<tr id='row"+$rowno+"'>" +
			"<td><select name='soortuur[]'>"+selectSoortUur+"</select></td>" +
			"<td><input style='width:50px' type='number' name='dag1[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td><input style='width:50px' type='number' name='dag2[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td><input style='width:50px' type='number' name='dag3[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td><input style='width:50px' type='number' name='dag4[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td><input style='width:50px' type='number' name='dag5[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td><input style='width:50px' type='number' name='dag6[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td><input style='width:50px' type='number' name='dag7[]' min='0' max='24' step='0.25' size='2'></td>" +
			"<td colspan=2><img src='./img/buttons/icons8-plus-48.png' alt='add rij' title='add rij' onclick=add_row();> <img src='./img/buttons/icons8-trash-can-48.png' alt='verwijder rij' title='verwijder rij' onclick=delete_row('row"+$rowno+"')></td>" +
			"<td></td>" +
		"</tr>");
}
function delete_row(rowno) {
	// moet hier de rij uit de array verwijderd worden????
	$('#'+rowno).remove();
}
