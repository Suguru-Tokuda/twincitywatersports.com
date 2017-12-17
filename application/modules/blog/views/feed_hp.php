<h1>News</h1>
<?php
$this->load->module('timedate');
foreach($query->result() as $row) {
  $article_preview = word_limiter($row->blog_content, 25); // shows only the first 25 letters
  $picture =$row->picture;
  $thumbnail_pic_path = base_url().'blog_pics/'.str_replace('.', '_thumb.', $picture);
  $date_published = $this->timedate->get_date($row->date_published, 'datepicker_us');
  $blog_url = base_url().'blog/article/'.$row->blog_url;
  ?>
  <div class="row" style="margin-bottom: 12px">
    <div class="col-md-3" >
      <img src="<?= $thumbnail_pic_path ?>" class="img-responsive img-thumbnail" >
    </div>
    <div class="col-md-9" >
      <h4><a href="<?= $blog_url?>"><?= $row->blog_title ?></a></h4>
      <p style="font-size: 0.8em;">
        <?= $row->author ?> -
        <span style="color: #999"><?= $date_published ?></span>
      </p>
      <p><?= $article_preview ?></h4>
      </div>
    </div>
    <?php
  }
  ?>
