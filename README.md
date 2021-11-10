<b>The first, open database.php file in config folder and setting connect to your DB</b>

<h4>Console command</h4>
<b>php public/index.php</b> - create new DB 'operators' and create tables 'users' and 'phones'<br><hr>
<b>php app/Users.php getUser <i>id</i></b> - get user data<br>
<b>php app/Users.php earnedPhone <i>phone</i> <i>sum</i></b> - update user data<br>
<b>php app/Users.php addUser <i>name</i> <i>birthday</i> <i>...phone</i></b> - add new user<br>
<b>php app/Users.php addPhoneForUser <i>id</i> <i>phone</i></b> - add new phone for user<br>
<b>php app/Users.php delete <i>id</i></b> - delete user<br><hr>