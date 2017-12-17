<div style="background-color: #ddd; border-radius: 7px; margin-top: 24px; padding: 7px;">
  <table class="table">
    <tr>
      <td colspan="2">Item ID: <?= $item_id ?></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="2">Username: <?= $userName ?></td>
      <td>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center;">
        <a class="btn btn-primary" href="mailto:<?= $email ?>"><span class="glyphicon glyphicon-envelope" aria-hiddne="true"></span> Email the seller</a>
        <!-- <button class="btn btn-primary" type="submit" name="submit" value="<?= $email ?>"><span class="glyphicon glyphicon-envelope" aria-hiddne="true"></span> Email the seller</button> -->
      </td>
      <td></td>
    </tr>
  </table>
</div>
