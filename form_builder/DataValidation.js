class DataValidation {
  constructor(itemId, type = 'number', subtype = 'isNumber', left = '', right = '') {
    this.types = {
      'number': {
        'name': 'Number',
        'subtypes': {
          'isNumber': {
            'name': 'Is Number',
            'fields': 0
          },
          'integer': {
            'name': 'Integer',
            'fields': 0
          },
          'whole_number': {
            'name': 'Whole Number',
            'fields': 0
          },
          'equals': {
            'name': 'Equals',
            'fields': 1
          },
          'not_equals': {
            'name': 'Not Equals',
            'fields': 1
          },
          'greater_than': {
            'name': 'Greater Than',
            'fields': 1
          },
          'greater_than_equal_to': {
            'name': 'Greater Than Equal To',
            'fields': 1
          },
          'less_than': {
            'name': 'Less Than',
            'fields': 1
          },
          'less_than_equal_to': {
            'name': 'Less Than Equal To',
            'fields': 1
          },
          'between': {
            'name': 'Between (Exclusive)',
            'fields': 2
          },
          'not_between': {
            'name': 'Not Between (Exclusive)',
            'fields': 2
          },
          'between_equals': {
            'name': 'Between (Inclusive)',
            'fields': 2
          },
          'not_between_equals': {
            'name': 'Not Between (Inclusive)',
            'fields': 2
          }
        },
        'default_subtype': 'isNumber'
      },
      'string': {
        'name': 'String',
        'subtypes': {
          'min_length': {
            'name': 'Minimum Length',
            'fields': 1
          },
          'max_length': {
            'name': 'Maximum Length',
            'fields': 1
          },
          'email': {
            'name': 'Email',
            'fields': 0
          },
          'url': {
            'name': 'URL',
            'fields': 0
          }
        },
        'default_subtype': 'min_length'
      }
    }
    this.type = type;
    this.subtype = subtype;
    this.itemId = itemId;
    this.textFields = [left, right];
  }
  getTypeDropdown() {
    //Create Enclosing Div
    let dropdown = document.createElement('div');
    dropdown.setAttribute('class', 'dropdown col-sm-3');

    // Create Button
    let dropdownButton = document.createElement('button');
    dropdownButton.setAttribute('class', 'btn btn-outline-secondary dropdown-toggle col');
    dropdownButton.setAttribute('id', 'item-' + this.itemId + '-validation-type-dropdown');
    dropdownButton.setAttribute('type', 'button');
    dropdownButton.setAttribute('data-toggle', 'dropdown');
    dropdownButton.setAttribute('aria-haspopup', 'true');
    dropdownButton.setAttribute('aria-expanded', 'false');
    dropdownButton.innerHTML = this.types[this.type]['name'];
    dropdown.appendChild(dropdownButton);

    // Create Menu Div
    let dropdownMenu = document.createElement('div');
    dropdownMenu.setAttribute('class', 'dropdown-menu item-type-dropdown');

    let self = this;

    // Create Dropdown Menu
    for(let [key, value] of Object.entries(this.types)) {
      let dropdownItem = document.createElement('button');
      dropdownItem.setAttribute('type', 'button');
      dropdownItem.setAttribute('class', 'dropdown-item');
      dropdownItem.onclick = function() {
        if(key != self.type) {
          self.type = key;
          self.subtype = value['default_subtype'];
          self.textFields = ['', ''];
          form.redrawItem(self.itemId);
        }
      }

      let itemText = document.createTextNode(value['name']);
      dropdownItem.appendChild(itemText);

      dropdownMenu.appendChild(dropdownItem);
    }

    dropdown.appendChild(dropdownMenu);

    return dropdown;
  }
  getSubtypeDropdown() {
    //Create Enclosing Div
    let dropdown = document.createElement('div');
    dropdown.setAttribute('class', 'dropdown col-sm-3');

    // Create Button
    let dropdownButton = document.createElement('button');
    dropdownButton.setAttribute('class', 'btn btn-outline-secondary dropdown-toggle col');
    dropdownButton.setAttribute('id', 'item-' + this.itemId + '-validation-subtype-dropdown');
    dropdownButton.setAttribute('type', 'button');
    dropdownButton.setAttribute('data-toggle', 'dropdown');
    dropdownButton.setAttribute('aria-haspopup', 'true');
    dropdownButton.setAttribute('aria-expanded', 'false');
    dropdownButton.innerHTML = this.types[this.type]['subtypes'][this.subtype]['name'];
    dropdown.appendChild(dropdownButton);

    // Create Menu Div
    let dropdownMenu = document.createElement('div');
    dropdownMenu.setAttribute('class', 'dropdown-menu item-type-dropdown');

    let self = this;

    // Create Dropdown Menu
    for(let [key, value] of Object.entries(this.types[this.type]['subtypes'])) {
      let dropdownItem = document.createElement('button');
      dropdownItem.setAttribute('type', 'button');
      dropdownItem.setAttribute('class', 'dropdown-item');
      dropdownItem.onclick = function() {
        if(key != self.subtype) {
          self.subtype = key;
          form.redrawItem(self.itemId);
        }
      }

      let itemText = document.createTextNode(value['name']);
      dropdownItem.appendChild(itemText);

      dropdownMenu.appendChild(dropdownItem);
    }

    dropdown.appendChild(dropdownMenu);

    return dropdown;
  }
  getTextFields() {
    // Get Container;
    let tfContainer = document.createElement('div');
    tfContainer.setAttribute('class', 'row m-auto p-0 col-sm-6 col-xs-12 d-flex justify-content-around');

    let self = this;
    // Get Text Fields;
    if(this.types[this.type]['subtypes'][this.subtype]['fields'] == 1) {
      let tf1 = document.createElement('input');
      tf1.setAttribute('type', 'number');
      tf1.setAttribute('class', 'form-control col');
      tf1.setAttribute('placeholder', 'Number');
      tf1.setAttribute('value', this.textFields[0]);
      tf1.setAttribute('required', 'I guess.');
      tf1.oninput = function() {
        self.textFields[0] = this.value;
      }
      tfContainer.appendChild(tf1);
    } else if(this.types[this.type]['subtypes'][this.subtype]['fields'] == 2) {
      let tf1 = document.createElement('input');
      tf1.setAttribute('type', 'number');
      tf1.setAttribute('class', 'form-control col-5');
      tf1.setAttribute('placeholder', 'Number');
      tf1.setAttribute('value', this.textFields[0]);
      tf1.setAttribute('required', 'I guess.');
      tf1.oninput = function() {
        self.textFields[0] = this.value;
      }
      tfContainer.appendChild(tf1);

      let andNode = document.createElement('div');
      andNode.innerHTML = 'and';
      andNode.setAttribute('class', 'col-2 p-0 d-flex align-items-center justify-content-center');
      tfContainer.appendChild(andNode);

      let tf2 = document.createElement('input');
      tf2.setAttribute('type', 'number');
      tf2.setAttribute('class', 'form-control col-5');
      tf2.setAttribute('placeholder', 'Number');
      tf2.setAttribute('value', this.textFields[1]);
      tf2.setAttribute('required', 'I guess.');
      tf2.oninput = function() {
        self.textFields[1] = this.value;
      }
      tfContainer.appendChild(tf2);
    }

    return tfContainer;
  }
  getDataValidationEdit() {
    let container = document.createElement('div');
    container.setAttribute('class', 'd-flex flex-col')

    // Validation Container
    let vcontainer = document.createElement('div');
    vcontainer.setAttribute('class', 'col row m-0 p-0');
    vcontainer.appendChild(this.getTypeDropdown());
    vcontainer.appendChild(this.getSubtypeDropdown());
    vcontainer.appendChild(this.getTextFields());
    container.appendChild(vcontainer);

    // Get Remove Button;
    let removeButton = document.createElement('button');
    removeButton.setAttribute('class', 'btn');
    removeButton.setAttribute('type', 'button');
    removeButton.setAttribute('id', 'remove-datavalidation-' + this.itemId);
    removeButton.innerHTML = '<i class="fas fa-times fa-lg fa-fw"></i>';
    let self = this;
    removeButton.onclick = function() {
      form.toggleItemDataValidation(self.itemId);
    }
    container.appendChild(removeButton);

    return container;
  }
  getDataValidation() {
    return {
      'type': this.type,
      'subtype': this.subtype,
      'left': this.textFields[0],
      'right': this.textFields[1]
    }
  }
}
