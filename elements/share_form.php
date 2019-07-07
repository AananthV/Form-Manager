<!-- Button trigger modal -->
<button type="button" class="btn btn-primary d-none" id="share-modal-toggle" data-toggle="modal" data-target="#shareModal"></button>

<!-- Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shareModalTitle">Share</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <input type="text" id="share-link" class="form-control" placeholder="Link" value="Test" aria-label="Share Link" readonly>
          <div class="input-group-append">
            <button type="button" id="share-link-copy" class="btn btn-outline-secondary" onclick="copy_share_link()">Copy</button>
          </div>
        </div>
        <a class="btn btn-outline-primary" id="facebook-share-link" target="_blank">
          <i class="fab fa-facebook-f fa-fw"></i> Share
        </a>
        <a class="btn btn-outline-primary" id="twitter-share-link" target="_blank">
          <i class="fab fa-twitter fa-fw"></i> Tweet
        </a>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    let share_form = function(form_id) {
      document.querySelector('#share-link').value = "<?php echo DOMAIN; ?>answer.php?id=" + form_id;
      document.querySelector('#facebook-share-link').setAttribute('href', "https://www.facebook.com/sharer/sharer.php?u=" + "<?php echo DOMAIN; ?>answer.php?id=" + form_id);
      document.querySelector('#twitter-share-link').setAttribute('href', "https://twitter.com/home?status=" + "Check this form out! <?php echo DOMAIN; ?>answer.php?id=" + form_id);
      document.querySelector('#share-modal-toggle').click();
    }
    let copy_share_link = function() {
      document.querySelector('#share-link').select();
      document.execCommand('copy');
      document.querySelector('#share-link-copy').innerHTML = 'Copied!';
    }
</script>
