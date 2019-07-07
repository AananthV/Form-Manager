<?php
  // Changing page.
  $page = 1;
  if(
    isset($_GET['page']) &&
    is_numeric($_GET['page']) &&
    intval($_GET['page']) > 0
  ) {
    $page = $_GET['page'];
  }

  // Order
  $sort_order = 'ASC';
  if(
    isset($_GET['sort']) &&
    $_GET['sort'] == 'DESC'
  ) {
    $sort_order = 'DESC';
  }

  // Exact Search
  $search_string = '';
  if(
    isset($_GET['search'])
  ) {
    $search_string = $_GET['search'];
  }
?>

<div class="container">
  <span class="d-flex flex-column flex-md-row align-items-center justify-content-md-between">
    <h1>Responses - <?php echo $form_title; ?></h1>
    <?php
      echo '<a class="btn btn-info mb-2 mb-md-0" href="?id=' . $form_id . '">View Group Responses</a>';
    ?>
  </span>
  <div id="response-list">
    <?php
      $search = array(
        'form' => $form_id
      );

      if($search_string != '') {
        $user_id = getValues('users', array('id'), array('username' => $search_string));
        if($user_id == false) {
          $search['user'] = 0;
        } else {
          $search['user'] = $user_id[0]['id'];
        }
      }

      $form = (object) getForm($form_id);
      $answer_ids = getValues(
        'answers',
        array('id'),
        $search,
        array('answered' => $sort_order),
        array('offset' => 10*($page - 1), 'count' => 10)
      );

      function getChoice($choices, $id) {
        foreach ($choices as $choice) {
          if($choice['id'] == $id) {
            if($choice['isOther'] == true) return false;
            return $choice['value'];
          }
        }
      }
    ?>

    <li class="list-group-item d-flex flex-column-reverse flex-md-row justify-content-between align-items-center">
      <div class="col-12 col-md-4 d-flex flex-column flex-sm-row align-items-center justify-content-around">
        <span class="mylist-item-title mr-0 mr-sm-2 mb-2 mb-sm-0">Sort By<span class="d-none d-sm-inline">:</span></span>
        <select class="form-control col mb-2 mb-sm-0" id="sort-order">
          <option value="ASC">Earliest First</option>
          <option value="DESC">Latest First</option>
        </select>
        <button class="btn btn-outline-success" onclick="sort_button()">Go!</button>
      </div>
      <div class="input-group col-12 col-md-4 mb-2 mb-md-0">
        <input type="search" class="form-control" placeholder="Username" aria-label="Search" id="search-string" value="<?php $search_string ?>">
        <div class="input-group-append">
          <button class="btn btn-outline-success" type="button" id="search-button" onclick="search_button()">Search</button>
        </div>
      </div>
    </li>

    <?php
      echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
      if($page > 1) {
        echo '<a class="btn btn-outline-primary" href="?individual_responses=true&id=' . $form_id . '&page=' . ($page - 1) . '">Prev</a>';
      } else {
        echo '<button type="button" class="btn btn-outline-primary" disabled>Prev</button>';
      }
      if(count($answer_ids) == 10) {
        echo '<a class="btn btn-outline-primary" href="?individual_responses=true&id=' . $form_id . '&page=' . ($page + 1) . '">Next</a>';
      } else {
        echo '<button type="button" class="btn btn-outline-primary" disabled>Next</button>';
      }
      echo '</li>';

      if(count($answer_ids) == 0) {
        echo '<li class="list-group-item text-center">';
        if($search_string != '') {
          echo '<strong>' . $search_string . '</strong> has not responded.';
        } else {
          echo 'No responses yet';
        }
        echo '</li>';
      }

      foreach ($answer_ids as $aids) {
        $answer_id = $aids['id'];
        $answer = getAnswer($form_id, $answer_id);

        echo '<div class="card" onclick="toggle_card_body(this)">'
            .'  <div class="card-header d-flex justify-content-between">'
            .'    <span>' . $answer['username'] . '</span>'
            .'    <span>' . $answer['answered']->format('d-m-y') . '</span>'
            .'  </div>'
            .'  <div class="card-body d-none">';

        $question_number = 1;
        foreach ($form->items as $item) {
          echo '<h5 class="card-title">'. $question_number++ . '. ' . $item->question . '</h5>';
          $answer_text = '';
          if(is_string($answer[$item->id])) {
            $answer_text = $answer[$item->id];
          } else if (is_object($answer[$item->id])) {
            $answer_chosen = array();
            foreach ($answer[$item->id]->selectedIds as $choice_id) {
              $choice = getChoice($item->choices, $choice_id);
              if($choice !== false) {
                $answer_chosen[] = $choice;
              }
            }
            if($answer[$item->id]->otherSelected) {
              $answer_chosen[] = $answer[$item->id]->otherAnswer;
            }
            $answer_text = implode(', ', $answer_chosen);
          }
          if($answer_text == '') {
            $answer_text = 'Not Answered.';
          }
          echo '<p class="card-text">' . $answer_text . '</p>';
        }
        echo '</div></div>';
      }

      echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
      if($page > 1) {
        echo '<a class="btn btn-outline-primary" href="?individual_responses=true&id=' . $form_id . '&page=' . ($page - 1) . '">Prev</a>';
      } else {
        echo '<button type="button" class="btn btn-outline-primary" disabled>Prev</button>';
      }
      if(count($answer_ids) == 10) {
        echo '<a class="btn btn-outline-primary" href="?individual_responses=true&id=' . $form_id . '&page=' . ($page + 1) . '">Next</a>';
      } else {
        echo '<button type="button" class="btn btn-outline-primary" disabled>Next</button>';
      }
      echo '</li>';

    ?>
  </div>
</div>

<script type="text/javascript">

  document.querySelector('#sort-order').value = '<?php echo $sort_order; ?>';

  let sort_button = function() {
    let sort_order = document.querySelector('#sort-order').value;
    if(sort_order != '<?php echo $sort_order; ?>') {
      window.location = "<?php echo DOMAIN . 'view_responses.php?id=' . $form_id . '&individual_responses=true&search' . $search_string . '&sort=';?>" + sort_order;
    }
  }

  let search_button = function() {
    let search_string = document.querySelector('#search-string').value;
    window.location = "<?php echo DOMAIN . 'view_responses.php?id=' . $form_id . '&individual_responses=true&sort' . $sort_order . '&search=';?>" + search_string;
  }

  let toggle_card_body = function(e) {
    e.querySelector('.card-body').classList.toggle('d-none');
  }
</script>
