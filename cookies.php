<?php

setcookie('🍪', '👌', time() + 5 * 60);
echo '<pre>';
print_r($_COOKIE);
echo '</pre>';
