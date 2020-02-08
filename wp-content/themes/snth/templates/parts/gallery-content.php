<div class="row">
    <div class="medium-4 columns">
        <ul class="contact-list-wrapper">
            <li class="address-wrapper">
                <?= get_theme_mod('snth_address1_text') ?><br>
                <?= get_theme_mod('snth_address2_text') ?>
            </li>
            <li class="email-wrapper"><?= get_theme_mod('snth_email_text') ?></li>
            <li class="phone-wrapper"><?= get_theme_mod('snth_phone_text') ?></li>
        </ul>
    </div>
    <div class="medium-8 columns">
        <div class="acf-map">

        </div>
    </div>
</div>


<div class="row column section-content">
    <?php
    SNTH_Lib::get_template('templates/parts/gallery', 'carousel', array('id' => null, 'last' => true));
    ?>
</div>