class Form {
  constructor(container) {
    this.container = container;
    this.headerContainer = this.container.querySelector('#header-container');
    this.itemContainer = this.container.querySelector('#item-container');
    this.id;
    this.owner;
    this.items = [];
    this.title = 'Untitled Form';
    this.description = '';
    this.expires = false;
    this.expiry = 0;
  }
  drawForm() {
    this.headerContainer.appendChild(this.getFormHeader());
    for(let item of this.items) {
      this.itemContainer.appendChild(document.createElement('hr'));
      this.itemContainer.appendChild(item.getItemAnswer());
    }
  }
  getFormHeader() {
    // Create Header container
    let header = document.createElement('div');
    header.setAttribute('class', 'form-item-header');

    // Create title div
    let title = document.createElement('div');
    title.setAttribute('class', 'form-title');
    title.appendChild(document.createTextNode(this.title));
    header.appendChild(title);

    // Create description Span
    if(this.description.length > 0) {
      let description = document.createElement('div');
      description.setAttribute('class', 'form-description');
      description.appendChild(document.createTextNode(this.description));
      header.appendChild(desciption);
    }

    return header;
  }
  drawEditForm() {
    this.setFormEditHeader();
    this.drawEditItems();
  }
  setFormEditHeader() {
    // Create header div
    let self = this;
    this.headerContainer.innerHTML = "";

    // Create Title Field
    let titleField = document.createElement('input');
    titleField.setAttribute('type', 'text');
    titleField.setAttribute('class', 'form-control form-control-lg');
    titleField.setAttribute('id', 'form-title');
    titleField.setAttribute('placeholder', 'Title');
    titleField.setAttribute('value', this.title || '');
    titleField.setAttribute('maxlength', 128);
    titleField.oninput = function() {
      self.title = this.value;
    }
    this.headerContainer.appendChild(titleField);

    //header.appendChild(document.createElement('br'));

    // Create description Field
    let descriptionField = document.createElement('input');
    descriptionField.setAttribute('type', 'text');
    descriptionField.setAttribute('class', 'form-control');
    descriptionField.setAttribute('id', 'form-description');
    descriptionField.setAttribute('placeholder', 'Form description');
    descriptionField.setAttribute('value', this.description || '');
    descriptionField.setAttribute('maxlength', 512);
    descriptionField.oninput = function() {
      self.description = this.value;
    }
    this.headerContainer.appendChild(descriptionField);

    this.headerContainer.appendChild(document.createElement('hr'));

    let currentDateTime = changeTimezone(new Date(), "GMT").toISOString();

    // Create Expiry Container
    let expiryDiv = document.createElement('div');
    expiryDiv.setAttribute('class', 'd-flex flex-column flex-md-row align-items-center justify-content-md-end');

    // Switch Div
    let switchDiv = document.createElement('div');
    switchDiv.setAttribute('class', 'custom-control custom-switch mr-0 mr-md-3');

    // Create the switch
    let expirySwitch = document.createElement('input');
    expirySwitch.setAttribute('type', 'checkbox');
    expirySwitch.setAttribute('class', 'custom-control-input');
    expirySwitch.setAttribute('id', 'form-expiry-switch');
    expirySwitch.checked = this.expires;
    expirySwitch.oninput = function() {
      form.expires = !form.expires;
    }
    switchDiv.appendChild(expirySwitch);

    let expirySwitchLabel = document.createElement('label');
    expirySwitchLabel.setAttribute('class', 'custom-control-label');
    expirySwitchLabel.setAttribute('for', 'form-expiry-switch');
    expirySwitchLabel.innerHTML = "Expires";
    switchDiv.appendChild(expirySwitchLabel);

    expiryDiv.appendChild(switchDiv);

    // Create expiry input
    let expiryDate = document.createElement('input');
    expiryDate.setAttribute('type', 'date');
    expiryDate.setAttribute('id', 'form-expiry-date');
    expiryDate.setAttribute('class', 'form-control col-12 col-md-4 col-lg-3 col-xl-2');
    expiryDate.setAttribute('min', currentDateTime.substr(0, 10));
    expiryDate.setAttribute('value', currentDateTime.substr(0, 10));
    expiryDiv.appendChild(expiryDate);

    let expiryTime = document.createElement('input');
    expiryTime.setAttribute('type', 'time');
    expiryTime.setAttribute('id', 'form-expiry-time');
    expiryTime.setAttribute('class', 'form-control col-12 col-md-4 col-lg-3 col-xl-2');
    expiryTime.setAttribute('min', currentDateTime.substr(11, 5));
    expiryTime.setAttribute('value', currentDateTime.substr(11, 5));
    expiryDiv.appendChild(expiryTime);

    this.headerContainer.appendChild(expiryDiv);
  }
  drawEditItems() {
    this.itemContainer.innerHTML = '';
    for(let item of this.items) {
      this.itemContainer.appendChild(item.getItemEdit());
    }
  }
  getNewItem(itemType) {
    let itemToBeAdded = null;
    switch (itemType) {
      case 1:
      case "Paragraph":
        itemToBeAdded = new TextInput(1);
        break;

      case 2:
      case "Multiple Choice":
        itemToBeAdded = new SelectChoice(0);
        break;

      case 3:
      case "Checkboxes":
        itemToBeAdded = new SelectChoice(1);
        break;
      case 4:
      case "Dropdown":
        itemToBeAdded = new Dropdown();
        break;

      default:
        itemToBeAdded = new TextInput(0);
        break;
    }
    return itemToBeAdded;
  }
  addItem(item) {
    if(item != null) {
      this.items.push(item);
      this.itemContainer.appendChild(item.getItemEdit());
    }
  }
  findItem(itemId) {
    for(let item in this.items) {
      if(this.items[item].id == itemId) {
        return this.items[item];
      }
    }
    return false;
  }
  removeItem(itemId) {
    // Remove From Items list
    for(let item in this.items) {
      if(this.items[item].id == itemId) {
        this.items.splice(item, 1);
        let itemDiv = this.itemContainer.querySelector('#item-' + itemId);
        itemDiv.parentNode.removeChild(itemDiv);
        break;
      }
    }
  }
  redrawItem(itemId) {
    // Get item to be redrawn
    let item = this.findItem(itemId);
    if(item) {
      this.itemContainer.querySelector('#item-' + itemId).replaceWith(item.getItemEdit());
    }
  }
  addChoiceToItem(itemId) {
    let item = this.findItem(itemId);
    if(item) {
      item.addChoice();
      this.redrawItem(itemId);
    }
  }
  addOtherChoiceToItem(itemId) {
    let item = this.findItem(itemId);
    if(item) {
      item.addOtherChoice();
      this.redrawItem(itemId);
    }
  }
  removeChoiceFromItem(itemId, choiceId) {
    let item = this.findItem(itemId);
    if(item) {
      item.removeChoice(choiceId);
      this.redrawItem(itemId);
    }
  }
  toggleItemDataValidation(itemId) {
    let item = this.findItem(itemId);
    if(item) {
      item.extras['Data Validation'] = !item.extras['Data Validation'];
      this.redrawItem(itemId);
    }
  }
  toggleIsRequired(itemId) {
    let item = this.findItem(itemId);
    if(item) {
      item.isRequired = !item.isRequired;
    }
  }
  getItemClone(item, newItemType = null) {
    if(item) {
      if(newItemType == null) {
        newItemType = item.type;
      }
      let newItem = this.getNewItem(newItemType);
      if(newItemType < 2) {
        newItem.cloneFromItem(
          item.type,
          item.question,
          item.description,
          item.isRequired,
          item.extras,
          item.validator
        );
      } else {
        newItem.cloneFromItem(
          item.type,
          item.question,
          item.description,
          item.isRequired,
          item.extras,
          item.hasOther,
          item.choices
        );
      }
      return newItem;
    }
    return null;
  }
  copyItem(itemId) {
    for(let item in this.items) {
      if(this.items[item].id == itemId) {
        let newItem = this.getItemClone(this.items[item]);
        this.items.insert(item + 1, newItem);
        let itemNode = this.itemContainer.querySelector('#item-' + this.items[item].id);
        itemNode.parentNode.insertBefore(newItem.getItemEdit(), itemNode.nextSibling);
        break;
      }
    }
  }
  changeItemType(itemId, newItemType) {
    for(let item in this.items) {
      if(this.items[item].id == itemId) {
        if(this.items[item].type != newItemType) {
          let newItem = this.getItemClone(this.items[item], newItemType);
          this.items.splice(item, 1);
          this.items.insert(item, newItem);
          this.itemContainer.querySelector('#item-' + itemId).replaceWith(newItem.getItemEdit());
        }
      }
    }
  }
  getFormData() {
    let itemData = [];
    for(let item of this.items) {
      itemData.push(item.getItemData());
    }
    let formData = {
      'owner': this.owner,
      'meta': {
        'title': this.title || 'Untitled Form',
        'description': this.desciption || '',
        'expires': this.expires,
        'expiry': {
          'datetime': this.headerContainer.querySelector('#form-expiry-date').value + ' ' + this.headerContainer.querySelector('#form-expiry-time').value,
          'timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
        }
      },
      'items': itemData
    }
    return formData;
  }
  constructForm(formData) {
    this.owner = formData['owner'];
    for(let [key, value] of Object.entries(formData['meta'])) {
      this[key] = value;
    }
    for(let item of formData['items']) {
      let newItem = this.getNewItem(item['type']);
      newItem.constructItem(item);
      this.items.push(newItem);
    }
  }
  getAnswer() {
    let answer = {
      'user': this.owner,
      'form': this.id,
      'answers': []
    }
    for(let item of this.items) {
      answer['answers'].push({
        'question_id': item.id,
        'type': item.type,
        'answer': item.getAnswer()
      });
    }
    return answer;
  }
}

let addItem = function(itemType) {
  form.addItem(form.getNewItem(itemType));
}
