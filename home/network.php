<!DOCTYPE html>
<html lang="en">
<head>
    <title>Network Map - Equestria.dev Intranet Home Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/styles.css">
</head>

<body>
<nav>
    <a href="/">Home</a> -
    <a href="/network.php">Network Map</a> -
    <a href="/setup.html">Setup Instructions</a> -
    <a href="/internet.html">Internet Access</a>
</nav>
<article>
    <?php $nmap = json_decode(file_get_contents("./output.json"), true)["nmaprun"]; ?>
    <h1>Network Map</h1>
    <blockquote style="margin-bottom: 1rem;"><b>Note:</b> Due to the relatively high cost needed to upgrade this network map, it is not updated in real time. Instead, updates are made every 30 minutes.</blockquote>

    <!--<details>
        <pre><?php //var_dump($nmap); ?></pre>
    </details>-->

    <table>
        <thead>
        <tr>
            <th>IP address</th>
            <th>Latency</th>
            <th>MAC</th>
            <th>Vendor</th>
            <th>Domain name</th>
        </tr>
        </thead>
        <tbody>
            <?php uasort($nmap["host"], function ($a, $b) {
                $ip1 = (int)(explode(".", $a["address"]["@addr"] ?? $a["address"][0]["@addr"])[0]);
                $ip2 = (int)(explode(".", $b["address"]["@addr"] ?? $b["address"][0]["@addr"])[0]);

                return $ip1 - $ip2;
            }); foreach ($nmap["host"] as $host):

            $dn = "";
            $ip = $host["address"]["@addr"] ?? $host["address"][0]["@addr"];

            if (str_starts_with($ip, "10.0.")) {
                $addr = implode(".", array_reverse(explode(".", $ip))) . ".in-addr.arpa";
                $dns = exec("dig +short $addr @10.0.1.1 PTR");
                if (trim($dns) !== "") $dn = $dns;
            }

            ?>
            <tr>
                <td><?= $host["address"]["@addr"] ?? $host["address"][0]["@addr"] ?></td>
                <td><?= isset($host["times"]) ? round($host["times"]["@srtt"] / 1000, 1) . " ms" : "" ?></td>
                <td><?= isset($host["address"][1]) ? $host["address"][1]["@addr"] : "" ?></td>
                <td><?= isset($host["address"][1]) && isset($host["address"][1]["@vendor"]) ? $host["address"][1]["@vendor"] : "" ?></td>
                <td><?= $dn ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</article>
<footer>
    <div>© Equestria.dev - <a href="https://intra.w.corp/">intra.w.corp</a></div>
</footer>
</body>
</html>
