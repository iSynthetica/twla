<div class="text-center chars-list">
    <?php
    foreach (range('a', 'z') as $char) {
        $class = 'a' == $char ? ' active' : '';
        echo '<span class="char' .$class. ' text-uppercase">' . $char . '</span>';
        if ('z' != $char) {
            echo '<span class="char-divider' .$class. '">&#8226;</span>';
        }
        echo "\n";
    }
    ?>
</div>