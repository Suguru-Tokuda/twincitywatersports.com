<style>
.map-responsive {
  overflow:hidden;
  padding-bottom:56.25%;
  position:relative;
  height:0;
}
.map-responsive iframe {
  left:0;
  top:0;
  height:100%;
  width:100%;
  position:absolute;
}

</style>
<!-- <div class="container" style="margin-bottom: 50px;"> -->
  <div class="row" style="margin-bottom: 50px;">
    <div class="col-md-12">
      <h1>Contact Us</h1>
      <div class="container">
        <div class="row">

          <div class="col-md-8">
            <div class="well well-sm">
              <?php
              echo validation_errors("<p style='color: red;'>", "</p>");
              ?>
              <form action="<?= $form_location ?>" method="post">
                <div class="row">
                  <div class="col-md-6">

                    <div class="form-group">
                      <label for="name">
                        Name</label>
                        <input type="text" name="yourname" value="<?= $yourname ?>" class="form-control" id="name" placeholder="Enter name" required="required" />
                      </div>

                      <div class="form-group">
                        <label for="email">
                          Email Address</label>
                          <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                          </span>
                          <input type="email" name="email" value="<?= $email ?>" class="form-control" id="email" placeholder="Enter email" required="required" /></div>
                        </div>

                        <div class="form-group">
                          <label for="phone">
                            Phone number</label>
                            <div class="input-group">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span>
                            </span>
                            <input type="text" name="phone" value="<?= $phone ?>" class="form-control" id="phone" placeholder="Enter phone number" required="required" /></div>
                          </div>

                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="name">
                              Message</label>
                              <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required"
                              placeholder="Message"></textarea>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <button type="submit" name="submit" value="submit" class="btn btn-primary pull-right" id="btnContactUs">
                              Send Message</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <form>
                        <legend><span class="glyphicon glyphicon-globe"></span> Our office</legend>
                        <address>
                          <strong><?= $our_company ?></strong><br>
                          <?= $our_address ?>
                          <abbr title="Phone">
                          </address>
                          <address>
                            <strong>Phone</strong><br>
                            <?= $our_phone ?>
                          </address>
                        </form>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="map-responsive">
                          <?= $map_code ?>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            <!-- </div> -->
