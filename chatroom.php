<!DOCTYPE html>
<html>
<head>
	<title>>Designación</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<style type="text/css">
		#messages {
			height: 200px;
			background: whitesmoke;
			overflow: auto;
		}
		#chat-room-frm {
			margin-top: 10px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h2 class="text-center" style="margin-top: 5px; padding-top: 0;">Designación</h2>
		<hr>
		<div class="row">
			<div class="col-md-4">
				<?php 
					session_start();
					if(!isset($_SESSION['user'])) {
						header("location: index.php");
					}
					require("db/users.php");
					require("db/chatrooms.php");

					$objChatroom = new chatrooms;
					$chatrooms   = $objChatroom->getAllChatRooms();

					$objUser = new users;
					$users   = $objUser->getAllUsers();
				 ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<td>
								<?php 
									foreach ($_SESSION['user'] as $key => $user) {
										$userId = $key;
										echo '<input type="hidden" name="userId" id="userId" value="'.$key.'">';
										echo "<div>".$user['name']."</div>";
										echo "<div>".$user['email']."</div>";
									}
								 ?>
							</td>
							<td align="right" colspan="2">
								<input type="button" class="btn btn-warning" id="leave-chat" name="leave-chat" value="Salir">
							</td>
						</tr>
						<tr>
							<th colspan="3">Usuarios</th>
						</tr>
					</thead>
					<tbody>
						 <?php 
							foreach ($users as $key => $user) {
								$color = 'color: red';
								if($user['login_status'] == 1) {
									$color = 'color: green';
								}
								if(!isset($_SESSION['user'][$user['id']])) {
								echo "<tr><td>".$user['name']."</td>";
								echo "<td><span class='glyphicon glyphicon-globe' style='".$color."'></span></td>";
								echo "<td>".$user['last_login']."</td></tr>";
								}
							}
						 ?>
					</tbody>
				</table>
			</div>
			<div class="col-md-8">
				<div id="messages">
					<table id="chats" class="table table-striped">
					  <thead>
					    <tr>
					      <th colspan="4" scope="col"><strong>Chat</strong></th>
					    </tr>
					  </thead>
					  <tbody>
					  	<?php 
					  		foreach ($chatrooms as $key => $chatroom) {

					  			if($userId == $chatroom['userid']) {
					  				$from = "Yo";
					  			} else {
					  				$from = $chatroom['name'];
					  			}
					  			echo '<tr><td valign="top"><div><strong>'.$from.'</strong></div><div>'.$chatroom['msg'].'</div><td align="right" valign="top">'.date("d/m/Y h:i:s A", strtotime($chatroom['created_on'])).'</td></tr>';
					  		}
					  	 ?>
					  </tbody>
					</table>
				</div>
					
				<form id="chat-room-frm" method="post" action="">
					<div class="form-group">
                    	<textarea class="form-control" id="msg" name="msg" placeholder="Escribé tu mensaje"></textarea>
	                </div>
	                <div class="form-group">
	                    <input type="button" value="Enviar" class="btn btn-success btn-block" id="send" name="send">
	                </div>
			    </form>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		var conn = new WebSocket('ws://localhost:8080');
		conn.onopen = function(e) {
		    console.log("Conexión establecida!");
		};

		conn.onmessage = function(e) {
		    console.log(e.data);
		    var data = JSON.parse(e.data);
		    var row = '<tr><td valign="top"><div><strong>' + data.from +'</strong></div><div>'+data.msg+'</div><td align="right" valign="top">'+data.dt+'</td></tr>';
		    $('#chats > tbody').prepend(row);

		};

		conn.onclose = function(e) {
			console.log("Connection Closed!");
		}

		$("#send").click(function(){
			var userId 	= $("#userId").val();
			var msg 	= $("#msg").val();
			var data = {
				userId: userId,
				msg: msg
			};
			conn.send(JSON.stringify(data));
			$("#msg").val("");
		});

		$("#leave-chat").click(function(){
			var userId 	= $("#userId").val();
			$.ajax({
				url:"action.php",
				method:"post",
				data: "userId="+userId+"&action=leave"
			}).done(function(result){
				var data = JSON.parse(result);
				if(data.status == 1) {
					conn.close();
					location = "index.php";
				} else {
					console.log(data.msg);
				}
				
			});
			
		})

	})
</script>
</html>