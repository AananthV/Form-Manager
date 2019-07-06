<div class="container">
  <span class="d-flex flex-column flex-md-row align-items-center justify-content-md-between">
    <h1>Responses - <?php echo $form_title; ?></h1>
    <?php
      echo '<a class="btn btn-info mb-2 mb-md-0" href="?individual_responses=true&id=' . $form_id . '">View Individual Responses</a>';
    ?>
  </span>
  <ul id="response-list" class="list-group">
    <?php
      $form_items = getFormAnswers($form_id);
      $question_number = 1;
      $charts = array();

      foreach ($form_items as $form_item) {
        echo '<li class="list-group-item">'
            .'<span class="mylist-item-title">' . $question_number++ . '. ' . $form_item['question'] . '</span>';

        if($form_item['type'] <= 1) {
          echo '<div class="answer-header-text">Answers:</div>'
              .'<ul class="list-group">'
              .'<li class="list-group-item d-none d-md-flex flex-row align-items-center">'
              .'  <span class="col col-md-3 col-lg-2"><strong>Username</strong></span>'
              .'  <span class="col"><strong>Answer</strong></span>'
              .'</li>'
              .'<div class="text-answer-holder">';

          foreach ($form_item['answers'] as $form_answer) {
            echo '<li class="list-group-item d-flex flex-column flex-md-row align-items-center">'
                .'  <span class="col col-md-3 col-lg-2"><span class="d-md-none"><strong>Username: </strong></span>' . $form_answer['username'] . '</span>'
                .'  <hr class="d-md-none col-11">'
                .'  <span class="col"><span class="d-md-none"><strong>Answer: </strong></span>' . $form_answer['value'] . '</span>'
                .'</li>';
          }

          echo '</div></ul>';

        } else {
          // Div to hold pie chart.
          echo '<div id="chart-' . $question_number . '" class="chart"></div>';

          $chart = array(
            'id' => $question_number,
            'choices' => array()
          );

          foreach ($form_item['answers']['choices'] as $choice) {
            $chart['choices'][] = array($choice['value'], $choice['times_chosen']);
          }

          $charts[] = $chart;

          if($form_item['answers']['hasOther'] == true) {
            echo '<div class="answer-header-text">Other Answers:</div>'
                .'<ul class="list-group">'
                .'<li class="list-group-item d-none d-md-flex flex-row align-items-center">'
                .'  <span class="col col-md-3 col-lg-2"><strong>Username</strong></span>'
                .'  <span class="col"><strong>Answer</strong></span>'
                .'</li>'
                .'<div class="text-answer-holder">';

            foreach ($form_item['answers']['otherAnswers'] as $form_answer) {
              echo '<li class="list-group-item d-flex flex-column flex-md-row align-items-center">'
                  .'  <span class="col col-md-3 col-lg-2"><span class="d-md-none"><strong>Username: </strong></span>' . $form_answer['username'] . '</span>'
                  .'  <hr class="d-md-none col-11">'
                  .'  <span class="col"><span class="d-md-none"><strong>Answer: </strong></span>' . $form_answer['value'] . '</span>'
                  .'</li>';
            }

            echo '</div></ul>';
          }
        }

        echo '</li>';
      }
    ?>
  </ul>
</div>

<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  // Load the Visualization API and the corechart package.
  google.charts.load('current', {'packages':['corechart']});

  // Set a callback to run when the Google Visualization API is loaded.
  google.charts.setOnLoadCallback(drawCharts);

  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawChart(chartId, chartData) {
    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Choice');
    data.addColumn('number', 'Times Chosen');
    data.addRows(chartData);

    // Set chart options
    var options = {};

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('chart-' + chartId));
    chart.draw(data, options);
  }

  let charts = JSON.parse(`<?php echo json_encode($charts); ?>`);

  function drawCharts(){
      for(let chart of charts) {
        drawChart(chart['id'], chart['choices']);
      }
  }
</script>
