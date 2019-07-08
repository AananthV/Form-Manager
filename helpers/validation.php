<?php
  function validate_answer($answer) {
    // Check if form exists.
    if(
      !property_exists($answer, 'form') ||
      !checkIfRowExists('forms', array('id' => $answer->form))
      ) return false;

    // Check if user exists.
    if(
      !property_exists($answer, 'user') ||
      !checkIfRowExists('users', array('id' => $answer->user))
      ) return false;

    // Check if user has already submitted form.
    if(
      checkIfRowExists('answers', array('user' => $answer->user, 'form' => $answer->form))
      ) return "ERROR: ALREADY SUBMITTED";

    // Check if number of questions match.
    if(
      !property_exists($answer, 'answers') ||
      !is_array($answer->answers) ||
      getValues('forms', array('questions'), array('id' => $answer->form))[0]['questions'] != count($answer->answers)
      ) return false;

    $invalid_answers = array();

    foreach ($answer->answers as $item) {
      // Check if question exists and if the question-data matches.
      if(
        !property_exists($item, 'question_id') ||
        !checkIfRowExists(
          'questions',
          array(
            'id' => $item->question_id,
            'parent_form' => $answer->form,
            'type' => $item->type
            )
          )
        ) return false;

      // Check if answer exists.
      if(!property_exists($item, 'answer')) {
        return false;
      }

      $question_data = getValues(
        'questions',
        array('isRequired', 'hasOther', 'isValidated'),
        array('id' => $item->question_id)
        )[0];

      if($item->type < 2) {
        // Check if answer is string and check its length.
        if(
          !is_string($item->answer) ||
          ($item->type == 0 && strlen($item->answer) > 128) ||
          ($item->type == 1 && strlen($item->answer) > 512)
          ) return false;

        if(
          $question_data['isRequired'] == 1 && $item->answer == ''
        ) {
          $invalid_answers[] = $item->question_id;
          continue;
        }

        if($question_data['isValidated'] == 1) {
          $validation = (object) getValues(
            'validation',
            array('type', 'subtype', 'left_', 'right_'),
            array('question' => $item->question_id)
            )[0];

          $validation['left'] = $validation['_left'];
          $validation['right'] = $validation['_right'];

          if(
            !validate($item->answer, $validation)
          ) {
            $invalid_answers[] = $item->question_id;
            continue;
          }
        }
      } else if ($item->type < 4) {
        // Check if selectedIds exists.
        if(
          !property_exists($item->answer, 'selectedIds') ||
          !is_array($item->answer->selectedIds)
          ) return false;

        if(
          $question_data['isRequired'] == 1 && count($item->answer->selectedIds) == 0
        ) {
          $invalid_answers[] = $item->question_id;
          continue;
        }

        foreach ($item->answer->selectedIds as $choice_id) {
          // Check if choice exists.
          if(
            !is_int($choice_id) ||
            !checkIfRowExists(
              'choices',
              array(
                'id' => $choice_id,
                'parent_question' => $item->question_id
                )
              )
            ) return false;
        }

        // Other Choice.
        if(
          property_exists($item->answer, 'otherSelected') &&
          $item->answer->otherSelected == true
        ){
          if(
            !property_exists($item->answer, 'otherAnswer') ||
            !is_string($item->answer->otherAnswer) ||
            strlen($item->answer->otherAnswer) > 128
            ) return false;

          if(
            $question_data['isRequired'] == 1 && $item->answer->otherAnswer == ''
          ) {
            $invalid_answers[] = $item->question_id;
            continue;
          }
        }
      } else if ($item->type == 4) {
        // Check if answer is string and check its length.
        if(
          !is_string($item->answer) ||
          strlen($item->answer) > 11
          ) return false;

        if(
          $question_data['isRequired'] == 1 && $item->answer == ''
        ) {
          $invalid_answers[] = $item->question_id;
          continue;
        }

        if(
          !checkIfRowExists(
            'choices',
            array(
              'id' => $item->answer,
              'parent_question' => $item->question_id
              )
            )
          ) return false;
      }
    }

    if(count($invalid_answers) > 0) {
      return json_encode($invalid_answers);
    }

    // If All of the above conditions are met
    return true;
  }

  function validate_form($form) {
    // Check if user exists.
    if(
      !property_exists($form, 'owner') ||
      !checkIfRowExists('users', array('id' => $form->owner))
      ) return 'ERROR: INVALID OWNER';

    // Check if meta exists
    if(
      !property_exists($form, 'meta') ||
      !is_object($form->meta)
      ) return 'ERROR: META DOES NOT EXIST';

    // Form title
    if(
      !property_exists($form->meta, 'title') ||
      !is_string($form->meta->title) ||
      strlen($form->meta->title) > 128
      ) return 'ERROR: TITLE DOES NOT EXIST';

    // Form description
    if(
      !property_exists($form->meta, 'description') ||
      !is_string($form->meta->description) ||
      strlen($form->meta->description) > 512
      ) return 'ERROR: DESCRIPTION DOES NOT EXIST';

    // Expires.
    if(
      !property_exists($form->meta, 'expires') &&
      !is_bool($form->meta->expires)
      ) return 'ERROR: EXPIRES DOES NOT EXIST';

    // Expiry
    if($form->meta->expires == true) {
      if(
        !property_exists($form->meta, 'expiry') ||
        !is_object($form->meta->expiry)
        ) return 'ERROR: EXPIRY DOES NOT EXIST';

      if(
        !property_exists($form->meta->expiry, 'datetime') ||
        !is_string($form->meta->expiry->datetime)
        ) return 'ERROR: EXPIRY DATETIME DOES NOT EXIST';

      if(
        !property_exists($form->meta->expiry, 'timezone') ||
        !is_string($form->meta->expiry->timezone)
        ) return 'ERROR: EXPIRY TIMEZONE DOES NOT EXIST';

      try {
        date_create($form->meta->expiry->datetime, timezone_open($form->meta->expiry->timezone));
      } catch (Exception $e) {
        return 'ERROR: EXPIRY INVALID';
      }
    }

    // Check if form items exists.
    if(
      !property_exists($form, 'items') ||
      !is_array($form->items) ||
      count($form->items) == 0
      ) return 'ERROR: EMPTY FORM';

    foreach ($form->items as $item) {
      // Item.
      if(!is_object($item)) return false;

      // Question.
      if(
        !property_exists($item, 'question') ||
        !is_string($item->question) ||
        strlen($item->question) > 128
        ) return false;

      // Description.
      if(
        !property_exists($item, 'description') ||
        !is_string($item->description) ||
        strlen($item->description) > 512
        ) return false;

      // Type.
      if(
        !property_exists($item, 'type') ||
        !is_int($item->type) ||
        !isBetweenEquals($item->type, 0, 4)
        ) return false;

      // isRequired
      if(
        !property_exists($item, 'isRequired') ||
        !is_bool($item->isRequired)
        ) return false;

      // Has Other
      if(
        property_exists($item, 'hasOther') &&
        !is_bool($item->hasOther)
        ) return false;

      // isValidated
      if(
        property_exists($item, 'isValidated') &&
        !is_bool($item->isValidated)
        ) return false;

      // Validate Validation xD
      if(
        property_exists($item, 'isValidated') &&
        $item->isValidated
      ) {
        if(
          !property_exists($item, 'validation') ||
          !is_object($item->validation)
          ) return false;

        // Type
        if(
          !property_exists($item->validation, 'type') ||
          !is_string($item->validation->type) ||
          strlen($item->validation->type) > 32
          ) return false;

        // subtype
        if(
          !property_exists($item->validation, 'subtype') ||
          !is_string($item->validation->subtype) ||
          strlen($item->validation->subtype) > 32
          ) return false;

        if(
          !property_exists($item->validation, 'left')
          ) return false;

        if(
          !property_exists($item->validation, 'right')
          ) return false;
      }

      // Choices.
      if($item->type > 1) {
        if(
          !property_exists($item, 'choices') ||
          !is_array($item->choices) ||
          count($item->choices) == 0
          ) return false;

        $hasOther = false;
        foreach ($item->choices as $choice) {
          if(!is_object($choice)) return false;

          // Value
          if(
            !property_exists($choice, 'value') ||
            !is_string($choice->value) ||
            strlen($choice->value) > 128
            ) return false;

          // isOther
          if(property_exists($choice, 'isOther')) {
            if(!is_bool($choice->isOther)) return false;

            if($choice->isOther && $hasOther) return false; // Can't have two other options.

            if($choice->isOther) {
              $hasOther = true;
            }
          }
        }

        if($item->hasOther != $hasOther) return false;
      }
    }

    // If All of the above conditions are met
    return true;
  }

  function validate_login($login) {
    if(!is_object($login)) return false;

    if(
      !property_exists($login, 'username') ||
      !validate_username($login->username)
      ) return false;

    if(
      !property_exists($login, 'password') ||
      !validate_password($login->password)
      ) return false;

    return true;
  }

  function validate_register($register) {
    $validation = (object) array(
      'first_name' => false,
      'last_name' => false,
      'username' => false,
      'password' => false
    );

    if(!is_object($register)) {
      return $validation;
    }

    if(
      property_exists($register, 'first_name') &&
      validate_name($register->first_name)
    ) {
      $validation->first_name = true;
    }

    if(
      property_exists($register, 'last_name') &&
      validate_name($register->last_name)
    ) {
      $validation->last_name = true;
    }

    if(
      property_exists($register, 'username') &&
      validate_username($register->username)
    ) {
      $validation->username = true;
    }

    if(
      property_exists($register, 'password') &&
      validate_password($register->password)
    ) {
      $validation->password = true;
    }

    return $validation;
  }
?>
