<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Carousel indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
  </ol>
  <!-- Wrapper for carousel items -->
  <div class="carousel-inner">
    <div class="item active">
      <img src=<?=base_url()."/img/surf-lessons-kona-hawaii.jpg"?> alt="First Slide">
    </div>
    <div class="item">
      <img src=<?=base_url()."/img/ocean-7.jpg"?> alt="Second Slide">
    </div>
    <div class="item">
      <img src=<?=base_url()."/img/background.jpg"?> alt="Third Slide">
    </div>
  </div>
  <!-- Carousel controls -->
  <a class="carousel-control left" href="#myCarousel" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="carousel-control right" href="#myCarousel" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>
