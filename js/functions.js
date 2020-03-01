function confirmDelUser() {
	return confirm("Weet je zeker dat de user verwijderd moet worden?");
}
function confirmDelSoortuur() {
	return confirm("Weet je zeker dat deze uurcode verwijderd moet worden?");
}

function confirmDelNieuwsbericht() {
	return confirm("Weet je zeker dat dit bericht verwijderd moet worden?");
}

function add_row(js_aantal_dagen_readonly) {
	var fieldElementsSoortUur=document.getElementById('dropdownSoortUren');
	var selectSoortUur = fieldElementsSoortUur.getAttribute("data-options");
	 
	if (js_aantal_dagen_readonly === undefined) 
	{
		js_aantal_dagen_readonly = -1;
	}
	
	readonly_dag0='';
	readonly_dag1='';
	readonly_dag2='';
	readonly_dag3='';
	readonly_dag4='';
	readonly_dag5='';
	readonly_dag6='';
	 
	if (js_aantal_dagen_readonly >= 0) readonly_dag0='readonly'; 
	if (js_aantal_dagen_readonly >= 1) readonly_dag1='readonly'; 
	if (js_aantal_dagen_readonly >= 2) readonly_dag2='readonly'; 
	if (js_aantal_dagen_readonly >= 3) readonly_dag3='readonly'; 
	if (js_aantal_dagen_readonly >= 4) readonly_dag4='readonly'; 
	if (js_aantal_dagen_readonly >= 5) readonly_dag5='readonly';
	if (js_aantal_dagen_readonly >= 6) readonly_dag6='readonly';
	var selectSoortUurEnabled = selectSoortUur.replace(/disabled/g, "enabled");
	
	$rowno=$("#uren_table tr").length;
	$rowno=$rowno+1;
	$("#uren_table tr:last").after("" +
		"<tr id='row"+$rowno+"'>" +
			"<td><select name='soortuur[]' enabled>"+selectSoortUurEnabled+"</select></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag1[]' min='0' max='24' step='0.25' size='2' "+readonly_dag0+"></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag2[]' min='0' max='24' step='0.25' size='2' "+readonly_dag1+"></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag3[]' min='0' max='24' step='0.25' size='2' "+readonly_dag2+"></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag4[]' min='0' max='24' step='0.25' size='2' "+readonly_dag3+"></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag5[]' min='0' max='24' step='0.25' size='2' "+readonly_dag4+"></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag6[]' min='0' max='24' step='0.25' size='2' "+readonly_dag5+"></td>" +
			"<td><input style='width:3.33vw;text-align:right' type='number' name='dag7[]' min='0' max='24' step='0.25' size='2' "+readonly_dag6+"></td>" +
			"<td colspan=2><img class='button' src='./img/icons/add-48.png' alt='add rij' title='add rij' onclick=add_row("+js_aantal_dagen_readonly+");> <img class='button' src='./img/icons/trash-48.png' alt='verwijder rij' title='verwijder rij' onclick=delete_row('row"+$rowno+"')></td>" +
			"<td></td>" +
		"</tr>");
}
function delete_row(rowno) {
	// moet hier de rij uit de array verwijderd worden????
	$('#'+rowno).remove();
}
