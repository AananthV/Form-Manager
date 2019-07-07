<div id="notification-list"></div>

<script type="text/javascript">
  let addNotification = function(notification) {
    // Get notification container.
    let notifDiv = document.createElement('div');
    notifDiv.setAttribute('class', 'card notification');
    notifDiv.setAttribute('id', 'notif-' + notification['id']);

    // Header
    let notifHeader = document.createElement('div');
    notifHeader.setAttribute('class', 'card-header d-flex align-items-center justify-content-between');
    notifHeader.innerHTML = '<strong>Notification</strong><button type="button" class="btn p-0" onclick="removeNotification(' + notification['id'] + ')"><i class="fas fa-times fa-lg"></i></button>';
    notifDiv.appendChild(notifHeader);

    // Body
    let notifBody = document.createElement('div');
    notifBody.setAttribute('class', 'card-body');

    let notifBodyTitle = document.createElement('h5');
    notifBodyTitle.setAttribute('class', 'card-title');
    notifBodyTitle.innerHTML = notification['title'];
    notifBody.appendChild(notifBodyTitle);

    notifBodyText = document.createElement('p');
    notifBodyText.setAttribute('class', 'card-text');
    notifBodyText.innerHTML = notification['text'];
    notifBody.appendChild(notifBodyText);

    notifButton = document.createElement('button');
    notifButton.setAttribute('type', 'button');
    notifButton.setAttribute('class', 'btn btn-primary');
    notifButton.onclick = notification['button'].onclick;
    notifButton.innerHTML = notification['button'].text;
    notifBody.appendChild(notifButton);

    notifDiv.appendChild(notifBody);

    let notifList = document.querySelector('#notification-list');
    if((window.innerWidth < 576 || notifList.offsetHeight > window.innerHeight) && notifList.childElementCount > 0) {
      notifList.removeChild(notifList.childNodes[0]);
    }
    document.querySelector('#notification-list').appendChild(notifDiv);
  }

  let removeNotification = function(id) {
    document.querySelector('#notification-list').removeChild(document.querySelector('#notif-' + id));
  }

  let generateNotification = function(notification_data) {
    notification_data['type'] = notification_data['type'].toString();
    if(notification_data['type'].substr(0, 1) == 1) {
      notification_data['button'] = {
        'onclick': function() {
          window.location = "<?php echo DOMAIN; ?>view_responses.php?id=" + notification_data['type'].substr(1) + "&individual_responses=true&sort=DESC";
        },
        'text': 'View Responses'
      };
    }
    addNotification(notification_data);
  }

  let getNotification = function() {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        if(this.responseText.substr(0, 7) == 'SUCCESS') {
          generateNotification(JSON.parse(this.responseText.substr(9)));
        } else {
          //console.log(this.responseText);
        }
      }
    };
    xhttp.open("GET", "<?php echo $ROOT_PATH . '/get_notifications.php';?>", true);
    xhttp.send();
  }

  // Check for notifications every 20s.
  setInterval(getNotification, 20000);
</script>
