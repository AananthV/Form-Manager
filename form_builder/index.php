<?php
  session_start();
  $currentPage = 'Home';
?>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/cdf20ee219.js"></script>

    <link rel="stylesheet" href="index.css">
    <title>Form Builder</title>
  </head>
  <body>
    <?php require '../elements/navbar.php' ?>

    <div class="container">
      <div class="item" id="form-answer-wrapper">
        <form>
          <div id="header-container">
          </div>
          <div id="item-container">
          </div>
          <button type="submit" class="btn btn-outline-success" id="submit-button" onclick="submitForm()">Submit</button>
        </form>
      </div>

      <div class="row">
        <div class="form col-md-11" id="form-wrapper">
          <div id="header-container" class="item">
          </div>
          <div id="item-container">
          </div>
          <div id="footer-container" class="d-flex justify-content-center">
            <button type="button" class="btn btn-outline-success" id="submit-button" onclick="submitForm()">Submit</button>
          </div>
        </div>
        <div class="col-md-1" id="item-menu-container">
          <div id="add-item-menu">
            <button type="button" class="btn" title="Short Answer" onclick="addItem('Short Answer')"><i class="fas fa-font fa-fw"></i></button>
            <button type="button" class="btn" title="Paragraph" onclick="addItem('Paragraph')"><i class="fas fa-align-left fa-fw"></i></button>
            <button type="button" class="btn" title="Multiple Choice" onclick="addItem('Multiple Choice')"><i class="fas fa-dot-circle fa-fw"></i></button>
            <button type="button" class="btn" title="Checkboxes" onclick="addItem('Checkboxes')"><i class="far fa-check-square fa-fw"></i></button>
            <button type="button" class="btn" title="Dropdown" onclick="addItem('Dropdown')"><i class="fas fa-chevron-circle-down fa-fw"></i></button>
          </div>
        </div>
      </div>
    </div>



    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script src="FormBuilderHelper.js" type="text/javascript" charset="utf-8"></script>
    <script src="FormItems.js" type="text/javascript" charset="utf-8"></script>
    <script src="DataValidation.js" type="text/javascript" charset="utf-8"></script>
    <script src="Form.js" type="text/javascript" charset="utf-8"></script>

    <?php require '../elements/auth.php' ?>
  </body>
</html>
