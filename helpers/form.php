<?php
  function addValidation($question, $validation) {
    $values = array(
      'question' => $question,
      'type' => $validation->type,
      'subtype' => $validation->subtype,
      'left_' => (is_numeric($validation->left) ? floatval($validation->left) : 0),
      'right_' => (is_numeric($validation->right) ? floatval($validation->right) : 0)
    );
    return insertValues('validation', $values);
  }

  function addQuesion($parent_form, $question, $description, $type, $isRequired, $hasOther, $isValidated) {
      $values = array(
        'parent_form' => $parent_form,
        'question' => $question,
        'description' => $description,
        'type' => $type,
        'isRequired' => $isRequired,
        'hasOther' => $hasOther,
        'isValidated' => $isValidated
      );
      return insertValues('questions', $values);
  }

  function addChoices($parent_question, $choices) {
    foreach ($choices as $choice) {
      $values = array(
        'parent_question' => $parent_question,
        'choice' => $choice->value,
        'isOther' => property_exists($choice, 'isOther') && $choice->isOther || false
      );
      if($values['isOther'] == true) {
        $values['choice'] == 'Other';
      }
      if(insertValues('choices', $values) == false) return false;
    }
  }

  function addFormData($owner, $title, $description, $questions, $expires = false, $expiry = null) {
    $values = array(
      'owner' => $owner,
      'title' => $title,
      'description' => $description,
      'questions' => $questions,
      'expires' => $expires,
      'expiry' => $expiry
    );
    return insertValues('forms', $values);
  }

  function getExpiry($expires, $expiry) {
    if($expires == true) {
      $datetime = date_create($expiry->datetime, timezone_open($expiry->timezone));
      $datetime->setTimezone(timezone_open(SERVER_TIMEZONE));
      return $datetime->format('Y-m-d H:i:s');
    }
    return null;
  }

  function addForm($form_data) {
    $form_id = addFormData(
      $form_data->owner,
      $form_data->meta->title,
      $form_data->meta->description,
      count($form_data->items),
      $form_data->meta->expires,
      getExpiry($form_data->meta->expires, $form_data->meta->expiry)
    );

    if($form_id == false) return false;

    getDBInstance()->query('UPDATE users SET forms = forms + 1 WHERE id=' . $form_data->owner);

    foreach ($form_data->items as $item) {
      $question_id = addQuesion(
        $form_id,
        $item->question,
        $item->description,
        $item->type,
        $item->isRequired,
        property_exists($item, 'hasOther') && $item->hasOther,
        property_exists($item, 'isValidated') && $item->isValidated
      );

      if($question_id == false) return false;

      if(property_exists($item, 'isValidated') && $item->isValidated) {
        if(addValidation($question_id, $item->validation) == false) {
          return false;
        }
      }

      if(property_exists($item, 'choices')) {
        addChoices($question_id, $item->choices);
      }
    }

    return $form_id;
  }

  function getForm($form_id) {
    $form_data = array();

    // Get Meta
    $form_data['meta'] = (object) getValues('forms', array('id', 'title', 'description', 'questions', 'active', 'expires', 'expiry'), array('id' => $form_id))[0];
    $form_data['meta']->active = $form_data['meta']->active == 1;
    $form_data['meta']->expires = $form_data['meta']->expires == 1;

    // Get Questions
    $form_data['items'] = array();
    $form_questions = getValues('questions', array('*'), array('parent_form' => $form_id));
    if(count($form_questions) != $form_data['meta']->questions) return false;
    foreach ($form_questions as $question) {
      $item = array(
        'id' => $question['id'],
        'question' => $question['question'],
        'description' => $question['description'],
        'type' => $question['type'],
        'isRequired' => $question['isRequired'] == 1,
        'hasOther' => $question['hasOther'] == 1,
        'isValidated' => $question['isValidated'] == 1
      );

      //Get Validation if it exits.
      if($question['isValidated'] == 1) {
        $item['validation'] = getValues(
          'validation',
          array('type', 'subtype', 'left_', 'right_'),
          array('question' => $question['id'])
        )[0];
        $item['validation']['left'] = $item['validation']['left_'];
        $item['validation']['right'] = $item['validation']['right_'];
      }

      // Get Choices if they exist
      if($question['type'] >= 2) {
        $item['choices'] = array();
        $choices = getValues('choices', array('*'), array('parent_question' => $question['id']));
        foreach ($choices as $choice) {
          $item['choices'][] = array('id' => $choice['id'], 'value' => $choice['choice'], 'isOther' => $choice['isOther']);
        }
      }
      $form_data['items'][] = (object) $item;
    }
    return $form_data;
  }

  function addTextAnswer($answer_id, $question_id, $answer, $type = 'short') {
    $insert = insertValues(
      $type . '_text_answers',
      array(
        'parent_answer' => $answer_id,
        'question' => $question_id,
        'value' => $answer
      )
    );
    return $insert != false;
  }

  function addChoiceAnswer($answer_id, $question_id, $choice_id) {
    $insert = insertValues(
      'choice_answers',
      array(
        'parent_answer' => $answer_id,
        'question' => $question_id,
        'choice' => $choice_id
      )
    );
    if($insert==false) return false;
    $updateChoices = getDBInstance()->query('UPDATE choices SET times_chosen = times_chosen + 1 WHERE id=' . $choice_id);
    return true;
  }

  function addResponseNotification($form_id, $answerer_id) {
    $form_data = getValues('forms', array('owner', 'title'), array('id' => $form_id))[0];
    $answerer = getValues('users', array('username'), array('id' => $answerer_id))[0]['username'];
    return insertValues(
      'notifications',
      array(
        'user' => $form_data['owner'],
        'type' => 1 . '' . $form_id,
        'title' => $form_data['title'],
        'text' => '<strong>' . $answerer . '</strong> answered your form.'
      )
    );
  }

  function addAnswer($answer_data) {
    $answer_id = insertValues(
      'answers',
      array(
        'user' => $answer_data->user,
        'form' => $answer_data->form
      )
    );

    if($answer_id == false) return false;

    getDBInstance()->query('UPDATE forms SET answers = answers + 1 WHERE id=' . $answer_data->form);

    getDBInstance()->query('UPDATE users SET answers = answers + 1 WHERE id=' . $answer_data->user);

    addResponseNotification($answer_data->form, $answer_data->user);

    foreach ($answer_data->answers as $answer) {
      if($answer->type == 0 && strlen($answer->answer) > 0) {
        $t_id = addTextAnswer($answer_id, $answer->question_id, $answer->answer, 'short');
        if($t_id == false) return false;
      } else if ($answer->type == 1 && strlen($answer->answer) > 0) {
        $t_id = addTextAnswer($answer_id, $answer->question_id, $answer->answer, 'long');
        if($t_id == false) return false;
      } else if ($answer->type == 2 || $answer->type == 3) {
        foreach ($answer->answer->selectedIds as $selectedChoice) {
          $c_id = addChoiceAnswer($answer_id, $answer->question_id, $selectedChoice);
          if($c_id == false) return false;
        }
        if($answer->answer->otherSelected && strlen($answer->answer->otherAnswer) > 0) {
          $t_id = addTextAnswer($answer_id, $answer->question_id, $answer->answer->otherAnswer, 'short');
          if($t_id == false) return false;
        }
      } else if ($answer->type == 4 && strlen($answer->answer) > 0) {
        $c_id = addChoiceAnswer($answer_id, $answer->question_id, $answer->answer);
        if($c_id == false) return false;
      }
    }
    return true;
  }

  function getAnswer($form_id, $answer_id) {
    if(
      !checkIfRowExists('answers', array('id' => $answer_id, 'form' => $form_id))
      ) return false;

    $answer_data = array();

    $meta_data = getValues('answers', array('user', 'answered'), array('id' => $answer_id));

    $answer_data['username'] = getValues('users', array('username'), array('id' => $meta_data[0]['user']))[0]['username'];
    $answer_data['answered'] = date_create($meta_data[0]['answered']);

    $form_questions = getValues(
      'questions',
      array('id', 'type'),
      array('parent_form' => $form_id)
    );

    foreach ($form_questions as $question) {
      if($question['type'] == 0) {
        $answer = getValues(
          'short_text_answers',
          array('value'),
          array('parent_answer' => $answer_id, 'question' => $question['id'])
        );
        if($answer !== false) {
          $answer_data[$question['id']] = $answer[0]['value'];
        } else {
          $answer_data[$question['id']] = 'Not Answered.';
        }
      } else if ($question['type'] == 1) {
        $answer = getValues(
          'long_text_answers',
          array('value'),
          array('parent_answer' => $answer_id, 'question' => $question['id'])
        );
        if($answer != false) {
          $answer_data[$question['id']] = $answer[0]['value'];
        } else {
          $answer_data[$question['id']] = 'Not Answered.';
        }
      } else {
        $choice_answer = array(
          'selectedIds' => array(),
          'otherSelected' => false,
          'otherAnswer' => ''
        );
        $choice_ids = getValues(
          'choice_answers',
          array('choice'),
          array('parent_answer' => $answer_id, 'question' => $question['id'])
        );
        if($choice_ids === false) {
          $answer_data[$question['id']] = 'Not Answered.';
        } else {
          foreach ($choice_ids as $choice_id) {
            $choice_answer['selectedIds'][] = $choice_id['choice'];
            if(
              $choice_answer['otherSelected'] == false &&
              getValues(
                'choices',
                array('isOther'),
                array('id' => $choice_id['choice'])
                )[0]['isOther'] == 1
            ) {
              $choice_answer['otherSelected'] = true;
              $choice_answer['otherAnswer'] = getValues(
                'short_text_answers',
                array('value'),
                array('parent_answer' => $answer_id, 'question' => $question['id'])
              )[0]['value'];
            }
          }
          $answer_data[$question['id']] = (object) $choice_answer;
        }
      }
    }
    return $answer_data;
  }

  function getAnswerer($answer_id) {
    $user_id = getValues('answers', array('user'), array('id' => $answer_id))[0]['user'];
    if($user_id == false) return false;
    return getValues('users', array('username'), array('id' => $user_id))[0]['username'];
  }

  function getFormAnswers($form_id) {
    if(
      !checkIfRowExists('forms', array('id' => $form_id))
      ) return false;

    $form_answers = array();

    $form_questions = getValues(
      'questions',
      array('id', 'type', 'question', 'hasOther'),
      array('parent_form' => $form_id)
    );

    foreach ($form_questions as $question) {

      $form_item = array(
        'question' => $question['question'],
        'type' => $question['type'],
        'answers' => array()
      );

      if($question['type'] == 0) {

        $user_answers = getValues(
          'short_text_answers',
          array('parent_answer', 'value'),
          array('question' => $question['id'])
        );

        foreach ($user_answers as $user_answer) {
          $form_item['answers'][] = array(
            'username' => getAnswerer($user_answer['parent_answer']),
            'value' => $user_answer['value']
          );
        }

      } else if($question['type'] == 1) {

        $user_answers = getValues(
          'long_text_answers',
          array('parent_answer', 'value'),
          array('question' => $question['id'])
        );

        foreach ($user_answers as $user_answer) {
          $form_item['answers'][] = array(
            'username' => getAnswerer($user_answer['parent_answer']),
            'value' => $user_answer['value']
          );
        }

      } else if($question['type'] < 5) {

        $answers = array(
          'choices' => array(),
          'hasOther' => $question['hasOther'] == 1,
          'otherAnswers' => array()
        );

        $choices = getValues(
          'choices',
          array('choice', 'times_chosen'),
          array('parent_question' => $question['id'])
        );

        foreach ($choices as $choice) {
          $answers['choices'][] = array(
            'value' => $choice['choice'],
            'times_chosen' => $choice['times_chosen']
          );
        }

        if($answers['hasOther'] == true) {

          $otherAnswers = getValues(
            'short_text_answers',
            array('parent_answer', 'value'),
            array('question' => $question['id'])
          );

          foreach ($otherAnswers as $user_answer) {
            $answers['otherAnswers'][] = array(
              'username' => getAnswerer($user_answer['parent_answer']),
              'value' => $user_answer['value']
            );
          }

        }

        $form_item['answers'] = $answers;

      }

      $form_answers[] = $form_item;

    }

    return $form_answers;
  }
?>
