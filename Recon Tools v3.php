<?php
// Ensure errors are displayed for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define a function to execute system commands
function runCommand($command) {
    $output = shell_exec($command);
    return htmlspecialchars($output);
}

// Handle form submissions
$choice = isset($_POST['choice']) ? $_POST['choice'] : '';
$target = isset($_POST['target']) ? escapeshellarg($_POST['target']) : '';

$result = '';

switch ($choice) {
    case '1':
        // Network Scanning Options
        if (isset($_POST['scan'])) {
            $scanType = $_POST['scan'];
            switch ($scanType) {
                case 'dnslookup':
                    $result = runCommand("nslookup $target && nslookup -type=txt $target");
                    break;
                case 'zonetransfer':
                    $result = runCommand("nslookup -type=any $target");
                    break;
                case 'portscan':
                    $result = runCommand("nmap $target");
                    break;
                case 'httpheader':
                    $result = runCommand("curl -I $target");
                    break;
                case 'honeypot':
                    $result = runCommand("nmap -sV --script=http-enum $target");
                    break;
                case 'robotstxt':
                    $result = runCommand("curl $target/robots.txt");
                    break;
                case 'linkgrabber':
                    $result = runCommand("curl -s $target | grep 'href='");
                    break;
                case 'traceroute':
                    $result = runCommand("tracert $target");
                    break;
                case 'grabbanners':
                    $result = runCommand("nmap -sV $target");
                    break;
                case 'subnetcalc':
                    $result = runCommand("nmap -sL $target");
                    break;
                case 'subdomainscanner':
                    $result = runCommand("nslookup -type=ns $target");
                    break;
                case 'sqliscanner':
                    $result = runCommand("sqlmap -u $target --batch --level=5 --risk=3");
                    break;
                case 'bloggersview':
                    $result = runCommand("curl -I $target && curl -s $target | grep -i '<title>' && curl http://data.alexa.com/data?cli=10&dat=s&url=$target | grep '<REACH RANK=' && curl -H 'Content-Type: application/json' -d '{\"site\": \"$target\"}' https://api.moz.com/v2/metrics");
                    break;
                case 'wordpressscan':
                    $result = runCommand("wpscan --url $target --enumerate vp && wpscan --url $target --detect-version && wpscan --url $target --enumerate vp --plugins-detection aggressive");
                    break;
                case 'crawler':
                    $result = runCommand("curl $target");
                    break;
                case 'mxlookup':
                    $result = runCommand("nslookup -type=mx $target");
                    break;
                case 'scanall':
                    $result = runCommand("whois $target && nslookup $target && nslookup -type=txt $target && nslookup -type=any $target && nmap $target && curl -I $target && nmap -sV --script=http-enum $target && curl $target/robots.txt && curl -s $target | grep 'href=' && curl http://ipinfo.io/$target && tracert $target && nmap -sV $target && nmap -sL $target && nslookup -type=ns $target && sqlmap -u $target --batch --level=5 --risk=3 && curl -I $target && curl -s $target | grep -i '<title>' && curl http://data.alexa.com/data?cli=10&dat=s&url=$target | grep '<REACH RANK=' && curl -H 'Content-Type: application/json' -d '{\"site\": \"$target\"}' https://api.moz.com/v2/metrics && curl -H 'Content-Type: application/json' -d '{\"site\": \"$target\"}' https://api.moz.com/v2/metrics && curl -s $target | grep -i 'facebook.com\|twitter.com\|linkedin.com' && wpscan --url $target --enumerate vp && wpscan --url $target --detect-version && wpscan --url $target --enumerate vp --plugins-detection aggressive && curl $target && nslookup -type=mx $target");
                    break;
            }
        }
        break;
    case '2':
        // Lookup Options
        if (isset($_POST['lookup'])) {
            $lookupType = $_POST['lookup'];
            switch ($lookupType) {
                case 'whois':
                    $result = runCommand("whois $target");
                    break;
                case 'iplocator':
                    $result = runCommand("curl http://ipinfo.io/$target");
                    break;
            }
        }
        break;
    case '3':
        // Local Network Options
        if (isset($_POST['local'])) {
            $result = runCommand("nmap -sn 192.168.1.0/24");
        }
        break;
    case '4':
        exit();
        break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recon Tools</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recon Tools V3</h1>

        <?php if ($choice == ''): ?>
            <form method="post">
                <label for="choice">Enter your choice:</label>
                <select name="choice" id="choice">
                    <option value="1">Network Scanning Options</option>
                    <option value="2">Lookup Options</option>
                    <option value="3">Local Network Options</option>
                    <option value="4">Exit</option>
                </select>
                <input type="submit" value="Submit">
            </form>
        <?php elseif ($choice == '1'): ?>
            <h2>Network Scanning Options</h2>
            <form method="post">
                <label for="target">Enter the target for scanning:</label>
                <input type="text" id="target" name="target">
                <input type="hidden" name="choice" value="1">
                <select name="scan">
                    <option value="dnslookup">DNS Lookup + Cloudflare Detector</option>
                    <option value="zonetransfer">Zone Transfer</option>
                    <option value="portscan">Port Scan</option>
                    <option value="httpheader">HTTP Header Grabber</option>
                    <option value="honeypot">Honeypot Detector</option>
                    <option value="robotstxt">Robots.txt Scanner</option>
                    <option value="linkgrabber">Link Grabber</option>
                    <option value="traceroute">Traceroute</option>
                    <option value="grabbanners">Grab Banners</option>
                    <option value="subnetcalc">Subnet Calculator</option>
                    <option value="subdomainscanner">Sub-Domain Scanner</option>
                    <option value="sqliscanner">Error Based SQLi Scanner</option>
                    <option value="bloggersview">Bloggers View</option>
                    <option value="wordpressscan">Wordpress Scan</option>
                    <option value="crawler">Crawler</option>
                    <option value="mxlookup">MX Lookup</option>
                    <option value="scanall">Scan All</option>
                </select>
                <input type="submit" value="Run Scan">
            </form>
            <?php if (isset($result)): ?>
                <h3>Scan Results:</h3>
                <pre><?php echo $result; ?></pre>
            <?php endif; ?>
        <?php elseif ($choice == '2'): ?>
            <h2>Lookup Options</h2>
            <form method="post">
                <label for="target">Enter the target for lookup:</label>
                <input type="text" id="target" name="target">
                <input type="hidden" name="choice" value="2">
                <select name="lookup">
                    <option value="whois">WHOIS Lookup</option>
                    <option value="iplocator">IP Location Finder</option>
                </select>
                <input type="submit" value="Run Lookup">
            </form>
            <?php if (isset($result)): ?>
                <h3>Lookup Results:</h3>
                <pre><?php echo $result; ?></pre>
            <?php endif; ?>
        <?php elseif ($choice == '3'): ?>
            <h2>Local Network Options</h2>
            <form method="post">
                <input type="hidden" name="choice" value="3">
                <input type="submit" name="local" value="Run Local Scan">
            </form>
            <?php if (isset($result)): ?>
                <h3>Local Scan Results:</h3>
                <pre><?php echo $result; ?></pre>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
