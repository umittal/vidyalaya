<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                      "http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Password Change</title>
</head>
<body>
  <h1>Change Password for {USERNAME}</h1>
  {MESSAGE}
  <form method="POST" action="changepassword.php">
  <table>
    <tr>
      <td>Enter your existing password:</td>
      <td><input type="password" size="10" name="oldPassword"></td>
    </tr>
    <tr>
      <td>Enter your new password:</td>
      <td><input type="password" size="10" name="newPassword1"></td>
    </tr>
    <tr>
      <td>Re-enter your new password:</td>
      <td><input type="password" size="10" name="newPassword2"></td>
    </tr>
  </table>
  <p><input type="submit" value="Update Password">
  </form>
  <p><a href="{HOMEPAGE}">Home</a>
  <p><a href="/dakhila/php/dataViewer2.php?command=logout">Logout</a>
</body>
</html>
