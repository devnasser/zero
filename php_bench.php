<?php
function is_prime($n) {
    if ($n < 2) return false;
    if ($n % 2 == 0) return $n == 2;
    $r = (int)sqrt($n) + 1;
    for ($i = 3; $i < $r; $i += 2) {
        if ($n % $i == 0) return false;
    }
    return true;
}
function count_primes($limit) {
    $sum = 0;
    for ($i = 0; $i < $limit; $i++) {
        if (is_prime($i)) $sum++;
    }
    return $sum;
}
$LIMIT = 100000;
$ITER = 10;
$start = microtime(true);
for ($j=0;$j<$ITER;$j++) {
    count_primes($LIMIT);
}
$end = microtime(true);
$elapsed = $end - $start;
printf("%.6f\n", $elapsed);
?>