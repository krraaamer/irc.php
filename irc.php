<?php

// let's not get too crazy
error_reporting(E_ALL ^ E_WARNING);
date_default_timezone_set("America/Los_Angeles");
header("Content-Type: text/plain");

// znc connection information
$config = array(
  "server" => "znc",
  "port"   => 6667,
  "user"   => "mybotname",
  "pass"   => "PakA9XKKSxL6e36G",
);

// each key should correspond to a network/channel array
$keys = array(
  "ed2809efc1ce" => array("local","#home"), // used for camera alerts
  "8d666f684bbf" => array("local","#plex"), // used for sonarr/radarr
  "a630af9bbd2c" => array("local","#voicemail"), // used for phone recordings
  "be3690bbe786" => array("local","#status"), // used for server monitoring
  "323f2ce184c7" => array("othernetwork","#chat"), // example 2nd net/chan
);

// NO NEED TO EDIT ANYTHING BEYOND THIS POINT

function send($key,$msg){

  global $config;
  global $keys;

  $socket = @fsockopen($config["server"], $config["port"]);
  if(!$socket){ return "NO_SOCKET"; }
  socket_set_timeout($socket, 30);

  fputs($socket, sprintf("PASS %s\n", $config["user"] . "/" . $keys[$key][0] . ":" . $config["pass"]));
  fputs($socket, sprintf("USER %s * * :%s\n", $config["user"], $config["user"]));
  fputs($socket, sprintf("NICK %s\n", $config["user"]));
  fputs($socket, sprintf("PRIVMSG %s :%s \n", $keys[$key][1], $msg));
  fputs($socket, sprintf("QUIT\n"));

}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$key = $input["key"];
$msg = $input["msg"];

if(!$keys[$key]){ echo("bad key or bad json"); exit; }
send($key,$msg);

?>
