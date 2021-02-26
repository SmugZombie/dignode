<?php
$time = -microtime(true);
header("Access-Control-Allow-Origin: *");

if(isset($_GET['path'])){ $path = $_GET['path']; }else{ $path = ""; }
$path = explode("/", $path);
$method = $path[0];
if(isset($path[1])){ $action = $path[1]; }else{ $action = ""; }
if(isset($path[2])){ $option1 = $path[2]; }else{ $option1 = ""; }
if(isset($path[3])){ $option2 = $path[3]; }else{ $option2 = ""; }

//echo json_encode($path);

$response = [];
$response['code'] = 0;
$response['message'] = "";
$response['ip'] = "";

switch(strtolower($method)){
    case "ip":
        $response['code'] = 200;
        $response['message'] = "success";
        $response['data']['records'][0]['ip'] = gethostbyname($path[1]);
        $response['domain'] = $path[1];
        break;
    case "dig":
        $domain = $path[1].".";
        $response['domain'] = $path[1];
        $resultsoa = dns_get_record($domain, DNS_SOA); $resultns = dns_get_record($domain, DNS_NS);
        $resulta = dns_get_record($domain, DNS_A); $resultmx = dns_get_record($domain, DNS_MX);
        $resultaaaa = dns_get_record($domain, DNS_AAAA); $resulttxt = dns_get_record($domain, DNS_TXT);
        $resultsrv = dns_get_record($domain, DNS_SRV); $resultcname = dns_get_record($domain, DNS_CNAME);
        $host = 0;
        getResults($resulta);
        getResults($resultsoa);
        getResults($resultns);
        getResults($resultmx);
        getResults($resultaaaa);
        getResults($resulttxt);
        getResults($resultsrv);
        getResults($resultcname);
        break;
    case "propagation":
        $response['ip'] = gethostbyname($path[1]);
        $response['http_status'] = getHTTPStatus($path[1], false);
        $response['https_status'] = getHTTPStatus($path[1], true);
        break;
    default:
        $response['code'] = 404;
        $response['message'] = "Endpoint Unknown";
        
}

generateResponse();

function generateResponse(){
    global $response, $time, $method;

    $time += microtime(true); $time = round($time, 5);

    $response['time_took'] = $time;
    $response['method'] = $method;
    $response['resolvers'] = getResolv();

    echo json_encode($response);

}

function getResults($result){
    global $host, $output, $response;
    $i = 0; $max = count($result);
    while($i < $max)
    {
            $get_host = $result[$i]['host'];
            $get_type = $result[$i]['type'];
            $get_target = $result[$i]['target'];
            $get_ipv4 = $result[$i]['ip'];
            $get_ipv6 = $result[$i]['ipv6'];
            $get_class = $result[$i]['class'];
            $get_ttl = $result[$i]['ttl'];
            $get_mname = $result[$i]['mname'];
            $get_rname = $result[$i]['rname'];
            $get_serial = $result[$i]['serial'];
            $get_refresh = $result[$i]['refresh'];
            $get_retry = $result[$i]['retry'];
            $get_expires = $result[$i]['expires'];
            $get_txt = $result[$i]['txt'];

            if($get_type != ""){
                    $record = count($response['data']['records']);
                    $response['data']['records'][$record]['type'] = $get_type;
                    $response['data']['records'][$record]['host'] = $get_host;
                    $response['data']['records'][$record]['target'] = $get_target;
                    $response['data']['records'][$record]['ipv4'] = $get_ipv4;
                    $response['data']['records'][$record]['ipv6'] = $get_ipv6;
                    $response['data']['records'][$record]['class'] = $get_class;
                    $response['data']['records'][$record]['ttl'] = $get_ttl;
                    $response['data']['records'][$record]['mname'] = $get_mname;
                    $response['data']['records'][$record]['rname'] = $get_rname;
                    $response['data']['records'][$record]['serial'] = $get_serial;
                    $response['data']['records'][$record]['refresh'] = $get_refresh;
                    $response['data']['records'][$record]['retry'] = $get_retry;
                    $response['data']['records'][$record]['expires'] = $get_expires;
                    $response['data']['records'][$record]['txt'] = $get_txt;
            }

            $i++;
            if($get_host != "" && $get_host != "."){$host ++;}
    }
}

function getResolv(){
    //$resolve = file_get_contents("/etc/resolv.conf");
    $nameservers = [];
    $file = fopen("/etc/resolv.conf","r");
    while(! feof($file))
    {
        $line = fgets($file);
        if (strpos($line, 'nameserver') !== false) {
            $nameservers[count($nameservers)] = str_replace(PHP_EOL, '', str_replace("nameserver ", "", $line));
        }
    }
    return $nameservers;
}

function getHTTPStatus($domain, $secure){
    /*$ch = curl_init($domain);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $content = curl_exec($ch);*/
    if($secure){
        return get_headers('https://'.$domain)[0];
    }else{
        return get_headers('http://'.$domain)[0];
    }   
}