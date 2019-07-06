class FormItem {
  constructor(itemType) {
    this.type = itemType;
    this.question = '';
    this.description = '';
    this.extras = {
      "Description": false
    };
    this.isRequired = false;
    this.id = Math.random().toString().replace('0.', '');
  }
  constructItem(itemData) {
    for(let [key, value] of Object.entries(itemData)) {
      this[key] = value;
    }
    if(this.isValidated != null && this.isValidated == true) {
      this.extras['Data Validation'] = true;
      this.validator = new DataValidation(
        this.id,
        itemData['validation']['type'],
        itemData['validation']['subtype'],
        itemData['validation']['left'],
        itemData['validation']['right']
      );
    }
    if(this.description.length > 0) {
      this.extras['Description'] = true;
    }
  }
  getItemEditHeader() {
    // Create Item Header div
    let header = document.createElement('div');
    header.setAttribute('id', 'item-header-' + this.id);

    // Question row
    let questionRow = document.createElement('div');
    questionRow.setAttribute('class', 'd-flex flex-column-reverse flex-sm-row justify-content-between');

    // Question Input
    let questionInput = document.createElement('input');
    questionInput.setAttribute('type', 'text');
    questionInput.setAttribute('maxlength', 128);
    questionInput.setAttribute('class', 'form-control col-md-8');
    questionInput.setAttribute('id', 'question-' + this.id);
    questionInput.setAttribute('placeholder', 'Question');
    questionInput.value = this.question || '';
    let self = this;
    questionInput.oninput = function() {
      self.question = this.value;
    }

    questionRow.appendChild(questionInput);

    // Line break for xs devices.
    let lineBreak = document.createElement('br');
    lineBreak.setAttribute('class', '.hidden-sm-up');
    questionRow.appendChild(lineBreak);

    questionRow.appendChild(getItemTypeDropdown(this.type, this.id));

    header.appendChild(questionRow);

    if(this.extras['Description']) {
      header.appendChild(document.createElement('br'));
      let descriptionInput = document.createElement('textarea');
      //descriptionInput.setAttribute('type', 'text');
      descriptionInput.setAttribute('maxlength', 512);
      descriptionInput.setAttribute('class', 'form-control col');
      descriptionInput.setAttribute('id', 'description-' + this.id);
      descriptionInput.setAttribute('placeholder', 'Description');
      descriptionInput.setAttribute('rows', 3);
      descriptionInput.innerHTML = this.description.replace(/<br\s*[\/]?>/gi, "\n") || '';
      descriptionInput.oninput = function() {
        self.description = this.value;
      }
      header.appendChild(descriptionInput);
    }

    return header;
  }
  getItemEditFooter() {
    // Create Footer Div
    let footer = document.createElement('div');
    footer.setAttribute('class', 'row justify-content-end');
    footer.setAttribute('id', 'item-footer-' + this.id);

    // Create Enclosing Div
    let enclosingDiv = document.createElement('div');
    enclosingDiv.setAttribute('class', 'col-md-5 col-lg-4 col-xl-3 d-flex align-items-center justify-content-between');

    enclosingDiv.appendChild(getItemRequiredSwitch(this.id, this.isRequired));

    for(let button of getFooterButtons(this.id)) {
      enclosingDiv.appendChild(button);
    }

    enclosingDiv.appendChild(getFooterMenuDropup(this.id, this.extras));

    footer.appendChild(enclosingDiv);

    return footer;
  }
  getItemEdit() {
    // Create Main Div
    let item = document.createElement('div');
    item.setAttribute('class', 'item');
    item.setAttribute('id', 'item-' + this.id);
    item.appendChild(this.getItemEditHeader());
    item.appendChild(document.createElement('br'));
    item.appendChild(this.getItemEditBody());
    item.appendChild(document.createElement('hr'));
    item.appendChild(this.getItemEditFooter());
    return item
  }
  getItemData() {
    let item = {
      'id': this.id,
      'type': this.type,
      'question': this.question || '',
      'description': this.description.replace(/\n/g, "<br />") || '',
      'isRequired': this.isRequired
    }
    for(let [key, value] of Object.entries(this.getItemSpecificData())) {
      item[key] = value;
    }
    return item;
  }
  getItemAnswerHeader() {
    // Create Header container
    let header = document.createElement('div');
    header.setAttribute('class', 'form-item-header');

    // Create Question Span
    let question = document.createElement('div');
    question.setAttribute('class', 'form-item-question');
    question.appendChild(document.createTextNode(this.question));
    if(this.isRequired) {
      question.appendChild(document.createTextNode('*'));
    }
    header.appendChild(question);

    // Create description Span
    if(this.extras['Description'] && this.description.length > 0) {
      let description = document.createElement('div');
      description.setAttribute('class', 'form-item-description');
      description.innerHTML = this.description;
      header.appendChild(description);
    }

    return header;
  }
  getItemAnswer() {
    // Create enclo div
    let item = document.createElement('div');
    item.setAttribute('id', 'answer-' + this.id + '-container');
    item.appendChild(this.getItemAnswerHeader());
    item.appendChild(this.getItemAnswerBody());
    return item;
  }
}

class TextInput extends FormItem {
  constructor(itemType) {
    super(itemType);
    this.extras['Data Validation'] = false;
    this.validator = null;
    this.inputTypes = {
      0: {
        "inputName": "Short Answer",
        "inputLength": 128,
        "inputPlaceholder": "Short Answer Text"
      },
      1: {
        "inputName": "Paragraph",
        "inputLength": 512,
        "inputPlaceholder": "Long Answer Text"
      }
    }
  }
  cloneFromItem(oldItemType, question, description, isRequired, extras, validator = null) {
    this.question = question;
    this.description = description;
    this.isRequired = isRequired;
    this.extras['Description'] = extras['Description'];
    this.extras['Data Validation'] = extras['Data Validation'] || false;
    if(this.extras['Data Validation'] && validator != null) {
      this.validator = new DataValidation(
        this.id,
        validator.type,
        validator.subtype,
        validator.textFields[0],
        validator.textFields[1]
      );
    }
  }
  getAnswer() {
    return document.querySelector('#answer-' + this.id).value;
  }
  getItemSpecificData() {
    let itemSpecificData = {
      'isValidated': false,
      'validation': null
    };
    if(this.extras['Data Validation']) {
      itemSpecificData['isValidated'] = true;
      itemSpecificData['validation'] = this.validator.getDataValidation();
    }
    return itemSpecificData;
  }
  getItemEditBody() {
    // Create body div
    let body = document.createElement('div');

    let inputField = document.createElement('input');
    inputField.setAttribute('type', 'text');
    inputField.setAttribute('readonly', 'yeah I guess');
    inputField.setAttribute('class', 'form-control col-lg-6');
    inputField.setAttribute('placeholder', this.inputTypes[this.type]['inputPlaceholder']);

    body.appendChild(inputField);

    if(this.extras['Data Validation']) {
      body.appendChild(document.createElement('br'));
      if(this.validator == null) {
        this.validator = new DataValidation(this.id);
      }
      body.appendChild(this.validator.getDataValidationEdit());
    }
    return body;
  }
  getItemAnswerBody() {
    // Create container div
    let body = document.createElement('div');
    body.setAttribute('id', 'answer-' + this.id + '-body');
    body.setAttribute('class', 'form-item-body');
    let inputField = null;
    if(this.type == 0) { // Single line input
      inputField = document.createElement('input');
      inputField.setAttribute('type', 'text');
    } else if (this.type == 1) {
      inputField = document.createElement('textarea');
      inputField.setAttribute('rows', '4');
    }
    inputField.setAttribute('class', 'form-control');
    inputField.setAttribute('maxlength', this.inputTypes[this.type]['inputLength'].toString());
    inputField.setAttribute('id', 'answer-' + this.id);
    inputField.setAttribute('placeholder', 'Your answer.');
    if(this.isRequired) {
      inputField.setAttribute('required', 'I guess');
    }
    body.appendChild(inputField);

    return body;
  }
}

class ChoiceInput extends FormItem {
  constructor(itemType) {
    super(itemType);
    this.choices = [];
    this.addChoice();
  }
  cloneFromItem(oldItemType, question, description, isRequired, extras, hasOther = false, choices = []) {
    this.question = question;
    this.description = description;
    this.isRequired = isRequired;
    this.extras['Description'] = extras['Description'];
    this.choices = [];
    for(let choice in choices) {
      if(isNaN(choice)) break; /* FIXME: Wierd bug changes choice to "insert" after the last choice. */
      this.addChoice();
      this.choices[choice].value = choices[choice].value;
      this.choices[choice].isOther = choices[choice].isOther;
    }
    if(this.hasOther != undefined) {
      this.hasOther = hasOther;
    } else if(hasOther) {
      for(let choice in this.choices) {
        if(this.choices[choice]['isOther']) {
          this.choices.splice(choice, 1);
        }
      }
    }
    if(this.choices.length == 0) {
      this.addChoice();
    }
  }
  getItemSpecificData() {
    let itemSpecificData = {
      'hasOther': (this.hasOther || false),
      'choices': this.choices
    }
    return itemSpecificData;
  }
  addChoice() {
    this.choices.push(
      {
        'value': '',
        'id': 'option-' + Math.random().toString().replace('0.', ''),
        'isOther': false
      }
    );
  }
  removeChoice(id) {
    if(this.choices.length == 1) return;
    for(let choice in this.choices) {
      if(this.choices[choice]['id'] == id) {
        if(this.choices.length == 2 && this.hasOther == true && this.choices[choice]['isOther'] != true) return;
        if(this.choices[choice]['isOther']) {
          this.hasOther = false;
        }
        this.choices.splice(choice, 1);
        break;
      }
    }
  }
  setChoice(id, choice) {
    /*
    * Validate Choice
    */
    for(let choice in this.choices) {
      if(this.choices[choice]['id'] == id) {
        if(this.choices[choice]['isOther']) return;
        this.choices[choice]['value'] = choice;
      }
    }
  }
}

class SelectChoice extends ChoiceInput {
  constructor(choiceType) {
    super(choiceType + 2);
    this.choiceTypes = {
      0: {
        'displayName': 'Multiple Choice',
        'type': 'Radio',
        'unCheckedIcon': '<i class="far fa-circle fa-lg fa-fw"></i>',
        'checkedIcon': '<i class="fas fa-dot-circle fa-lg fa-fw"></i>'
      },
      1: {
        'displayName': 'Checkboxes',
        'type': 'Checkbox',
        'unCheckedIcon': '<i class="far fa-square fa-lg fa-fw"></i>',
        'checkedIcon': '<i class="far fa-check-square fa-lg fa-fw"></i>'
      }
    }
    this.hasOther = false;
    this.choiceType = choiceType;
  }
  addOtherChoice() {
    this.hasOther = true;
    this.choices.push(
      {
        'value': '',
        'id': Math.random().toString().replace('0.', ''),
        'isOther': true
      }
    );
  }
  getItemEditBody() {
    // Create body div
    let body = document.createElement('div');
    let otherChoice = null;

    let choiceNumber = 0;

    for(let choice in this.choices) {
      if(isNaN(choice)) break; /* FIXME: Wierd bug changes choice to "insert" after the last choice. */
      if(this.choices[choice]['isOther']) {
        otherChoice = getOptionEditField(
          this.id, this.choices.length, this.choices[choice], this.choiceTypes[this.choiceType]['unCheckedIcon']
        );
      } else {
        body.appendChild(getOptionEditField(
          this.id, choiceNumber++, this.choices[choice], this.choiceTypes[this.choiceType]['unCheckedIcon']
        ));
      }
    }

    if(this.hasOther && otherChoice) {
      body.appendChild(otherChoice);
    }

    body.appendChild(document.createElement('br'));

    body.appendChild(getOptionAddButton(
      this.id,
      this.choiceType,
      this.choices.length,
      this.hasOther,
      this.choiceTypes[this.choiceType]['unCheckedIcon']
    ));

    return body;
  }
  getItemAnswerBody() {
    // Create body container
    let body = document.createElement('div');
    body.setAttribute('id', 'answer-' + this.id + '-body');
    body.setAttribute('class', 'form-item-body');

    let otherRow = null;

    for(let choice in this.choices) {
      if(isNaN(choice)) break; /* FIXME: Wierd bug changes choice to "insert" after the last choice. */
      if(this.choices[choice]['isOther']) {
        otherRow = document.createElement('div');
        otherRow.setAttribute('class', 'row ml-auto d-flex align-items-center');

        let checkEnclosingDiv = document.createElement('div');
        checkEnclosingDiv.setAttribute('class', 'form-check');

        // Create Input
        let inputOption = document.createElement('input');
        inputOption.setAttribute('type', this.choiceTypes[this.choiceType]['type']);
        inputOption.setAttribute('class', 'form-check-input');
        inputOption.setAttribute('name', 'answer-' + this.id);
        inputOption.setAttribute('value', this.choices[choice]['id']);
        inputOption.setAttribute('id', 'answer-' + this.id + '-option-' + this.choices[choice]['id']);
        checkEnclosingDiv.appendChild(inputOption);

        // Create Label
        let inputLabel = document.createElement('label');
        inputLabel.setAttribute('for', 'answer-' + this.id + '-option-' + this.choices[choice]['id']);
        inputLabel.appendChild(document.createTextNode('Other:'));
        checkEnclosingDiv.appendChild(inputLabel);

        otherRow.appendChild(checkEnclosingDiv);

        // Create Other Input
        let inputEnclosingDiv = document.createElement('div');
        inputEnclosingDiv.setAttribute('class', 'col');

        let otherInput = document.createElement('input');
        otherInput.setAttribute('type', 'text');
        otherInput.setAttribute('class', 'form-control');
        otherInput.setAttribute('maxlength', '128');
        otherInput.setAttribute('id', 'answer-' + this.id + '-other');
        otherInput.setAttribute('placeholder', 'Your answer.');
        inputEnclosingDiv.appendChild(otherInput);

        otherRow.appendChild(inputEnclosingDiv);
      } else {
        // Create enclosing div
        let enclosingDiv = document.createElement('div');
        enclosingDiv.setAttribute('class', 'form-check');

        // Create Input
        let inputOption = document.createElement('input');
        inputOption.setAttribute('type', this.choiceTypes[this.choiceType]['type']);
        inputOption.setAttribute('class', 'form-check-input');
        inputOption.setAttribute('name', 'answer-' + this.id);
        inputOption.setAttribute('value', this.choices[choice]['id']);
        inputOption.setAttribute('id', 'answer-' + this.id + '-option-' + this.choices[choice]['id']);
        enclosingDiv.appendChild(inputOption);

        // Create Label
        let inputLabel = document.createElement('label');
        inputLabel.setAttribute('for', 'answer-' + this.id + '-option-' + this.choices[choice]['id']);
        inputLabel.appendChild(document.createTextNode(this.choices[choice]['value']));
        enclosingDiv.appendChild(inputLabel);

        body.appendChild(enclosingDiv);
      }
    }

    if(this.hasOther && otherRow) {
      body.appendChild(otherRow);
    }

    return body;
  }
  getAnswer() {
    // Get Selected Option(s)
    let answer = {
      'selectedIds': [],
      'otherSelected': false
    }
    for(let choice in this.choices) {
      if(isNaN(choice)) break;
      let option = document.querySelector('#answer-' + this.id + '-option-' + this.choices[choice]['id']);
      if(option.checked) {
        answer['selectedIds'].push(this.choices[choice]['id']);
        if(this.choices[choice]['isOther'] == true) {
          answer['otherSelected'] = true;
          answer['otherAnswer'] = document.querySelector('#answer-' + this.id + '-other').value;
        }
      }
    }
    return answer;
  }
}

class Dropdown extends ChoiceInput {
  constructor() {
    super(4);
  }
  getItemEditBody() {
    // Create body div
    let body = document.createElement('div');

    for(let choice in this.choices) {
      if(isNaN(choice)) break; /* FIXME: Wierd bug changes choice to "insert" after the last choice. */
      body.appendChild(getOptionEditField(
        this.id, choice, this.choices[choice]
      ));
    }

    body.appendChild(document.createElement('br'));

    body.appendChild(getOptionAddButton(
      this.id,
      2,
      this.choices.length
    ));

    return body;
  }
  getItemAnswerBody() {
    // Create body container
    let body = document.createElement('div');
    body.setAttribute('id', 'answer-' + this.id + '-body');
    body.setAttribute('class', 'form-item-body');

    // Create Select
    let select = document.createElement('select');
    select.setAttribute('id', 'answer-' + this.id);
    select.setAttribute('class', 'form-control');

    // Default Option
    let defaultOption = document.createElement('option');
    defaultOption.setAttribute('value', '');
    defaultOption.appendChild(document.createTextNode('SELECT'));
    select.appendChild(defaultOption);

    for(let choice in this.choices) {
      if(isNaN(choice)) break; /* FIXME: Wierd bug changes choice to "insert" after the last choice. */
      let option = document.createElement('option');
      option.setAttribute('value', this.choices[choice]['id']);
      option.appendChild(document.createTextNode(this.choices[choice]['value']));

      select.appendChild(option);
    }

    body.appendChild(select);

    return body;
  }
  getAnswer() {
    return document.querySelector('#answer-' + this.id).value;
  }
}
