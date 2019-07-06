<div class="container">
  <span class="d-flex flex-column flex-md-row align-items-center justify-content-md-between">
    <h1>Responses - <?php echo $form_title; ?></h1>
    <?php
      echo '<a class="btn btn-info mb-2 mb-md-0" href="?id=' . $form_id . '">View Group Responses</a>';
    ?>
  </span>
  <div id="response-list">
    <?php
      $form = (object) getForm($form_id);
      $answer_ids = getValues(
        'answers',
        array('id'),
        array('form' => $form_id)
      );

      function getChoice($choices, $id) {
        foreach ($choices as $choice) {
          if($choice['id'] == $id) {
            if($choice['isOther'] == true) return false;
            return $choice['value'];
          }
        }
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
    ?>
  </div>
</div>

<script type="text/javascript">
  let toggle_card_body = function(e) {
    e.querySelector('.card-body').classList.toggle('d-none');
  }
</script>
