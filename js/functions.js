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
	$rowno=$("#employee_table tr").length;
	$rowno=$rowno+1;
	$("#employee_table tr:last").after("" +
		"<tr id='row"+$rowno+"'>" +
			"<td><input type='text' name='name[]' placeholder='Enter Name'></td>" +
			"<td><input type='text' name='age[]' placeholder='Enter Age'></td>" +
			"<td><input type='text' name='job[]' placeholder='Enter Job'></td>" +
			"<td><img src='./img/buttons/icons8-plus-48.png' alt='add rij' title='add rij' onclick=add_row();></td>" +
			"<td><img src='./img/buttons/icons8-trash-can-48.png' alt='verwijder rij' title='verwijder rij' onclick=delete_row('row"+$rowno+"')></td>" +
		"</tr>");
}
function delete_row(rowno) {
	// moet hier de rij uit de array verwijderd worden????
	$('#'+rowno).remove();
}