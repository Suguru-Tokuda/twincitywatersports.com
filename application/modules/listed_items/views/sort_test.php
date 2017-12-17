<?php
  if ($num_rows > 0) {
 ?>

<ul id="sortlist" style="list-style: none">
  <?php
  foreach($query->result() as $row) {
    $image_location = base_url()."/small_pics/".$row->picture_name;
    ?>
    <li class="sort"><img src="<?= $image_location ?>" title="pic1"></li>
    <?php
  }
  ?>
</ui>

<?php
}
 ?>
 <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
 <script>
 $(function() {
   $("#file").change(function() {
     var reader = new FileReader();
     reader.onload = function(image) {
       $('.imageUploadedOrNot').show(0);
       $('#blankImg').attr('src', image.target.result);
     }
     reader.readAsDataURL(this.files[0]); // this refers to $('#file')
   });
 });
 </script>
