// Insert At Point
Array.prototype.insert = function ( index, item ) {
    this.splice( index, 0, item );
};

// Define all item types
let itemTypes = {
  0: {
    "itemName": "Short Answer",
    "itemIcon": "fas fa-font fa-fw"
  },
  1: {
    "itemName": "Paragraph",
    "itemIcon": "fas fa-align-left fa-fw"
  },
  2: {
    "itemName": "Multiple Choice",
    "itemIcon": "far fa-dot-circle fa-fw"
  },
  3: {
    "itemName": "Checkboxes",
    "itemIcon": "far fa-square fa-fw"
  },
  4: {
    "itemName": "Dropdown",
    "itemIcon": "fas fa-chevron-circle-down fa-fw"
  }
}

/*
* Gets the dropdown (requires bootstrap, popper.js, and fontawesome) to select the itemType
* in the item editor.
*/
function getItemTypeDropdown(itemType, itemId) {

  //Create Enclosing Div
  let dropdown = document.createElement('div');
  dropdown.setAttribute('class', 'dropdown col-md-4');

  // Create Button
  let dropdownButton = document.createElement('button');
  dropdownButton.setAttribute('class', 'btn btn-outline-secondary dropdown-toggle col');
  dropdownButton.setAttribute('type', 'button');
  dropdownButton.setAttribute('data-toggle', 'dropdown');
  dropdownButton.setAttribute('aria-haspopup', 'true');
  dropdownButton.setAttribute('aria-expanded', 'false');
  dropdownButton.innerHTML = itemTypes[itemType]['itemName'];
  dropdown.appendChild(dropdownButton);

  // Create Menu Div
  let dropdownMenu = document.createElement('div');
  dropdownMenu.setAttribute('class', 'dropdown-menu dropdown-menu-right item-type-dropdown');

  // Create Dropdown Menu
  for(let item = 0; item < 5; item++) {
    let dropdownItem = document.createElement('button');
    dropdownItem.setAttribute('type', 'button');
    dropdownItem.setAttribute('class', 'dropdown-item');
    dropdownItem.setAttribute('id', 'itemType-' + item + '-' + itemId)

    dropdownItem.onclick = function() {
      form.changeItemType(itemId, item);
    }

    let itemIcon = document.createElement('i');
    itemIcon.setAttribute('class', itemTypes[item]['itemIcon']);
    dropdownItem.appendChild(itemIcon);

    let itemText = document.createTextNode(itemTypes[item]['itemName']);
    dropdownItem.appendChild(itemText);

    dropdownMenu.appendChild(dropdownItem);
  }

  dropdown.appendChild(dropdownMenu);

  return dropdown;
}

function getItemRequiredSwitch(itemId, isRequired) {
  // Create Required Switch Div
  let switchDiv = document.createElement('div');
  switchDiv.setAttribute('class', 'custom-control custom-switch');

  // Create the switch
  let requiredSwitch = document.createElement('input');
  requiredSwitch.setAttribute('type', 'checkbox');
  requiredSwitch.setAttribute('class', 'custom-control-input');
  requiredSwitch.setAttribute('id', 'required-switch-' + itemId);
  requiredSwitch.checked = isRequired;
  requiredSwitch.oninput = function() {
    form.toggleIsRequired(itemId);
  }
  switchDiv.appendChild(requiredSwitch);

  requiredSwitchLabel = document.createElement('label');
  requiredSwitchLabel.setAttribute('class', 'custom-control-label');
  requiredSwitchLabel.setAttribute('for', 'required-switch-' + itemId);
  requiredSwitchLabel.innerHTML = "Required"
  switchDiv.appendChild(requiredSwitchLabel);

  return switchDiv;
}

function getFooterButtons(itemId) {
  // Create Duplicate Button
  let duplicateButton = document.createElement('button');
  duplicateButton.setAttribute('class', 'btn');
  duplicateButton.setAttribute('type', 'button');
  duplicateButton.setAttribute('title', 'Duplicate');
  duplicateButton.setAttribute('id', 'duplicate-button-' + itemId);
  duplicateButton.innerHTML = '<i class="far fa-copy"></i>';
  duplicateButton.onclick = function() {
    form.copyItem(itemId);
  }

  // Create remove button
  let removeButton = document.createElement('button');
  removeButton.setAttribute('class', 'btn');
  removeButton.setAttribute('type', 'button');
  removeButton.setAttribute('title', 'Remove');
  removeButton.setAttribute('id', 'remove-button-' + itemId);
  removeButton.innerHTML = '<i class="fas fa-trash"></i>';
  removeButton.onclick = function() {
    form.removeItem(itemId);
  }

  return [duplicateButton, removeButton];
}

function getFooterMenuDropup(itemId, itemExtras) {
  // Create dropup div
  let dropup = document.createElement('div');
  dropup.setAttribute('class', 'dropup');

  // Create button
  let dropupButton = document.createElement('button');
  dropupButton.setAttribute('id', 'footer-menu-' + itemId);
  dropupButton.setAttribute('class', 'btn');
  dropupButton.setAttribute('type', 'button');
  dropupButton.setAttribute('data-toggle', 'dropdown');
  dropupButton.setAttribute('aria-haspopup', 'true');
  dropupButton.setAttribute('aria-expanded', 'false');
  dropupButton.innerHTML = '<i class="fas fa-ellipsis-v"></i>';

  dropup.appendChild(dropupButton);

  // Create dropup
  let dropupDiv = document.createElement('div');
  dropupDiv.setAttribute('class', 'dropdown-menu dropdown-menu-right');
  dropupDiv.setAttribute('aria-labelledby', 'footer-menu-' + itemId);

  for(let [key, value] of Object.entries(itemExtras)) {
    menuButton = document.createElement('button');
    menuButton.setAttribute('type', 'button');
    menuButton.setAttribute('class', 'dropdown-item');
    menuButton.setAttribute('id', 'footer-' + key + '-button-' + itemId);
    menuButton.onclick = function() {
      itemExtras[key] = !itemExtras[key];
      form.redrawItem(itemId);
    }
    menuButton.innerHTML = (value?'<i class="fas fa-check fa-fw"></i>':'<i class="fa fa-fw"></i>') + " " + key;
    dropupDiv.appendChild(menuButton);
  }

  dropup.appendChild(dropupDiv);

  return dropup;
}

function getOptionEditField(itemId, optionId, option, optionIconClass = null) {
  // Create container div
  let container = document.createElement('div');
  container.setAttribute('class', 'd-flex justify-content-around');

  // Create Icon
  let iconButton = document.createElement('button');
  iconButton.setAttribute('class', 'btn');
  iconButton.setAttribute('type', 'button');
  iconButton.setAttribute('disabled', 'I guess');
  iconButton.innerHTML = optionIconClass || "<i class='fa fa-fw'>" + (parseInt(optionId) + 1) + ".</i>";
  container.appendChild(iconButton);

  // Option Input Field
  let inputField = document.createElement('input');
  inputField.setAttribute('type', 'text');
  inputField.setAttribute('maxlength', '128');
  inputField.setAttribute('class', 'form-control col-lg-10');
  inputField.setAttribute('id', 'input-option-' + itemId + '-' + option['id']);
  if(option['isOther']) {
    inputField.setAttribute('placeholder', 'Other');
    inputField.setAttribute('disabled', 'I guess');
  } else {
    inputField.setAttribute('placeholder', 'Option ' + (parseInt(optionId) + 1));
  }
  inputField.setAttribute('value', option['value']);
  inputField.oninput = function() {
    option['value'] = this.value;
  }
  container.appendChild(inputField);

  // Remove Button
  let removeButton = document.createElement('button');
  removeButton.setAttribute('class', 'btn');
  removeButton.setAttribute('type', 'button');
  removeButton.setAttribute('id', 'remove-option-' + itemId + '-' + option['id']);
  removeButton.innerHTML = '<i class="fas fa-times fa-lg fa-fw"></i>';
  removeButton.onclick = function() {
    form.removeChoiceFromItem(itemId, option['id']);
  }
  container.appendChild(removeButton);

  return container;
}

function getOptionAddButton(itemId, choiceType, numChoices, hasOther = false, choiceIcon = null) {
  // Create enclosing div
  let container = document.createElement('div');

  // Create add Choice button
  let addChoice = document.createElement('button');
  addChoice.setAttribute('type', 'button');
  addChoice.setAttribute('class', 'btn btn-outline-secondary');
  addChoice.setAttribute('id', 'add-choice-' + itemId);
  addChoice.onclick = function() {
    form.addChoiceToItem(itemId);
  }
  addChoice.innerHTML = (choiceIcon || "<i class='fa fa-fw'>" + (parseInt(numChoices) + 1) + ".</i>") + " Add Option";
  container.appendChild(addChoice);

  // Create add "OTHER" button
  if(choiceType != 2 && hasOther == false) { // Dropdown doesnt Have "OTHER" field
    container.appendChild(document.createTextNode(' or '));

    let addOther = document.createElement('button');
    addOther.setAttribute('type', 'button');
    addOther.setAttribute('class', 'btn btn-outline-secondary');
    addOther.setAttribute('id', 'add-other-' + itemId);
    addOther.onclick = function() {
      form.addOtherChoiceToItem(itemId);
    }
    addOther.innerHTML = 'ADD "OTHER"';
    container.appendChild(addOther);
  }

  return container;
}
