<?php
header("Content-Type:application/json");
$product_name = "noitemdespawn";
$memory = new Memcached();
$memory->addServer("127.0.0.1", 11211);

if (isset($_GET['versions']) && $_GET['versions'] == "") {
    http_response_code(503);

    $cache = $memory->get("jomcraftapi.$product_name.versions");

    if ($cache) {
        http_response_code(200);
        echo $cache;
    } else {
        $conn = mysqli_connect("localhost", "JomcraftAPI", ini_get("mysqli.default_pw"), "JomcraftAPI");
        $stmt = mysqli_prepare($conn, "SELECT * FROM Product_Info WHERE NAME = ?;");
        mysqli_stmt_bind_param($stmt, "s", $product_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $forge = json_decode($product['FORGE']);

        $fabric = json_decode($product['FABRIC']);

        $quilt = json_decode($product['QUILT']);

        mysqli_free_result($result);
        mysqli_close($conn);

        $arr = array('product' => $product_name, 'versions' => array(
            "forge" => $forge,
            "fabric" => $fabric,
            "quilt" => $quilt
        ));

        $js = json_encode($arr);

        $memory->set("jomcraftapi.$product_name.versions", $js, time() + 7200);

        http_response_code(200);

        echo $js;
    }
} else if (isset($_GET['latest']) && $_GET['latest'] != "") {
    $request = strtoupper($_GET['latest']);
    $cache = $memory->get("jomcraftapi.$product_name.latest.$request");

    if ($cache) {
        http_response_code(200);
        echo $cache;
    } else if ($request == "FORGE" || $request == "FABRIC" || $request == "QUILT") {
        $conn = mysqli_connect("localhost", "JomcraftAPI", ini_get("mysqli.default_pw"), "JomcraftAPI");
        $stmt = mysqli_prepare($conn, "SELECT * FROM Product_Info WHERE NAME = ?;");
        mysqli_stmt_bind_param($stmt, "s", $product_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($product[$request]) {
            try {
                $version = json_decode($product[$request]);
                $arr = array('version' => $version->latest);
                $js = json_encode($arr);

                $memory->set("jomcraftapi.$product_name.latest.$request", $js, time() + 7200);

                $json_response = $js;

                http_response_code(200);
                echo $json_response;
            } catch (Exception $e) {
                http_response_code(503);
            }
        } else {
            http_response_code(503);
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
} else if (isset($_GET['endpoint']) && $_GET['endpoint'] != "") {
    $request = strtoupper($_GET['endpoint']);
    $cache = $memory->get("jomcraftapi.$product_name.endpoint.$request");

    if ($cache) {
        http_response_code(200);
        echo $cache;
    } else if ($request == "FORGE" || $request == "FABRIC" || $request == "QUILT") {
        $conn = mysqli_connect("localhost", "JomcraftAPI", ini_get("mysqli.default_pw"), "JomcraftAPI");
        $stmt = mysqli_prepare($conn, "SELECT * FROM Product_Info WHERE NAME = ?;");
        mysqli_stmt_bind_param($stmt, "s", $product_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($product[$request]) {
            try {
                $version = json_decode($product[$request]);
                $versionV = ucfirst(strtolower($request));
                $arr = array('subject' => "Latest $versionV", 'status' => $version->latest, 'color' => '8cba05');
                $js = json_encode($arr);

                $memory->set("jomcraftapi.$product_name.endpoint.$request", $js, time() + 7200);

                $json_response = $js;

                http_response_code(200);
                echo $json_response;
            } catch (Exception $e) {
                http_response_code(503);
            }
        } else {
            http_response_code(503);
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
} else {
    http_response_code(400);
}
