<?php
/*
* Output for reports page
*/

function cd_reports_page(){
  ?>
  <div class="wrap">
    <h2>Client Dash - Reports</h2>
    <?php
    cd_create_tab_page(array(
      'tabs' => array(
        'Site Overview' => 'site',
        //'Analytics' => 'analytics',
        //'SEO' => 'seo',
        //'Ecommerce' => 'ecommerce'
      )
    ));
    ?>
  </div><!--.wrap-->
  <?php
}
?>