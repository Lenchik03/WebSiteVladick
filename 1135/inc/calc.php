<?php include_once './template/header.php'?>
<form action="" method="post" class="calculate-form">
    <input type="text" name="number1" class="numbers" placeholder="Первое число">
    <select class="operations" name="operation">
        <option value='plus'>+ </option>
        <option value='minus'>- </option>
        <option value="multiply">* </option>
        <option value="divide">/ </option>
    </select>
    <input type="text" name="number2" class="numbers" placeholder="Второе число">

    <input class="submit_form" type="submit" name="submit" value="Получить ответ">
</form>

<?php
include_once 'function.php';
echo calculator('number1', 'number2', 'operation');

