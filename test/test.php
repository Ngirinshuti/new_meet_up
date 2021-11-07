<?php

print_r($_POST);


header("Location: ../");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css">
    <title>Testing</title>
</head>

<body>
    <div class="formContainer">

        <form action="" method="post" class="mainForm">
            <h2 class="header">Test</h2>
            <div class="inputContainer">
                <input name="test" type="text" style="border: 1px solid;">
            </div>
            <div class="inputContainer">
                <input type="text" name="values[]" value="1" style="border: 1px solid;">
                <input type="text" name="values[]" value="2" style="border: 1px solid;">
                <input type="text" name="values[]" value="3" style="border: 1px solid;">
            </div>
            <button type="submit">Test</button>
        </form>
    </div>
</body>

</html>