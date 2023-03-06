<?php

	error_reporting( E_ALL & ~E_NOTICE );

	$data_con = mysqli_connect("localhost", "root", "sample ","sample", "sca");
	if( mysqli_connect_error( ) ){
		echo mysqli_connect_error( );exit;
	}

	########### state code #############
	if( $_POST['action'] == "load_states"){
		
		$states = [];
		$query = "select * from states order by state";
		$res = mysqli_query($data_con, $query );
		if( mysqli_error($data_con) ){
			echo json_encode([
				"status"=>"fail",
				"error"=>"There was an error at server",
				"query"=>$query,
				"sqlerror"=>mysqli_error($data_con)
			]);
			exit;
		}
		while( $row = mysqli_fetch_assoc( $res)  ){
			$states[] = $row;
		}
		echo json_encode([
			"status"=>"success",
			"states"=>$states
		]);
		exit;
	}

	if( $_POST['action'] == "add_state" ){

		//state = "andhra ";
		//state.match( /^[A-Za-z\ \&]{3,50}$/i )

		if( !preg_match("/^[A-Za-z\ \&]{3,50}$/i", $_POST['state'] ) ){
			echo json_encode([
				"status"=>"fail",
				"error"=>"Validation Error: State incorrect format"
			]);exit;
		}

		$query = "insert into states set 
		state = '" . mysqli_escape_string($data_con, $_POST['state'] ) . "'";
		mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo json_encode([
				"status"=>"fail",
				"error"=>"Db Error: " . mysqli_error($data_con),
				"query"=>$query
			]);exit;
		}
		echo json_encode([
			"status"=>"success",
			"state_id"=>mysqli_insert_id($data_con)
		]);
		exit;
	}

	if( $_POST['action'] == "delete_state" ){
		$query = "delete from states where id = " . $_POST['state_id'];
		$res = mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";exit;
		}
		echo "success";
		exit;
	}

	if( $_POST['action'] == "edit_state" ){
		$query = "update states set 
		state = '" . mysqli_escape_string($data_con, $_POST['state'] ) . "'
		where id = " . $_POST['state_id'];
		mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

		################# citycode ################
	if( $_POST['action'] == "load_cities"){
		
		//SQL QUERIES


		$cities = [];
		$query = "select a.*, b.state from  cities  as a 
		left join  states as b 
		on ( a.state_id = b.id ) where state_id = " . $_POST['state_id'] . " 
		order by a.city";
		$res = mysqli_query($data_con, $query );
		while( $row = mysqli_fetch_assoc( $res)  ){
			$cities[] = $row;
		}
		echo json_encode($cities);
		exit;
	}
	if( $_POST['action'] == "add_city" ){
		$query = "insert into cities set 
		state_id = '" . mysqli_escape_string($data_con, $_POST['state_id'] ) . "',
		city = '" . mysqli_escape_string($data_con, $_POST['city'] ) . "'";
		mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	if( $_POST['action'] == "delete_city" ){
		$query = "delete from cities where id = " . $_POST['city_id'];
		$res = mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";exit;
		}
		echo "success";
		exit;
	}

	if( $_POST['action'] == "edit_city" ){
		$query = "update cities set 
		city = '" . mysqli_escape_string($data_con, $_POST['city'] ) . "'
		where id = " . $_POST['city_id'];
		mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}


		################# areacode ################
	if( $_POST['action'] == "load_areas"){
		
		$areas = [];
		$query = "select a.*, s.state, c.city from areas as a 
		left join states as s 
		on ( a.state_id = s.id ) 
		left join cities as c 
		on ( a.city_id = c.id ) where a.city_id = ".$_POST['city_id']." order by a.area";
		$res = mysqli_query($data_con, $query );
		if( mysqli_error($data_con ) ){
			echo $query . "\n";
			echo mysqli_error($data_con);
			exit;
		}
		while( $row = mysqli_fetch_assoc( $res)  ){
			$areas[] = $row;
		}
		echo json_encode($areas);
		exit;
	}
	if( $_POST['action'] == "add_area" ){
		$query = "insert into areas set 
		state_id = '" . mysqli_escape_string($data_con, $_POST['state_id'] ) . "',
		city_id = '" . mysqli_escape_string($data_con, $_POST['city_id'] ) . "',
		area = '" . mysqli_escape_string($data_con, $_POST['area'] ) . "'";
		mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	if( $_POST['action'] == "delete_area" ){
		$query = "delete from areas where id = " . $_POST['area_id'];
		$res = mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";exit;
		}
		echo "success";
		exit;
	}

	if( $_POST['action'] == "edit_area_areas" ){
		$query = "update areas set 
		area = '" . mysqli_escape_string($data_con, $_POST['area'] ) . "'
		where id = " . $_POST['area_id'];
		mysqli_query($data_con, $query);
		if( mysqli_error($data_con) ){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}


?>
<html>
<head>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>


<!-- *************states code****************** -->

<div id="add_state_div" style="position: absolute;display: none;">
	<div class="modal" tabindex="-1" style="display: block;">
	   <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Add State</h5>
	        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" onclick="hide_add_state_form()"></button>
	      </div>
	      <div class="modal-body">

	       	<table width="100%">
			<tbody>
			<tr>
				<td>State</td>
				<td><input  class="form-control" type="text" id="new_state"></td>
			</tr>
		</tbody></table>


	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hide_add_state_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="ADD" onclick="add_state()">
	      </div>
	    </div>
	  </div>
	</div>
</div>


<div id="edit_state_div" style="position: absolute;display: none;">
	<div class="modal" tabindex="-1" style="display: block;">
	   <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit State</h5>
	        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" onclick="hide_edit_state_form()"></button>
	      </div>
	      <div class="modal-body">

	       	<table width="100%">
			<tbody>
			<tr>
				<td>State</td>
				<td><input  class="form-control" type="text" id="edit_state"></td>
			</tr>
		</tbody></table>


	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hide_edit_state_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="EDIT" onclick="edit_state()">
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- *************City code****************** -->
<div id="add_city_div" style="position: absolute;display: none;">
	<div class="modal" tabindex="-1" style="display: block;">
	   <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Add City</h5>
	        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" onclick="hide_add_city_form()"></button>
	      </div>
	      <div class="modal-body">

	       	<table width="100%">
			<tbody>
			<tr>
				<td>City</td>
				<td><input  class="form-control" type="text" id="new_city_city"></td>
			</tr>
		</tbody></table>


	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hide_add_city_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="ADD" onclick="add_city()">
	      </div>
	    </div>
	  </div>
	</div>
</div>


<div id="edit_city_div" style="position: absolute;display: none;">
	<div class="modal" tabindex="-1" style="display: block;">
	   <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit City</h5>
	        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" onclick="hide_edit_city_form()"></button>
	      </div>
	      <div class="modal-body">

	       	<table width="100%">
			<tbody>
			<tr>
				<td>City</td>
				<td><input  class="form-control" type="text" id="edit_city_city"></td>
			</tr>
		</tbody></table>


	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hide_edit_city_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="EDIT" onclick="edit_city()">
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- *************Areas code****************** -->

<div id="add_area_div" style="position: absolute;display: none;">
	<div class="modal" tabindex="-1" style="display: block;">
	   <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Add Areas</h5>
	        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" onclick="hide_add_area_form()"></button>
	      </div>
	      <div class="modal-body">

	       	<table width="100%">
			<tbody>
			<tr>
				<td>Areas</td>
				<td><input  class="form-control" type="text" id="new_areas"></td>
			</tr>
		</tbody></table>


	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hide_add_area_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="ADD" onclick="add_area()">
	      </div>
	    </div>
	  </div>
	</div>
</div>


<div id="edit_area_div" style="position: absolute;display: none;">
	<div class="modal" tabindex="-1" style="display: block;">
	   <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Edit Areas</h5>
	        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" onclick="hide_edit_area_form()"></button>
	      </div>
	      <div class="modal-body">

	       	<table width="100%">
			<tbody>
			<tr>
				<td>Areas</td>
				<td><input  class="form-control" type="text" id="edit_areas"></td>
			</tr>
		</tbody></table>

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hide_edit_area_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="EDIT" onclick="edit_area_areas()">
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- ***************table format ************** -->

<div class="d-flex">
	<table class="table table-bordered table-sm" style="width: auto;">
		<tr>
			<td>States</td>
		</tr>
		<tr>
			<td>
				<div><input type="button" class="btn btn-info btn-sm" value="+" onclick="show_add_state_form()" ></div>
				<table class="table table-bordered table-striped table-sm table-hover" >
					<tr>
						<td>State</td>
						<td>Edit/Delete</td>
					</tr>
					<tbody id="states_list_div" >
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<table id="cities_block" style="display:none; width: auto;" class="table table-bordered table-sm">
		<tr>
			<td>Cities</td>
		</tr>
		<tr>
			<td>
				<div><input type="button" class="btn btn-info btn-sm" value="+" onclick="show_add_city_form()" ></div>
				<table class="table table-bordered table-striped table-sm table-hover" >
					<tr>
						<td>Id</td>
						<td>State</td>
						<td>City</td>
						<td>EDit/Delete</td>
					</tr>
					<tbody id="cities_list_div" >
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<table id="area_block" style="display:none;width: auto;" class="table table-bordered table-sm">
		<tr>
			<td>Area</td>
		</tr>
		<tr>
			<td>
				<div><input type="button" class="btn btn-info btn-sm" value="+" onclick="show_add_area_form()"></div>
				<table class="table table-bordered table-striped table-sm table-hover" >
					<tr>
						<td>Id</td>
						<td>State</td>
						<td>City</td>
						<td>Area</td>
						<td>Edit/Delete</td>
					</tr>
					<tbody id="areas_list_div" >
					</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>
<table>
	<tr>
		<td>
			<pre id="states_list_tree"></pre>
		</td>
		<td>
			<pre id="cities_list_tree"></pre>
		</td>
		<td>
			<pre id="areas_list_tree"></pre>
		</td>
	</tr>
</table>
<script>

	/*##################state################*/
	function show_add_state_form(){
		document.getElementById("add_state_div").style.display = 'block';
	}
	function hide_add_state_form(){
		document.getElementById("add_state_div").style.display = 'none';
	}
	function add_state(){
		state = document.getElementById("new_state").value;
		vpostdata = "action=add_state&state="+encodeURIComponent(state);

		data_con = new XMLHttpRequest();
		data_con.open("POST","sca.php",true);
		data_con.onload = function(){
			if( this.responseText ){
				try{
					response = JSON.parse( this.responseText );
					if( "status" in response ){
						if( response["status"] == "success" ){
							load_states();
						}else{
							alert("There was an error: \n" + response['error'] );
						}
					}else{
						alert("Incorrect response recieved from server!");
					}
				}catch(e){
					alert("Error parsing json response");
				}
			}else{
				alert("Empty/Incorrect response")
			}
		}
		data_con.setRequestHeader("content-type","application/x-www-form-urlencoded");
		data_con.send(vpostdata);
	}

	var states_list = [];
	function load_states(){
		data_con = new XMLHttpRequest();
		data_con.open( "POST", "sca.php", true );
		data_con.onload = function(){
			if( this.responseText ){
				try{
					response = JSON.parse( this.responseText );
					if( "status" in response ){
						if( response["status"] == "success" ){
							states_list = response['states'];
							generate_state_list();
						}else{
							alert("There was an error: \n" + response['error'] );
						}
					}else{
						alert("Incorrect response recieved from server!");
					}
				}catch(e){
					alert("Error parsing json response");
				}
			}else{
				alert("Empty/Incorrect response")
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( "action=load_states" );
	}
	selected_state = 1;
	selected_state_id = 0;
	function select_state(vi){
		cities_list = [];
		generate_city_list();
		areas_list = [];
		generate_area_list();
		console.log( vi );
		selected_state = vi;
		selected_state_id = states_list[ vi ]['id'];
		document.getElementById("cities_block").style.display = 'block';
		load_cities();
	}
	function generate_state_list(){
		var str = "";
		for(var i=0;i<states_list.length;i++){
			str = str + `<tr>
				<td><a href='#' onclick="select_state(`+i+`);return false" >`+ states_list[i]['state'] + `</td>
				<td>
				<input class="bg-dark text-white" type="button" value="E" onclick="show_edit_state_form(`+i+`)" >
				<input class="bg-danger text-white" type="button" value="X" onclick="delete_state(`+i+`)" >
				</td>
			</tr>`;
		}
		document.getElementById("states_list_div").innerHTML = str;
		document.getElementById("states_list_tree").innerHTML = JSON.stringify(states_list,null,4);
	}
	var editing_state_id = 0;
	function hide_edit_state_form(){
		document.getElementById("edit_state_div").style.display = 'none';
	}
	function show_edit_state_form(sid){
		editing_state_id = sid;
		document.getElementById("edit_state_div").style.display = 'block';
		document.getElementById("edit_state").value = states_list[ editing_state_id ]['state'];
	}function edit_state(){
		states_list[editing_state_id]['state'] = document.getElementById("edit_state").value;
		vpostdata = "action=edit_state";
		vpostdata += "&state_id=" +states_list[editing_state_id]['id'];
		vpostdata += "&state=" +states_list[editing_state_id]['state'];

		data_con = new XMLHttpRequest();
		data_con.open( "POST", "sca.php", true );
		data_con.onload = function(){
			if( this.responseText == "success" ){
				hide_edit_state_form();
				generate_state_list();
			}else{
				alert("There was an error");		
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( vpostdata );
	}
	deleting_state_id = 0;
	function delete_state(vi){
		deleting_state_id = vi;
		data_con = new XMLHttpRequest();
		data_con.open( "POST", "sca.php", true );
		data_con.onload = function(){
			if( this.responseText == "success" ){
				states_list.splice( deleting_state_id,1 );
				generate_state_list();
			}else{
				alert("There was an error");		
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( "action=delete_state&state_id=" + states_list[vi]['id'] );	
	}

	/*#################city###################*/

	function show_add_city_form(){
		document.getElementById("add_city_div").style.display = 'block';
	}
	function hide_add_city_form(){
		document.getElementById("add_city_div").style.display = 'none';
	}
	function add_city(){
		city = document.getElementById("new_city_city").value;
		vpostdata = "action=add_city&state_id="+selected_state_id+ "&city="+ encodeURIComponent(city);

		data_con = new XMLHttpRequest();
		data_con.open("POST","ajax3.php",true);
		data_con.onload = function(){
			if(this.responseText = "success"){
				load_cities();
				hide_add_city_form();
			}else{
				alert("There was an error at server");
			}
		}
		data_con.setRequestHeader("content-type","application/x-www-form-urlencoded");
		data_con.send(vpostdata);
	}

	var cities_list = [];
	function load_cities(){
		data_con = new XMLHttpRequest();
		data_con.open( "POST", "ajax3.php", true );
		data_con.onload = function(){
			cities_list = JSON.parse( this.responseText );
			console.log( cities_list );
			document.getElementById("cities_list_tree").innerHTML = JSON.stringify(cities_list,null,4);
			generate_city_list();
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( "action=load_cities&state_id=" + selected_state_id );
	}
	selected_city = 1;
	selected_city_id = 0;
	function select_city(ci){
		selected_city = ci;
		selected_city_id = cities_list[ci]['id'];
		document.getElementById("area_block").style.display = 'block';
		load_areas();
	}
	function generate_city_list(){
		var str = "";
		for(var i=0;i<cities_list.length;i++){
			str = str + `<tr>
				<td>` + cities_list[i]['id'] + `</td>
				<td>` + cities_list[i]['state'] + `</td>
				<td><a href='#' onclick="select_city(`+i+`)">` + cities_list[i]['city'] + `</td>
				<td>
				<input class="bg-dark text-white" type="button" value="E" onclick="show_edit_city_form(`+i+`)" >
				<input class="bg-danger text-white" type="button" value="X" onclick="delete_city(`+i+`)" >
				</td>
			</tr>`;
		}
		document.getElementById("cities_list_div").innerHTML = str;
	}
	var editing_city_id = 0;
	function hide_edit_city_form(){
		document.getElementById("edit_city_div").style.display = 'none';
	}
	function show_edit_city_form(cid){
		editing_city_id = cid;
		document.getElementById("edit_city_div").style.display = 'block';
		document.getElementById("edit_city_city").value = cities_list[ editing_city_id ]['city'];
	}
	function edit_city(){
		cities_list[editing_city_id]['city'] = document.getElementById("edit_city_city").value;
		vpostdata = "action=edit_city";
		vpostdata += "&city_id=" +cities_list[editing_city_id]['id'];
		vpostdata += "&city=" +cities_list[editing_city_id]['city'];

		data_con = new XMLHttpRequest();
		data_con.open( "POST", "ajax3.php", true );
		data_con.onload = function(){
			if( this.responseText == "success" ){
				hide_edit_city_form();
				generate_city_list();
			}else{
				alert("There was an error");		
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( vpostdata );
	}
	deleting_city_id = 0;
	function delete_city(ci){
		deleting_city_id = ci;
		data_con = new XMLHttpRequest();
		data_con.open( "POST", "ajax3.php", true );
		data_con.onload = function(){
			if( this.responseText == "success" ){
				cities_list.splice( deleting_city_id,1 );
				generate_city_list();
			}else{
				alert("There was an error");		
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( "action=delete_city&city_id=" + cities_list[ci]['id'] );	
	}

	/*############### area ############*/
	function show_add_area_form(){
		document.getElementById("add_area_div").style.display = 'block';
	}
	function hide_add_area_form(){
		document.getElementById("add_area_div").style.display = 'none';
	}
	function add_area(){
		area = document.getElementById("new_areas").value;
		vpostdata = "action=add_area&state_id="+selected_state_id
		            + "&city_id="+ selected_city_id
		            + "&area="+ encodeURIComponent(area);

		data_con = new XMLHttpRequest();
		data_con.open("POST","ajax3.php",true);
		data_con.onload = function(){
			if(this.responseText = "success"){
				load_areas();
				hide_add_area_form();
			}else{
				alert("There was an error at server");
			}
		}
		data_con.setRequestHeader("content-type","application/x-www-form-urlencoded");
		data_con.send(vpostdata);
	}

	var areas_list = [];
	function load_areas(){
		data_con = new XMLHttpRequest();
		data_con.open( "POST", "sca.php", true );
		data_con.onload = function(){
			try{
				areas_list = JSON.parse( this.responseText );
				console.log( areas_list );
				document.getElementById("areas_list_tree").innerHTML = JSON.stringify(areas_list,null,4);
				generate_area_list();
			}catch(e){
				alert("There was an error loading areas:\n" + e );
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( "action=load_areas&city_id="+selected_city_id );
	}
	function generate_area_list(){
		var str = "";
		for(var i=0;i<areas_list.length;i++){
			str = str + `<tr>
				<td>` + areas_list[i]['id'] + `</td>
				<td>` + areas_list[i]['state'] + `</td>
				<td>` + areas_list[i]['city'] + `</td>
				<td>` + areas_list[i]['area'] + `</td>
				<td>
				<input class="bg-dark text-white" type="button" value="E" onclick="show_edit_area_form(`+i+`)" >
				<input class="bg-danger text-white" type="button" value="X" onclick="delete_area(`+i+`)" >
				</td>
			</tr>`;
		}
		document.getElementById("areas_list_div").innerHTML = str;
	}
	var editing_area_id = 0;
	function hide_edit_area_form(){
		document.getElementById("edit_area_div").style.display = 'none';
	}
	function show_edit_area_form(aid){
		editing_area_id = aid;
		document.getElementById("edit_area_div").style.display = 'block';
		document.getElementById("edit_areas").value = areas_list[ editing_area_id ]['area'];
	}

	function edit_area_areas(){
		areas_list[editing_area_id]['area'] = document.getElementById("edit_areas").value;
		vpostdata = "action=edit_area_areas";
		vpostdata += "&area_id=" +areas_list[editing_area_id]['id'];
		vpostdata += "&area=" +areas_list[editing_area_id]['area'];

		data_con = new XMLHttpRequest();
		data_con.open( "POST", "sca.php", true );
		data_con.onload = function(){
			if( this.responseText == "success" ){
				hide_edit_area_form();
				generate_area_list();
			}else{
				alert("There was an error");		
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( vpostdata );
	}
	deleting_area_id = 0;
	function delete_area(ai){
		deleting_area_id = ai;
		data_con = new XMLHttpRequest();
		data_con.open( "POST", "sca.php", true );
		data_con.onload = function(){
			if( this.responseText == "success" ){
				areas_list.splice( deleting_area_id,1 );
				generate_area_list();
			}else{
				alert("There was an error");		
			}
		}
		data_con.setRequestHeader("content-type", "application/x-www-form-urlencoded");
		data_con.send( "action=delete_area&area_id=" + areas_list[ai]['id'] );	
	}
load_states();
</script>
</body>
</html>
