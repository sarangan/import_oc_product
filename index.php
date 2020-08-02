<!DOCTYPE html>
<html>
    <style>
        .wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10%;
        }
    </style>
<body>

<div class='wrapper'>
<form action="upload.php" method="post" enctype="multipart/form-data">
  Select csv to upload:
  <input type="file" name="file" id="file" accept=".csv"/>
  <br/>
  <input type="submit" value="Upload CSV" name="submit">
</form>
</div>
</body>
</html>
