<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EliteFort</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <!-- <link rel="stylesheet" href="assets/css/login.css"> -->

    <?php
    // echo $_SERVER['DOCUMENT_ROOT'] . '/assets/css/' . basename($_SERVER['PHP_SELF'], ".php") . ".css";
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/css/' . basename($_SERVER['PHP_SELF'], ".php") . ".css")){
    
    ?>
        <link rel="stylesheet" href="assets/css/<?=basename($_SERVER['PHP_SELF'], ".php")?>.css"/>
    <?php
    }
    ?>
</head>