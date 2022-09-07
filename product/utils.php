<?php
function versions($productName, $memory)
{
    http_response_code(503);
    $cache = $memory->get("jomcraftapi.$productName.versions");

    if ($cache) {
        http_response_code(200);
        echo $cache;
        return;
    }
    $conn = mysqli_connect("localhost", "JomcraftAPI", ini_get("mysqli.default_pw"), "JomcraftAPI");
    $stmt = mysqli_prepare($conn, "SELECT * FROM Product_Info WHERE NAME = ?;");
    mysqli_stmt_bind_param($stmt, "s", $productName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $forge = json_decode($product['FORGE']);
    $fabric = json_decode($product['FABRIC']);
    $quilt = json_decode($product['QUILT']);

    mysqli_free_result($result);
    mysqli_close($conn);

    $arr = array('product' => $productName, 'versions' => array(
        "forge" => $forge,
        "fabric" => $fabric,
        "quilt" => $quilt
    ));

    $returnJSON = json_encode($arr);

    $memory->set("jomcraftapi.$productName.versions", $returnJSON, time() + 7200);

    http_response_code(200);

    echo $returnJSON;
    return;
}

function latest($requestString, $productName, $memory)
{
    $request = strtoupper($requestString);
    $cache = $memory->get("jomcraftapi.$productName.latest.$request");

    if ($cache) {
        http_response_code(200);
        echo $cache;
        return;
    } else if ($request == "FORGE" || $request == "FABRIC" || $request == "QUILT") {
        $conn = mysqli_connect("localhost", "JomcraftAPI", ini_get("mysqli.default_pw"), "JomcraftAPI");
        $stmt = mysqli_prepare($conn, "SELECT * FROM Product_Info WHERE NAME = ?;");
        mysqli_stmt_bind_param($stmt, "s", $productName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($product[$request]) {
            try {
                $version = json_decode($product[$request]);
                $arr = array('version' => $version->latest);
                $returnJSON = json_encode($arr);
                $memory->set("jomcraftapi.$productName.latest.$request", $returnJSON, time() + 7200);

                http_response_code(200);
                echo $returnJSON;
                mysqli_free_result($result);
                mysqli_close($conn);
                return;
            } catch (Exception $e) {
                http_response_code(503);
                mysqli_free_result($result);
                mysqli_close($conn);
                return;
            }
        }
        http_response_code(503);
        mysqli_free_result($result);
        mysqli_close($conn);
        return;
    }
    return;
}

function endpoint($requestString, $productName, $memory)
{
    $request = strtoupper($requestString);
    $cache = $memory->get("jomcraftapi.$productName.endpoint.$request");

    if ($cache) {
        http_response_code(200);
        echo $cache;
    } else if ($request == "FORGE" || $request == "FABRIC" || $request == "QUILT") {
        $conn = mysqli_connect("localhost", "JomcraftAPI", ini_get("mysqli.default_pw"), "JomcraftAPI");
        $stmt = mysqli_prepare($conn, "SELECT * FROM Product_Info WHERE NAME = ?;");
        mysqli_stmt_bind_param($stmt, "s", $productName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($product[$request]) {
            try {
                $version = json_decode($product[$request]);
                $versionV = ucfirst(strtolower($request));
                $arr = array('subject' => "Latest $versionV", 'status' => $version->latest, 'color' => '8cba05');
                $returnJSON = json_encode($arr);
                $memory->set("jomcraftapi.$productName.endpoint.$request", $returnJSON, time() + 7200);

                http_response_code(200);
                echo $returnJSON;
                mysqli_free_result($result);
                mysqli_close($conn);
                return;
            } catch (Exception $e) {
                http_response_code(503);
                mysqli_free_result($result);
                mysqli_close($conn);
                return;
            }
        }
        http_response_code(503);
        mysqli_free_result($result);
        mysqli_close($conn);
        return;
    }
}