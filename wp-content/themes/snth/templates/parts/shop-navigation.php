<?php
if ($categories = SNTH_Woo::get_categories()) {
    $i = 1
    ?>
    <nav class="shop-nav">
        <ul class="text-uppercase">
            <?php
            foreach ($categories as $category) {
                ?>
                <li class="<?= 1 === $i ? ' active' : '' ?>" data-category="<?= $category->term_id ?>">
                    <?php //var_dump($category) ?>
                    <?= $category->name ?>
                </li>
                <?php
                ++$i;
            }
            ?>
        </ul>
    </nav>
    <?php
}
?>