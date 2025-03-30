<?php
declare(strict_types = 1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
// \Otus\Diag\FileExceptionHandlerLogCustom::print();

function division(float $a, float $b): int {
    return $a/$b;
}

division(5, 7);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");