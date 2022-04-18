<?php
if (empty($_SESSION['naam']) || empty($_SESSION['id']))
	exit(header('Location: ././error'));