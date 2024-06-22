<?php

$servername = "localhost";
$username = "admin";
$password = "password";
$dbname = "sys";
$port = "3306";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

function countProductsInGroup($groupId, $conn)
{
    $allProducts = 0;
    $sql = "SELECT COUNT(*) as total FROM products WHERE id_group = $groupId";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $allProducts += $row['total'];

    $sql = "SELECT id FROM `groups` WHERE id_parent = $groupId";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $allProducts += countProductsInGroup($row['id'], $conn);
    }
    return $allProducts;
}
function printGroup($parentId, $conn)
{
    $sql = "SELECT id, name FROM `groups` WHERE id_parent = $parentId";
    $result = $conn->query($sql);
    foreach ($result as $item) {
        $all = countProductsInGroup($item['id'], $conn);
        echo "<li><a href='?group=" . $item['id'] . "'>" . $item['name'] . " ($all)</a></li>";
    }
}

function printProducts($groupId, $conn)
{
    $sql = "SELECT name FROM products WHERE id_group =" . $groupId;
    $arrs = $conn->query($sql);

    foreach ($arrs as $arr) {
        echo "<li>" . $arr['name'] . "</li>";
    }
}

$selectedGroup = isset($_GET['group']) ? $_GET['group'] : 0;

printGroup($selectedGroup, $conn);
printProducts($selectedGroup, $conn);

$conn->close();