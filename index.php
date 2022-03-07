<!DOCTYPE html>
<html>

<head>
	<title>Chat application in php using web scocket programming</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="container">
		<h2 class="text-center" style="margin-top: 5px; padding-top: 0;">>Designación</h2>
		<hr>
		<?php
		if (isset($_POST['join'])) {
			session_start();
			require("db/users.php");
			$objUser = new users;
			$objUser->setEmail($_POST['email']);
			$objUser->setName($_POST['uname']);
			$objUser->setLoginStatus(1);
			$objUser->setLastLogin(date('Y-m-d h:i:s'));
			$userData = $objUser->getUserByEmail();
			if (is_array($userData) && count($userData) > 0) {
				$objUser->setId($userData['id']);
				if ($objUser->updateLoginStatus()) {
					echo "Cargando usuario..";
					$_SESSION['user'][$userData['id']] = $userData;
					header("location: chatroom.php");
				} else {
					echo "Error al cargar usuario.";
				}
			} else {
				if ($objUser->save()) {
					$lastId = $objUser->dbConn->lastInsertId();
					$objUser->setId($lastId);
					$_SESSION['user'][$lastId] = [
						'id' => $objUser->getId(),
						'name' => $objUser->getName(),
						'email' => $objUser->getEmail(),
						'login_status' => $objUser->getLoginStatus(),
						'last_login' => $objUser->getLastLogin()
					];

					echo "Usuario registrado..";
					header("location: chatroom.php");
				} else {
					echo "Error..";
				}
			}
		}
		?>
		<div class="row join-room">
			<div class="col-md-6 col-md-offset-3">
				<form id="join-room-frm" role="form" method="post" action="" class="form-horizontal">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon addon-diff-color">
								<span class="glyphicon glyphicon-user"></span>
							</div>
							<input type="text" class="form-control" id="uname" name="uname" placeholder="Nombre">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon addon-diff-color">
								<span class="glyphicon glyphicon-envelope"></span>
							</div>
							<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="">
						</div>
					</div>
					<div class="form-group">
						<input type="submit" value="Entrar" class="btn btn-success btn-block" id="join" name="join">
					</div>
				</form>
			</div>
		</div>
	</div>
</body>

</html>